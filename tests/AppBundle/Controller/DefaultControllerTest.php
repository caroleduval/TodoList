<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * Test on homepage "/" if not connected. must failed
     */
    public function testIndexifnotconnected()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $crawler = $client->followRedirect();

        static::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    /**
     * Test on homepage "/" if connected. must succeed
     */
    public function testIndexifconnectedasUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $client->request('GET', '/');

        static::assertEquals(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
    }

    /**
     * Test on homepage "/" if connected. must succeed
     */
    public function testIndexifconnectedasAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $client->request('GET', '/');

        static::assertEquals(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
    }
}
