<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-07-21
 * Time: 20:49
 */

namespace AppBundle\Command;


use AppBundle\Services\SolarTermSetup;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SolarTermSetUpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('yipiao:solar-term:setup')
            ->setDescription('Setups solar term db table.')
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
        /** @var SolarTermSetup $solarTermService */
        $solarTermService = $this->getContainer()->get('app.solarterm.setup');
        $output->writeln($solarTermService->setup());
    }
}