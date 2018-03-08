<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-04
 * Time: 18:58
 */

namespace AppBundle\ORM\DataFixtures;


use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setId(1);
        $user->setAccessToken('{123456}');
        $user->setEnabled(true);
        $user->setReceivePushNotifications(false);
        $user->setUsername('test_user');
        $user->setNickname('test_user');
        $user->setEmail('test_user@test.com');
        $user->setPlainPassword('password');

        $metadata = $manager->getClassMetaData(User::class);
        $metadata->setIdGenerator(new AssignedGenerator());
        $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
        $manager->persist($user);
        $manager->flush();

        $this->addReference('test-user', $user);
    }

    public function getOrder(){
        return 1;
    }
}