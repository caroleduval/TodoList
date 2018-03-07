<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexifnotconnected()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $crawler = $client->followRedirect();

        static::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    public function testIndexifconnected()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $client->request('GET', '/');

//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
    }
}
