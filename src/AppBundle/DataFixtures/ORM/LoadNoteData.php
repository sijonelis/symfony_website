<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-18
 * Time: 09:17
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Note;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNoteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $note = new Note();
        $note->setTea($this->getReference('test-tea1'));
        $note->setUser($this->getReference('test-user'));
        $note->setViewed(false);
        $note->setNote('Test note text');
        $note->setCreatedAt(new \DateTime('2017-04-09 05:00:03'));
        $note->setNotifyStaff(false);

        $manager->persist($note);
        $manager->flush();

    }

    public function getOrder() {
        return 4;
    }
}