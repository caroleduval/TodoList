<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Command\LoadDataCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @var null
     */
    private $client=null;

    /**
     * Initialize the test database before running the all tests
     */
    public static function setUpBeforeClass()
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $application = new App($kernel);
        $application->add(new LoadDataCommand());

        $command = $application->find('app:initialize-TDL');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()),array('-env'=>'test'));
    }

    /**
     * Initialize a client to simulate the navigation
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Test for logging in and out as Administrator
     */
    public function testloginActionisOKasAdmin()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $array = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser()->getRoles();

        static::assertEquals(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
        $this->assertContains('ROLE_ADMIN', $array);

        $this->client->request('GET', '/logout');
        $this->client->followRedirect();
        $this->client->followRedirect();
        static::assertRegExp('/\/login$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test for logging in and out as User
     */
    public function testloginActionisOKasUser()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $array = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser()->getRoles();

        static::assertEquals(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
        $this->assertNotContains('ROLE_ADMIN', $array);

        $this->client->request('GET', '/logout');
        $this->client->followRedirect();
        $this->client->followRedirect();
        static::assertRegExp('/\/login$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test for logging with an invalid username
     */
    public function testloginWrongUsername()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'bsername';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(1, $crawler->filter('div.alert.alert-danger')->count());
    }

    /**
     * Test for logging with an invalid password
     */
    public function testloginWrongPassword()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'username';
        $form['_password'] = 'wrong';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(1, $crawler->filter('div.alert.alert-danger')->count());
    }
}