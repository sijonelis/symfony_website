<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-04-25
 * Time: 23:02
 */

namespace AppBundle\Repository;


use AppBundle\Entity\TeaFavourite;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;

class TeaFavouriteRepository extends EntityRepository
{
    public function getFavouriteTea($user, $tea) {
        return $this->getEntityManager()->getRepository('AppBundle:TeaFavourite')->findOneBy(['tea' => $tea, 'user' => $user]);
    }

    public function putFavouriteTea($user, $tea) {
        /** @var TeaFavourite $tf */
        $tf = new TeaFavourite();
        $tf->setUser($user);
        $tf->setTea($tea);


        $this->getEntityManager()->persist($tf);
        $this->getEntityManager()->flush();
    }

    public function deleteFavouriteTea($favTea) {

        $this->getEntityManager()->remove($favTea);
        $this->getEntityManager()->flush();
    }
}