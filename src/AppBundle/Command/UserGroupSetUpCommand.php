<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-10-20
 * Time: 13:54
 */

namespace AppBundle\Command;


use AppBundle\Services\UserGroupSetup;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserGroupSetUpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('yipiao:user-group:setup')
            ->setDescription('Setups user group db table.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserGroupSetup $solarTermService */
        $userGroupSetupService = $this->getContainer()->get('app.usergroup.setup');
        $output->writeln($userGroupSetupService->setup());
    }
}