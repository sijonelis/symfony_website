<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 12/21/2016
 * Time: 3:48 AM
 */

namespace AppBundle\Command;


use AppBundle\Services\TeaFeature\FeatureRandomTea;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeatureTeaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('yipiao:feature:random-tea')
            ->setDescription('Features a random tea from the available, published teas.')
//            ->addArgument(
//                'name',
//                InputArgument::OPTIONAL,
//                'Who do you want to greet?'
//            )
//            ->addOption(
//                'yell',
//                null,
//                InputOption::VALUE_NONE,
//                'If set, the task will yell in uppercase letters'
//            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FeatureRandomTea $featureRandomTeaService */
        $featureRandomTeaService = $this->getContainer()->get('app.tea.feature_random_tea');
        $featureRandomTeaService->featureTea();
        $output->writeln('Featured teas until today');
    }
}