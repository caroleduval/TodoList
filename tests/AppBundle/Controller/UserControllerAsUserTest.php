<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerAsUserTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

    public function testUserList()
    {
        $crawler = $this->client->request('GET', '/users');

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('a:contains("Edit")')->count());
    }

    public function testUserCreate()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);
        static::assertSame(0, $crawler->filter('html:contains("Administrateur") ')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userUser@email.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
    }

    public function testUserCreateUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userUser2@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    public function testUserCreateEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'admin@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    public function testUserCreatePasswordError()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $form['user[email]'] = 'userUser2@email.fr';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    public function testUserEditUsernameNOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = 'admin';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    public function testUserEditEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[email]'] = 'admin@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    public function testUserEditAnotherUser()
    {
        $this->client->request('GET', 'users/1/edit');

        static::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

//    public function testUserEditUsernameOK()
//    {
//        $crawler = $this->client->request('GET', '/users');
//
//        $link = $crawler->selectLink('Edit')->link();
//        $crawler = $this->client->click($link);
//
//        $form = $crawler->selectButton('Modifier')->form();
//        $form['user_edit[username]'] = 'usertest modif';
//        $this->client->submit($form);
//
//        $crawler = $this->client->followRedirect();
//
//        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
//        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
//    }

    public function testUserEditPasswordOK()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'usertest modif',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[password][first]'] = 'password2';
        $form['user_edit[password][second]'] = 'password2';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
    }
}
