<?php
namespace AppBundle\Repository;

use AppBundle\Entity\TeaFeatured;
use Doctrine\ORM\EntityRepository;
use Exception;

class TeaFeaturedRepository extends EntityRepository
{
    /**
     * Gets today's featured tea
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCurrentTea($user)
    {
        $currentTea = $this->getEntityManager()
            ->createQuery('
            SELECT tf.id AS id, t.id AS tea_id, (tf.nextTea) AS next_id, (tf.previousTea) AS previous_id
            FROM AppBundle:TeaFeatured tf
            INNER JOIN AppBundle:Tea t WITH tf.tea = t.id
            WHERE tf.day = :currentDate
            AND t.published = true'
            )
            ->setParameters(['currentDate' => date('Y-m-d', time())])
            ->getOneOrNullResult();

        /** TeaFeatured $lastFeatured */
        if (!$currentTea){
            if ($lastFeatured = $this->getLastFeaturedTea()) {
                $currentTea = $this->getTeaById($lastFeatured->getId(), $user);
            }
        } else {
            // getTeaById in main if statement does type casting inside
            $currentTea = $this->castTypes($this->getTeaDetails($currentTea, $user));
        }

        return $currentTea;
    }

    /**
     * Gets a featured tea by Featured Tea Id
     * @param $featuredTeaId
     * @param $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTeaById($featuredTeaId, $user)
    {
        $tea = $this->getEntityManager()
            ->createQuery('
            SELECT tf.id AS id, t.id AS tea_id, (tf.nextTea) AS next_id, (tf.previousTea) AS previous_id
            FROM AppBundle:TeaFeatured tf
            INNER JOIN AppBundle:Tea t WITH tf.tea = t.id
            WHERE tf.id = :featuredTeaId
            AND t.published = true'
            )
            ->setParameters(['featuredTeaId' => $featuredTeaId])
            ->getOneOrNullResult();

        if ($tea) {
            return $this->castTypes($this->getTeaDetails($tea, $user));
        }

        return null;
    }

    /**
     * Gets latest featured tea model
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastFeaturedTea() : ?TeaFeatured
    {
        $lastFeaturedTea = $this->getEntityManager()
            ->createQuery('
                SELECT tf
                FROM AppBundle:TeaFeatured tf
                INNER JOIN AppBundle:Tea t WITH tf.tea = t.id
                WHERE t.published = true
                ORDER BY tf.id DESC'
            )
            ->setMaxResults(1)
            ->getOneOrNullResult();
        return $lastFeaturedTea;
    }

    /**
     * Gets a list of teas that were featured in the ($count) last days
     * @param $count
     * @return mixed
     */
    public function getRecentlyFeatured(int $count)
    {
        $teaList = $this->getEntityManager()
            ->createQuery('
            SELECT DISTINCT (t.id) AS id
            FROM AppBundle:TeaFeatured tf
            INNER JOIN AppBundle:Tea t WITH tf.tea = t.id
            ORDER BY tf.id DESC'
            )
            ->setMaxResults($count)
            ->getArrayResult();

        $teaList = array_column($teaList, 'id');

        return $teaList;
    }



    /**
     *  Casting types of some variables to adhere to REST principles
     */
    private function castTypes($tea)
    {
        //cast types
        if (!empty($tea)) {
            if (array_key_exists('next_id', $tea) && $tea['next_id'] != null)
                $tea['newsfeed']['next_id'] = intval($tea['next_id']);
            if (array_key_exists('previous_id', $tea) && $tea['previous_id'] != null)
                $tea['newsfeed']['previous_id'] = intval($tea['previous_id']);
            $tea['newsfeed']['id'] = $tea['id'];

            unset ($tea['next_id'], $tea['previous_id'], $tea['id']);
        }
        return $tea;
    }

    private function getTeaDetails($currentTea, $user) {
        $currentTea = array_merge($currentTea, $this->getEntityManager()->getRepository('AppBundle:Tea')->displayTeaById($currentTea['tea_id'], isset($user) ? $user->getId() : null));
        unset($currentTea['tea_id']);
        return $currentTea;
    }

    public function deleteTeaFromFeaturedTeas($teaId) {
        /** @var TeaFeatured $tea */
        while ($tea = $this->findOneBy(['tea' => $teaId])) {
            /** @var TeaFeatured $nextTea */
            $nextTea = $tea->getNextTea();
            /** @var TeaFeatured $previousTea */
            $previousTea = $tea->getPreviousTea();
            $tea->setPreviousTea(null);
            $tea->setNextTea(null);
            $this->getEntityManager()->persist($tea);
            $this->getEntityManager()->flush();

            if ($nextTea && $previousTea) {
                $nextTea->setPreviousTea($previousTea);
                $previousTea->setNextTea($nextTea);
            } else if ($nextTea && !$previousTea) {
                $nextTea->setPreviousTea(null);
            } else if (!$nextTea && $previousTea) {
                $previousTea->setNextTea(null);
            }

            $this->getEntityManager()->remove($tea);
            $this->getEntityManager()->flush();
        }
    }

}