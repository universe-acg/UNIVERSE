<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/** Stable ownership registry; demo seeding never purges unrelated records. */
#[ORM\Entity]
#[ORM\Table(name: 'universe_demo_record')]
class DemoRecord
{
    #[ORM\Id, ORM\Column(length: 180)] public string $recordKey;
    #[ORM\Column(length: 255)] public string $entityClass;
    #[ORM\Column(length: 180)] public string $entityId;
    #[ORM\Column(type: 'json')] public array $provenance = [];
    public function __construct(string $key, string $class, string $id, array $provenance = []) { $this->recordKey=$key; $this->entityClass=$class; $this->entityId=$id; $this->provenance=$provenance; }
}
