<?php

namespace JourEtMenu\JourEtMenuBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ReservationsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservationsRepository extends EntityRepository
{
	
	public function byClient($utilisateur) {
		$qb = $this->createQueryBuilder('u')
		->select('u')
		->where('u.client = :utilisateur')
		->orderBy('u.id')
		->setParameter('utilisateur', $utilisateur);
		return $qb->getQuery()->getResult();
	}
	
	
	public function byRestaurant($utilisateur) {
		$qb = $this->createQueryBuilder('u')
		->select('u')
		->where('u.restaurant = :utilisateur')
		->orderBy('u.id')
		->setParameter('utilisateur', $utilisateur);
		return $qb->getQuery()->getResult();
	}
}
