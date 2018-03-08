<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-18
 * Time: 09:22
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\TeaFeatured;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNewsfeedData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tf1 = new TeaFeatured();
        $tf1->setTea($this->getReference('test-tea1'));
        $tf1->setDay(new \DateTime('2017-04-09'));


        $tf2 = new TeaFeatured();
        $tf2->setTea($this->getReference('test-tea1'));
        $tf2->setDay(new \DateTime('2017-04-10'));


        $tf3 = new TeaFeatured();
        $tf3->setTea($this->getReference('test-tea1'));
        $tf3->setDay(new \DateTime('2017-04-11'));

        $tf1->setNextTea($tf2);
        $tf2->setNextTea($tf3);
        $tf2->setPreviousTea($tf1);
        $tf3->setPreviousTea($tf2);

        $manager->persist($tf1);
        $manager->persist($tf2);
        $manager->persist($tf3);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}
    
