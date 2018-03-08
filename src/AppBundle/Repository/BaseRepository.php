<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-02-08
 * Time: 23:44
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

class BaseRepository extends EntityRepository
{
    /**
     * @param string $entityName
     * @param int $id
     * @return null|object
     */

    public function getReference(string $entityName, ?int $id) {
        if ($id == null) return null;
        try {
            return $this->getEntityManager()->getReference("AppBundle:$entityName", $id);
        } catch (ORMException $e) {
            return null;
        }
    }
}