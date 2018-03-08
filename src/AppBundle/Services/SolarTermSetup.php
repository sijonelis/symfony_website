<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-07-21
 * Time: 20:51
 */

namespace AppBundle\Services;


use AppBundle\Entity\SolarTerm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;

class SolarTermSetup
{
    private $entityManager;

    /**
     * WriteNote constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setUp() {
        $count = $this->entityManager->getRepository('AppBundle:SolarTerm')->createQueryBuilder('st')
            ->select('count(st.id)')
            ->getQuery()
            ->getSingleScalarResult();
        if ($count != 0) return 'solar terms are already setup. No changes done';

        $terms = [
            [
                'name' => '立春',
                'month' => 2,
                'day' => 4
            ],
            [
                'name' => '雨水',
                'month' => 2,
                'day' => 19
            ],
            [
                'name' => '惊蛰',
                'month' => 3,
                'day' => 6
            ],
            [
                'name' => '春分',
                'month' => 3,
                'day' => 21
            ],
            [
                'name' => '清明',
                'month' => 4,
                'day' => 5
            ],
            [
                'name' => '谷雨',
                'month' => 4,
                'day' => 20
            ],
            [
                'name' => '立夏',
                'month' => 5,
                'day' => 6
            ],
            [
                'name' => '小满',
                'month' => 5,
                'day' => 21
            ],
            [
                'name' => '芒种',
                'month' => 6,
                'day' => 6
            ],
            [
                'name' => '夏至',
                'month' => 6,
                'day' => 21
            ],
            [
                'name' => '小暑',
                'month' => 7,
                'day' => 7
            ],
            [
                'name' => '大暑',
                'month' => 7,
                'day' => 23
            ],
            [
                'name' => '立秋',
                'month' => 8,
                'day' => 8
            ],
            [
                'name' => '处暑',
                'month' => 8,
                'day' => 23
            ],
            [
                'name' => '白露',
                'month' => 9,
                'day' => 8
            ],
            [
                'name' => '秋分',
                'month' => 9,
                'day' => 23
            ],
            [
                'name' => '寒露',
                'month' => 10,
                'day' => 8
            ],
            [
                'name' => '霜降',
                'month' => 10,
                'day' => 23
            ],
            [
                'name' => '立冬',
                'month' => 11,
                'day' => 7
            ],
            [
                'name' => '小雪',
                'month' => 11,
                'day' => 22
            ],
            [
                'name' => '大雪',
                'month' => 12,
                'day' => 7
            ],
            [
                'name' => '冬至',
                'month' => 12,
                'day' => 22
            ],
            [
                'name' => '小寒',
                'month' => 1,
                'day' => 6
            ],
            [
                'name' => '大寒',
                'month' => 1,
                'day' => 20
            ]
        ];

        foreach ($terms as $sequenceNo => $term) {
            $st = new SolarTerm();
            $st->setName($term['name']);
            $st->setDateFrom($term['month'] . '-' . $term['day']);
            $st->setDateTo($sequenceNo == 23
                ? $terms[0]['month'] . '-' . ($terms[0]['day'] - 1)
                : $terms[$sequenceNo + 1]['month'] . '-' . ($terms[$sequenceNo + 1]['day'] - 1));
            $this->entityManager->persist($st);
            $this->entityManager->flush();
        }
        return "solar terms set up successfully.";
    }

}