<?php
namespace App\Model;
use Doctrine\ORM\Mapping as ORM;
#[ORM\MappedSuperclass]
abstract class Comment
{
    #[ORM\Column(type: 'text')] protected $body = '';
    #[ORM\Column(type: 'datetime')] protected $createdAt;
    #[ORM\Column(type: 'integer')] protected $state = 0;
    public function __construct() { $this->createdAt = new \DateTime(); }
    public function getBody() { return $this->body; }
    public function setBody($body) { $this->body = $body; return $this; }
    public function getCreatedAt() { return $this->createdAt; }
    public function setCreatedAt($date) { $this->createdAt = $date; return $this; }
    public function getState() { return $this->state; }
    public function setState($state) { $this->state = $state; return $this; }
}
