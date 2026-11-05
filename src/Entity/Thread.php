<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Thread as BaseThread;

#[ORM\Table(name: 'universe_thread')]
#[ORM\Entity(repositoryClass: \App\Repository\ThreadRepository::class)]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Thread extends BaseThread
{

    /**
     * @var string $id
     */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 180)]
    protected $id;

    public function getId()
    {
        return $this->id;
    }

}
