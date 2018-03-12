<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\LoadDataCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class LoadDataCommandTest extends KernelTestCase
{
    /**
     * Testing the command that loads the testing data base
     */
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new LoadDataCommand());

        $command = $application->find('app:initialize-TDL');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--env' => 'test',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Database schema updated successfully!', $output);
        $this->assertContains('purging database', $output);
        $this->assertContains('loading AppBundle\DataFixtures\ORM\LoadData', $output);
    }
}