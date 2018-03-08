<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-10-20
 * Time: 14:08
 */

namespace AppBundle\Services;


use AppBundle\Entity\Communication\UserGroup;
use Doctrine\ORM\EntityManager;

class UserGroupSetup
{
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setUp() {
        $count = $this->entityManager->getRepository('AppBundle:Communication\UserGroup')->createQueryBuilder('ug')
            ->select('count(ug.id)')
            ->getQuery()
            ->getSingleScalarResult();
        if ($count != 0) return 'user groups are already setup. No changes done';

        $userGroups = [
            [
                'title' => 'All',
                'enabled' => false,
                'pushNotificationSendFrequencyDays' => 0
            ],
            [
                'title' => 'Active',
                'enabled' => false,
                'pushNotificationSendFrequencyDays' => 0
            ],
            [
                'title' => 'Inactive',
                'enabled' => false,
                'pushNotificationSendFrequencyDays' => 0
            ],
            [
                'title' => 'Engaged',
                'enabled' => false,
                'pushNotificationSendFrequencyDays' => 0
            ],
        ];

        foreach ($userGroups as $sequenceNo => $userGroup) {
            $nug = new UserGroup();
            $nug->setTitle($userGroup['title']);
            $nug->setEnabled($userGroup['enabled']);
            $nug->setPushNotificationSendFrequencyDays($userGroup['pushNotificationSendFrequencyDays']);
            $this->entityManager->persist($nug);
            $this->entityManager->flush();
        }
        return "user groups set up successfully.";
    }

}