<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class TaskControllerAsAnonTest extends WebTestCase
{
    /**
     * @var null
     */
    private $client=null;

    /**
     * Initialize a client to simulate the navigation
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Test on "/tasks" page as Anon. must failed
     */
    public function testTaskList()
    {
        $this->client->request('GET', '/tasks');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    /**
     * Test on "/tasks/create" page as Anon. must failed
     */
    public function testTaskCreate()
    {
        $this->client->request('GET', '/tasks/create');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    /**
     * Test on "/tasks/{id}/edit" page as Anon. must failed
     */
    public function testTaskEdit()
    {
        $this->client->request('GET', '/tasks/1/edit');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    /**
     * Test on "/tasks/{id}/toggle" page as Anon. must failed
     */
    public function testTaskToggle()
    {
        $this->client->request('GET', '/tasks/1/toggle');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    /**
     * Test on "/tasks/{id}/delete" page as Anon. must failed
     */
    public function testTaskDelete()
    {
        $this->client->request('GET', '/tasks/1/delete');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }
}
