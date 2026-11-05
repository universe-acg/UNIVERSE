<?php
namespace App\Command;

use App\Entity as E;
use App\Service\DemoImageInstaller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:demo:repair-images', description: 'Restore demo-owned media without reseeding or overwriting user uploads.')]
final class RepairDemoImagesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private DemoImageInstaller $images,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        #[Autowire('%env(bool:APP_DEMO)%')] private bool $demo) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->demo) { $output->writeln('Skipped: demo mode is disabled.'); return self::SUCCESS; }
        $this->images->installAllAssets();
        // Restore the original fixture filename in every shared upload location.
        $source = $this->projectDir.'/public/uploads/landing/Deree-44.jpg';
        if (!is_file($source)) $source = $this->images->resolveVenue('Deree Student Lounge');
        if (!$source || !is_file($source)) throw new \RuntimeException('The bundled campus fallback photo is missing.');
        foreach (['events','venues','facilities','listings','blog','categories','layout','organizers','organizers/covers','users/avatars','homepage/hero'] as $directory) {
            $target = $this->projectDir.'/public/uploads/'.$directory.'/campus-demo.jpg';
            $legacy = $this->projectDir.'/var/private/demo/campus-demo.jpg';
            $legacyPlaceholder = is_file($target) && is_file($legacy) && hash_file('sha256', $target) === hash_file('sha256', $legacy);
            if (!is_file($target) || $legacyPlaceholder) { if (!is_dir(dirname($target))) mkdir(dirname($target), 0755, true); if (!copy($source, $target)) throw new \RuntimeException('Cannot restore '.$directory.' media.'); }
        }
        $count = 0; $owners = []; $counts = [];
        $classes = [E\Event::class, E\Venue::class, E\FacilityEntity::class, E\Organizer::class];
        foreach ($classes as $class) {
            $meta = $this->em->getClassMetadata($class);
            foreach ($this->em->getRepository(E\DemoRecord::class)->findBy(['entityClass' => $class]) as $record) {
                $object = $this->em->find($class, $record->entityId);
                if (!$object) continue;
                $this->em->initializeObject($object);
                $field = $object instanceof E\Organizer ? 'logoName' : 'imageName';
                $current = $meta->getFieldValue($object, $field);
                // Never replace a custom upload, including a custom missing file.
                if ($current && $current !== 'campus-demo.jpg') { $owners[$class][$record->entityId] = $current; continue; }
                $resolved = match ($class) {
                    E\Organizer::class => $this->images->resolveClub($object->getName()),
                    E\Venue::class => $this->images->resolveVenue($object->getTitle()),
                    E\FacilityEntity::class => $this->images->resolveFacility($object->getName()),
                    default => $this->images->resolveEventCategory($object->getCategory()?->getName() ?? 'Campus community', (int)$object->getId()),
                };
                if ($resolved) {
                    $name = basename($resolved); $meta->setFieldValue($object, $field, $name);
                    $prefix = $object instanceof E\Organizer ? 'logo' : 'image';
                    $info = getimagesize($resolved);
                    foreach (['Size' => filesize($resolved), 'MimeType' => $info['mime'], 'Dimensions' => [$info[0], $info[1]], 'OriginalName' => basename($resolved)] as $suffix => $value) {
                        if ($meta->hasField($prefix.$suffix)) $meta->setFieldValue($object, $prefix.$suffix, $value);
                    }
                    $owners[$class][$record->entityId] = $name; ++$count; $counts[$class] = ($counts[$class] ?? 0) + 1;
                }
            }
        }
        foreach ([E\EventImage::class => [E\Event::class, 'getEvent'], E\VenueImage::class => [E\Venue::class, 'getVenue'], E\FacilityEntityImage::class => [E\FacilityEntity::class, 'getFacility']] as $class => [$ownerClass, $getter]) {
            foreach ($this->em->getRepository(E\DemoRecord::class)->findBy(['entityClass' => $class]) as $record) {
                $image = $this->em->find($class, $record->entityId);
                if (!$image || $image->getImageName() !== 'campus-demo.jpg') continue;
                $name = $owners[$ownerClass][$image->$getter()->getId()] ?? null;
                if (!$name || $name === $image->getImageName()) continue;
                $meta = $this->em->getClassMetadata($class); $meta->setFieldValue($image, 'imageName', $name); ++$count; $counts[$class] = ($counts[$class] ?? 0) + 1;
            }
        }
        $this->em->flush();
        foreach ($counts as $class => $changes) $output->writeln($class.': '.$changes.' references repaired.');
        $output->writeln(sprintf('Demo media restored; %d placeholder references upgraded. Existing custom uploads preserved.', $count));
        return self::SUCCESS;
    }
}
