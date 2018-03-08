<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerAsAdminTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

    public function testUserList()
    {
        $crawler = $this->client->request('GET', '/users');

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(3, $crawler->filter('a:contains("Edit")')->count());
    }

    public function testAdminCreateAsAdminOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usertestAdmin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'admin2@mail.fr';
        $form['user[roles]'][1]->tick();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
        static::assertSame(1, $crawler->filter('html:contains("usertestAdmin") ')->count());
    }

    public function testAdminCreateAsUserOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAdmin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'useradmin@mail.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
    }

    public function testAdminCreateUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAdmin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'emailtest@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    public function testAdminCreateEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAdmin2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'useradmin@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    public function testAdminCreatePasswordError()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usertest2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $form['user[email]'] = 'email3@mail.fr';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }


    public function testAdminEditUsernameNOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = 'user';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    public function testAdminEditEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[email]'] = 'user@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    public function testAdminEditPassword()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = 'usertestUser';
        $form['user_edit[password][first]'] = 'password2';
        $form['user_edit[password][second]'] = 'password2';
        $form['user_edit[email]'] = 'email3@mail.fr';
        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
    }

    public function testAdminEditPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[password][first]'] = 'password';
        $form['user_edit[password][second]'] = 'password2';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    public function testAdminEditUsernameOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = 'usertest modif';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
    }
}

