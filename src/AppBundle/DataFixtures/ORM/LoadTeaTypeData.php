<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-04
 * Time: 18:58
 */

namespace AppBundle\ORM\DataFixtures;


use AppBundle\Entity\TeaType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class LoadTeaTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $teaType = new TeaType();
        $teaType->setId(1);
        $teaType->setName('红茶');
        $teaType->setDescription('Mock description');

        $metadata = $manager->getClassMetaData(TeaType::class);
        $metadata->setIdGenerator(new AssignedGenerator());
        $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);

        $manager->persist($teaType);
        $manager->flush();

        $this->addReference('red-tea-type', $teaType);
    }

    public function getOrder(){
        return 2;
    }
}