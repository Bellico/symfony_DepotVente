<?php

namespace DepotVente\BourseBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BourseRepository extends EntityRepository{

	public function getCurrentBourse(){
		// $query = $this->createQueryBuilder('b')
		// ->orderBy('b.id', 'DESC')
		// ->orderBy('b.dateCreated', 'DESC')
		// ->setMaxResults(1);
		// return $query->getQuery()->getSingleResult();
		return $this->findOneBy(
            array(),
            array('dateCreated' => 'DESC','id'=>'DESC')
        );
	}
}
