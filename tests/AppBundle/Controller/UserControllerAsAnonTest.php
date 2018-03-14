<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerAsAnonTest extends WebTestCase
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
     * Test on "/users" page as Anon. must failed
     */
    public function testUserList()
    {
        $this->client->request('GET', '/users');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user. must succeed.
     */
    public function testUserCreate()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);
        static::assertSame(0, $crawler->filter('html:contains("Administrateur") ')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'NewAnon';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'NewAnon@email.fr';
        $this->client->submit($form);

        $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user without a username. must failed.
     */
    public function testUserCreateUsernameAsNull()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user with an existing username. must failed.
     */
    public function testUserCreateUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user without an email. must failed.
     */
    public function testUserCreateEmailAsNull()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user with an invalid email. must failed.
     */
    public function testUserCreateInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user with an existing email. must failed.
     */
    public function testUserCreateEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usertest';
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'username@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user with 2 different passwords. must failed.
     */
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

    /**
     * Test on "/users/{id}/edit" page as Anon. must failed.
     */
    public function testUserEditKO()
    {
        $this->client->request('GET', '/users/1/edit');

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
    }
}
