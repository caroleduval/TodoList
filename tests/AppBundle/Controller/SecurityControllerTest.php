<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testloginActionisOKasAdmin()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
//        $this->assertContains('ROLE_ADMIN', $this->client->getUser()->getRoles());
    }

    public function testloginActionisOKasUser()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
//        $this->assertNotContains('ROLE_ADMIN', $this->client->getResponse()->getUser()->getRoles());
    }

    public function testloginWrongUsername()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'bsername';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('div.alert.alert-danger')->count());
    }

    public function testloginWrongPassword()
    {
        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'username';
        $form['_password'] = 'wrong';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('div.alert.alert-danger')->count());
    }
}