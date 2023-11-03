<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

use App\Entity\Amenity;
use App\Repository\AmenityRepository;
use Doctrine\ORM\EntityManagerInterface;

class AmenityService
{
    private $entityManager;
    private $amenityRepository;

    public function __construct(EntityManagerInterface $entityManager, AmenityRepository $amenityRepository)
    {
        $this->amenityRepository = $amenityRepository;
        $this->entityManager = $entityManager;
    }

    public function findOneBySlug($slug): ?Amenity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createAmenity($icon, $name, $slug, $hidden)
    {
        $amenity = new Amenity();
        $amenity->setIcon($icon);
        $amenity->setName($name);
        $amenity->setSlug($slug);
        $amenity->setHidden($hidden);

        $this->entityManager->persist($amenity);
        $this->entityManager->flush();
    }

    public function getAmenitiessql($criterias = [])
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->amenityRepository->getAmenitiessql($hidden, $keyword, $slug, $limit);
    }

    public function getAmenities($criterias = [])
    {
        $hidden = array_key_exists('hidden', $criterias) ? $criterias['hidden'] : false;
        $keyword = array_key_exists('keyword', $criterias) ? $criterias['keyword'] : "all";
        $slug = array_key_exists('slug', $criterias) ? $criterias['slug'] : "all";
        $limit = array_key_exists('limit', $criterias) ? $criterias['limit'] : "all";
        $count = array_key_exists('count', $criterias) ? $criterias['count'] : false;

        return $this->amenityRepository->getAmenities($hidden, $keyword, $slug, $limit);
    }

    public function getAmenityBySlug($slug)
    {
        return $this->amenityRepository->findOneBy(['slug' => $slug]);
    }
}