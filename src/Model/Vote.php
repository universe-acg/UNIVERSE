<?php
namespace App\Model;
use Doctrine\ORM\Mapping as ORM;
#[ORM\MappedSuperclass]
abstract class Vote
{
    #[ORM\Column(type: 'integer')] protected $value = 1;
    #[ORM\Column(type: 'datetime')] protected $createdAt;
    public function __construct() { $this->createdAt = new \DateTime(); }
    public function getValue() { return $this->value; }
    public function setValue($value) { $this->value = $value; return $this; }
    public function getCreatedAt() { return $this->createdAt; }
}
