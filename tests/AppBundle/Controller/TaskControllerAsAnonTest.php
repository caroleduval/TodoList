<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class TaskControllerAsAnonTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

//////////////////////////////////////////// fonctionne
    public function testTaskList()
    {
        $this->client->request('GET', '/tasks');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

////////////////////////////////////////// fonctionne
    public function testTaskCreate()
    {
        $this->client->request('GET', '/tasks/create');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    public function testTaskEdit()
    {
        $this->client->request('GET', '/tasks/1/edit');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    public function testTaskDelete()
    {
        $this->client->request('GET', '/tasks/1/delete');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }
}
