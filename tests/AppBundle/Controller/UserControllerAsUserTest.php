<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerAsUserTest extends WebTestCase
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
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

    /**
     * Test on "/users" page as User.
     * Display the user's profile page
     */
    public function testUserList()
    {
        $crawler = $this->client->request('GET', '/users');

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('a:contains("Edit")')->count());
    }

    /**
     * Test on "/users/create" page as User.
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
     * Test on "/users/create" page as User.
     * Test on posting an user with an existing username. must failed.
     */
    public function testUserCreateUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userUser2@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user without a password. must failed.
     */
    public function testUserCreatePasswordAsNull()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = null;
        $form['user[password][second]'] = null;
        $form['user[email]'] = 'userUser2@email.fr';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user with 2 different passwords. must failed.
     */
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

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user without an email. must failed.
     */
    public function testUserCreateEmailAsNull()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user with an existing email. must failed.
     */
    public function testUserCreateEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'username@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user with an invalid email. must failed.
     */
    public function testUserCreateInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userUser2';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user. must succeed.
     */
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

    /**
     * Test on "/users/{id}/edit" page on an another user's profile. must failed.
     */
    public function testUserEditAnotherUser()
    {
        $this->client->request('GET', 'users/1/edit');

        static::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with username as null. must failed.
     */
    public function testUserEditUsernameAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = null;
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an existing username. must failed.
     */
    public function testUserEditUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = 'username';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with 2 different passwords. must failed.
     */
    public function testUserEditPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[password][first]'] = 'password';
        $form['user_edit[password][second]'] = 'password2';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an email as null. must failed.
     */
    public function testUserEditEmailAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[email]'] = null;
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an existing email. must failed.
     */
    public function testUserEditEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[email]'] = 'username@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as Anon.
     * Test on posting an user with an invalid email. must failed.
     */
    public function testUserEditInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[email]'] = 'invalidemail';
        $crawler=$this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with a valid username. must succeed.
     */
    public function testUserEditEmailOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[email]'] = 'usertest_modif@email.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("usertest_modif@email.fr")')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with a valid username. must succeed.
     */
    public function testUserEditUsernameOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[username]'] = 'usertest modif';
        $this->client->submit($form);

        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'usertest modif',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $this->client->request('GET', '/users');

        static::assertSame(1, $crawler->filter('html:contains("usertest modif")')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with a valid email. must succeed.
     */
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
