<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 12/24/2016
 * Time: 6:52 PM
 */

namespace AppBundle\Services\TeaFeature;


use AppBundle\Entity\Tea;
use AppBundle\Entity\TeaFeatured;
use AppBundle\Repository\TeaFeaturedRepository;
use AppBundle\Repository\TeaRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use DoctrineExtensions\Query\Mysql\Date;

class FeatureRandomTea
{
    private $teaRepository;
    private $teaFeaturedRepository;
    private $entityManager;

    private $featuredTeaCount;

    public function __construct(TeaRepository $teaRepository, TeaFeaturedRepository $teaFeaturedRepository, EntityManager $entityManager, $featuredTeaCount)
    {
        $this->teaRepository = $teaRepository;
        $this->teaFeaturedRepository = $teaFeaturedRepository;
        $this->entityManager = $entityManager;

        $this->featuredTeaCount = $featuredTeaCount;
    }

    public function featureTea()
    {
        /** @var TeaFeatured $lastFeatured */
        $lastFeatured = $this->teaFeaturedRepository->getLastFeaturedTea();

        if(!$lastFeatured) {
            $lastFeatured = $this->featureTeaToday();
        }

        $lastFeaturedDay = date_format($lastFeatured->getDay(), 'Ymd');
        $today = date_format(new DateTime(), 'Ymd');

        while ($lastFeaturedDay != $today) {
            $recentTeas = $this->teaFeaturedRepository->getRecentlyFeatured($this->featuredTeaCount);
            $teaToFeature = $this->teaRepository->getTeaToFeature($recentTeas);
            //todo +1 day is not added
            $date = $lastFeatured->getDay();
            $date->modify('+1 day');

            $teaFeatured = new TeaFeatured();
            $teaFeatured->setDay($date);
            $teaFeatured->setPreviousTea($lastFeatured);
            $teaFeatured->setTea($teaToFeature);

            $this->entityManager->persist($teaFeatured);
            $this->entityManager->flush();

            $lastFeatured->setNextTea($teaFeatured);
            $this->entityManager->persist($lastFeatured);
            $this->entityManager->flush();

            $lastFeatured = $this->teaFeaturedRepository->getLastFeaturedTea();
            $lastFeaturedDay = date_format($lastFeatured->getDay(), 'Ymd');
        }
    }

    /**
     * If tea_featured table is empty, feature a random tea
     * @return TeaFeatured
     */
    private function featureTeaToday()
    {
        $teaToFeature = $this->teaRepository->getTeaToFeature([]);
        $date = new \DateTime();

        $teaFeatured = new TeaFeatured();
        $teaFeatured->setDay($date);
        $teaFeatured->setTea($teaToFeature);

        $this->entityManager->persist($teaFeatured);
        $this->entityManager->flush();
        return $teaFeatured;
    }
}