<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerAsAnonTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testUserList()
    {
        $this->client->request('GET', '/users');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    public function testUserCreate()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);
        static::assertSame(0, $crawler->filter('html:contains("Administrateur") ')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon@mail.fr';
        $this->client->submit($form);

        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
    }

    public function testUserCreateUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    public function testUserCreateEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();        $form['user[username]'] = 'usertest';
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    public function testUserCreatePasswordError()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $form['user[email]'] = 'userAnon2@mail.fr';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    public function testUserEditKO()
    {
        $this->client->request('GET', '/users/1/edit');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }
}
