<?php
namespace App\Command;

use App\Entity\{AppLayoutSettings, Settings};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:brand:install', description: 'Install the supplied Universe wordmark, preserving later administrator logo changes.')]
final class InstallBrandCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')] private string $projectDir) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $this->projectDir.'/public/images/brand/universe-wordmark.png';
        $version = hash_file('sha256', $source);
        $filename = 'universe-wordmark-'.substr($version, 0, 12).'.png';
        $directory = $this->projectDir.'/public/uploads/layout';
        if (!is_dir($directory)) mkdir($directory, 0755, true);
        if (!is_file($directory.'/'.$filename) && !copy($source, $directory.'/'.$filename)) throw new \RuntimeException('Could not install the brand wordmark.');
        $settings = $this->em->getRepository(AppLayoutSettings::class)->findOneBy([], ['id' => 'ASC']);
        if (!$settings) throw new \RuntimeException('Brand layout settings are missing.');
        $this->em->initializeObject($settings);
        $marker = $this->em->getRepository(Settings::class)->findOneBy(['key' => 'brand_wordmark_version']);
        if ($marker?->getValue() === $version && $settings->getLogoName() !== 'campus-demo.jpg') {
            $output->writeln('Brand wordmark already installed; administrator changes preserved.'); return self::SUCCESS;
        }
        $info = getimagesize($source);
        $settings->setLogoName($filename);
        $settings->setLogoSize(filesize($source));
        $settings->setLogoMimeType('image/png');
        $settings->setLogoOriginalName('Universe wordmark.png');
        $settings->setLogoDimensions([$info[0], $info[1]]);
        if (!$marker) { $marker = new Settings(); $marker->setKey('brand_wordmark_version'); $this->em->persist($marker); }
        $marker->setValue($version); $this->em->flush();
        $output->writeln('Supplied Universe wordmark installed.');
        return self::SUCCESS;
    }
}
