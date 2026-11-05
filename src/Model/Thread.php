<?php
namespace App\Model;
use Doctrine\ORM\Mapping as ORM;
#[ORM\MappedSuperclass]
abstract class Thread
{
    #[ORM\Column(type: 'string', length: 1024)] protected $permalink = '';
    #[ORM\Column(type: 'boolean')] protected $isCommentable = true;
    #[ORM\Column(type: 'integer')] protected $numComments = 0;
    public function setId($id) { $this->id = $id; return $this; }
    public function getPermalink() { return $this->permalink; }
    public function setPermalink($value) { $this->permalink = $value; return $this; }
    public function isCommentable() { return $this->isCommentable; }
    public function setIsCommentable($value) { $this->isCommentable = $value; return $this; }
    public function getNumComments() { return $this->numComments; }
    public function setNumComments($value) { $this->numComments = $value; return $this; }
}
