<?php
namespace App\Twig;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
final class CompatibilityExtension extends AbstractExtension
{
    public function __construct(private Security $security, private \App\Security\MutationRoutes $mutations) {}
    public function getFilters(): array { return [new TwigFilter('localizeddate', [$this,'localizedDate']), new TwigFilter('htmlpurifier', [$this,'purify'], ['is_safe'=>['html']])]; }
    public function localizedDate($value, $dateType='medium', $timeType='medium', $locale=null, $timezone='Europe/Athens', $format=null): string {
        if ($value === null) return '';
        $date=$value instanceof \DateTimeInterface ? \DateTimeImmutable::createFromInterface($value) : new \DateTimeImmutable((string)$value);
        return $date->setTimezone(new \DateTimeZone($timezone ?: 'Europe/Athens'))->format($format ?: 'd M Y H:i');
    }
    public function purify(?string $value): string { $config=\HTMLPurifier_Config::createDefault(); $config->set('Cache.DefinitionImpl', null); return (new \HTMLPurifier($config))->purify($value ?? ''); }
    public function getTests(): array { return [new TwigTest('fos_comment_in_state', fn($comment,$state)=>$comment->getState() === (['visible'=>0,'deleted'=>1,'pending'=>2][$state] ?? $state))]; }
    public function getFunctions(): array {
        return [
            new TwigFunction('mutation_paths', [$this->mutations,'paths']),
            new TwigFunction('fos_comment_can_comment_thread', fn($thread)=>$this->security->getUser() !== null && $thread->isCommentable()),
            new TwigFunction('fos_comment_can_comment', fn($comment)=>$this->security->getUser() !== null),
            new TwigFunction('fos_comment_can_vote', fn($comment)=>$this->security->getUser() !== null && $comment->getAuthor() !== $this->security->getUser()),
            new TwigFunction('fos_comment_can_edit_comment', fn($comment)=>$this->security->isGranted('ROLE_ADMINISTRATOR') || ($this->security->getUser() !== null && $comment->getAuthor() === $this->security->getUser())),
            new TwigFunction('fos_comment_can_edit_thread', fn($thread)=>$this->security->isGranted('ROLE_ADMINISTRATOR')),
        ];
    }
}
