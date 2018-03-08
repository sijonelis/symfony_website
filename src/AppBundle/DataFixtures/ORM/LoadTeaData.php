<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-08-04
 * Time: 18:58
 */

namespace AppBundle\ORM\DataFixtures;


use AppBundle\Entity\Tea;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class LoadTeaData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tea = new Tea();
        $tea->setId(1);
        $tea->setName('红茶');
        $tea->setTeaType($this->getReference('red-tea-type'));
        $tea->setCoverImage('5982da1286e5d505906266.jpg');
        $tea->setHistory("<h1>明前龙井</h1>
                            <p>龙井是属于绿茶，产于浙江杭州西湖一带，由古代茶农创制于宋代，已有一千二百余年历史。<br />
                            龙井茶有&ldquo;色绿、香郁、味甘、形美&rdquo;四绝的特点。所含氨基酸、儿茶素、叶绿素、维生素C等成分均比其它茶叶多，营养丰富。<br />
                            因为采摘时间不同，分为&ldquo;明前茶&rdquo;和&ldquo;雨前茶&rdquo;。在清明前采制的叫&ldquo;明前茶&rdquo;，谷雨前采制的叫&ldquo;雨前茶&rdquo;。向有&ldquo;雨前是上品，明前是珍品&rdquo;的说法。</p>
                            
                            <p><img alt=\"\" height=\"140\" src=\"http://oqi0g5mfs.bkt.clouddn.com/tea/64407963486.jpg\" width=\"140\" /></p>");
        $tea->setTitle('红茶');
        $tea->setWaterTitle("prep title");
        $tea->setWater("<p>載断日作当庁償務定知請観口田。配際加様支州見室浦雄禁地撮熱講地供雄天舎。家新青録請決事識之更民触較人要走予囲政決。正好協記柴金検拡要念針視。自雑進社在政子周紙読区加。補本覧公筆毎撮内固転療漢稿著天払更成真混。無差遅子負金携思竜応食経産回容。供並行立堀伝勝街棄質口条仲歯電校圏識。表島信会抄通融地作炎無初夕険果旅暮位。</p>");
        $tea->setStorage("<p>医支本堂審典新位月存佐流商要仕分山最甘。畳転玉配卒心賠住住地紙真。者掲恵面検心知趣治幕集実盛題属。力挙喜重石聞経認国速社法件続排。元後移増療択禁原横写更信抱予一決費兼。何作王努編言険時織院割左域生伊新提済。取政無注航生勝国社世詳財置観。訪巨情下祝原章蔵字作港恐堀豆自現月言曲。冬第郡政名評招本野保自房報未。<br />
&nbsp;</p>");
        $tea->setPublishedAt(new \DateTime('2017-04-09 05:00:03'));
        $tea->setUpdatedAt(new \DateTime('2017-04-09 05:00:03'));
        $tea->setPublished(true);
        $tea->setUser($this->getReference('test-user'));
        $tea->setViewCount(6);

        $metadata = $manager->getClassMetaData(Tea::class);
        $metadata->setIdGenerator(new AssignedGenerator());
        $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
        $manager->persist($tea);
        $manager->flush();

        $this->addReference('test-tea1', $tea);

//        $tea->setPublished(false);
//        $manager->persist($tea);
//        $manager->flush();
    }

    public function getOrder(){
        return 3;
    }
}