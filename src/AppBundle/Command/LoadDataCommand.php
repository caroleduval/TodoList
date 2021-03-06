<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class LoadDataCommand extends ContainerAwareCommand
{
    /**
     * Configure command options
     */
    protected function configure()
    {
        $this
            ->setName('app:initialize-TDL')
            ->setDescription('Initializes database for tests.')
            ->setHelp('This command set up the test database and tables with datas that enabled the tests...')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();

        # Supprimer la base de données existante
        $createdbCmd = $app->find('doctrine:database:drop');
        $createdbInput = new ArrayInput(['command' => 'doctrine:database:drop', '--force' => true, '--if-exists' => true]);
        $createdbCmd->run($createdbInput, $output);

        # Créer la base de données
        $createdbCmd = $app->find('doctrine:database:create');
        $createdbInput = new ArrayInput(['command' => 'doctrine:database:create']);
        $createdbCmd->run($createdbInput, $output);


        # Créer les tables
        $createtablesCmd = $app->find('doctrine:schema:update');
        $createtablesInput = new ArrayInput(['command' => 'doctrine:schema:update', '--force' => true]);
        $createtablesCmd->run($createtablesInput, $output);

        # Charger les données
        $loaddataCmd = $app->find('doctrine:fixtures:load');
        $loaddataInput = new ArrayInput(['command' => 'doctrine:fixtures:load']);
        $loaddataInput->setInteractive(false);
        $loaddataCmd->run($loaddataInput, $output);
    }
}