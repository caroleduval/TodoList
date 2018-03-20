<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Command\LoadDataCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;

class UserControllerAsAdminTest extends WebTestCase
{
    /**
     * @var null
     */
    private $client=null;

    /**
     * Initialize the test database before running the all tests
     */
    public static function setUpBeforeClass()
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $application = new App($kernel);
        $application->add(new LoadDataCommand());

        $command = $application->find('app:initialize-TDL');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()),array('-env'=>'test'));
    }

    /**
     * Initialize a client to simulate the navigation
     */
    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

    /**
     * Test on "/users" page as Anon
     * Display the users list. must failed.
     */
    public function testUserListAsAnon()
    {
        $this->client = static::createClient();

        $this->client->request('GET', '/users');
        static::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        static::assertRegExp('/\/login$/', $this->client->getRequest()->getUri());
    }
    /**
     * Test on "/users" page as User
     * Display the users list. must failed.
     */
    public function testUserListAsUser()
    {
        $this->client = static::createClient();

        $this->client->request('GET', '/users');

        static::assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test on "/users" page as Admin.
     * Display the users list
     */
    public function testUserListAsAdmin()
    {
        $crawler = $this->client->request('GET', '/users');

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(3, $crawler->filter('a:contains("Edit")')->count());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin without a username. must failed.
     */
    public function testAdminCreateAdminUsernameAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2@mail.fr';
        $form['user[roles]'][1]->tick();
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin with an existing username. must failed.
     */
    public function testAdminCreateAdminUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'emailtest@mail.fr';
        $form['user[roles]'][1]->tick();
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as User.
     * Test on posting an user without a password. must failed.
     */
    public function testAdminCreateAdminPasswordAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userUser2';
        $form['user[password][first]'] = null;
        $form['user[password][second]'] = null;
        $form['user[email]'] = 'userUser2@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin with 2 different passwords. must failed.
     */
    public function testAdminCreateAdminPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usertest2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $form['user[email]'] = 'email3@mail.fr';
        $form['user[roles]'][1]->tick();
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin without an email. must failed.
     */
    public function testAdminCreateAdminEmailAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userwithoutemail';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[roles]'][1]->tick();
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin with an existing email. must failed.
     */
    public function testAdminCreateAdminEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userEmailnotUnique';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'username@email.fr';
        $form['user[roles]'][1]->tick();
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin with an invalid email. must failed.
     */
    public function testAdminCreateAdminInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2';
        $form['user[roles]'][1]->tick();
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin. must succeed.
     */
    public function testAdminCreateAdminOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'NewAdmin';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'NewAdmin@email.fr';
        $form['user[roles]'][1]->tick();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("NewAdmin") ')->count());

        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'NewAdmin',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'NewAdmin';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $this->client->followRedirect();

        $array = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser()->getRoles();

        $this->assertContains('ROLE_ADMIN', $array);
    }

    /**
     * Test on "/users/{id}/edit" page as Admin.
     * Test on editing an inexistant user. must failed.
     */
    public function testInvalidUserEdit()
    {
        $this->client->request('POST', '/tasks/99/edit');

        static::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with an username as null. must failed.
     */
    public function testAdminEditAdminUsernameNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = null;
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with an existing username. must failed.
     */
    public function testAdminEditAdminUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'username';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with 2 different passwords. must failed.
     */
    public function testAdminEditAdminPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin without password. must failed.
     */
    public function testAdminEditAdminPasswordNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $crawler = $this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/edit$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with an email as null. must failed.
     */
    public function testAdminEditAdminEmailNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = null;
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with an existing email. must failed.
     */
    public function testAdminEditAdminEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'username@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with an invalid email. must failed.
     */
    public function testAdminEditAdminInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'useremail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with a valid username. must succeed.
     */
    public function testAdminEditAdminUsernameOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'NewAdmin modif';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("NewAdmin modif") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with a valid password. must succeed.
     */
    public function testAdminEditAdminPassword()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password2';
        $form['user[password][second]'] = 'password2';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing the email of another admin with a valid address. must succeed.
     */
    public function testAdminEditAdminEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'NewAdmin_modif@email.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("NewAdmin_modif@email.fr") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing another admin with a USER_ROLE. must succeed.
     */
    public function testAdminEditAdminRole()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[roles]'][0]->tick();
        $form['user[roles]'][1]->untick();
        $this->client->submit($form);

        $crawler=$this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());


        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'NewAdmin modif',
            'PHP_AUTH_PW'   => 'password2',
        ));

        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'NewAdmin modif';
        $form['_password'] = 'password2';
        $this->client->submit($form);

        $this->client->followRedirect();

        $array = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser()->getRoles();

        $this->assertSame(['ROLE_USER'], $array);
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user without a username. must failed.
     */
    public function testAdminCreateUserUsernameAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user with an existing username. must failed.
     */
    public function testAdminCreateUserUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'username';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'emailtest@mail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user without password. must failed.
     */
    public function testAdminCreateUserPasswordAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usertest2';
        $form['user[email]'] = 'email3@mail.fr';
        $form['user[roles]'][1]->tick();
        $crawler = $this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user with 2 different passwords. must failed.
     */
    public function testAdminCreateUserPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'usertest2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $form['user[email]'] = 'email3@mail.fr';
        $form['user[roles]'][1]->tick();
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user without an email. must failed.
     */
    public function testAdminCreateUserEmailAsNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userwithoutemail';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user with an existing email. must failed.
     */
    public function testAdminCreateUserEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userEmailnotUnique';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'username@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an admin with an invalid email. must failed.
     */
    public function testAdminCreateUserInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAnon2';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'userAnon2';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/users\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/create" page as Admin.
     * Test on posting an user as user. must succeed.
     */
    public function testAdminCreateAsUserOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'NewUser';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'Newuser@email.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("NewUser") ')->count());

        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'NewUser',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'NewUser';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $this->client->followRedirect();

        $array = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser()->getRoles();

        $this->assertContains('ROLE_USER', $array);
        $this->assertNotContains('ROLE_ADMIN', $array);
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with an username as null. must failed.
     */
    public function testAdminEditUserUsernameNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = null;
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with an existing username. must failed.
     */
    public function testAdminEditUserUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'user';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with 2 different passwords. must failed.
     */
    public function testAdminEditUserPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user without password. must failed.
     */
    public function testAdminEditUserPasswordNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $crawler = $this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/edit$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with an email as null. must failed.
     */
    public function testAdminEditUserEmailNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = null;
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with an existing email. must failed.
     */
    public function testAdminEditUserEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'user@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with an invalid email. must failed.
     */
    public function testAdminEditUserInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'useremail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with a valid username. must succeed.
     */
    public function testAdminEditUserUsernameOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'NewUser modif';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("NewUser modif") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with a valid password. must succeed.
     */
    public function testAdminEditUserPassword()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password2';
        $form['user[password][second]'] = 'password2';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing the email of an user with a valid address. must succeed.
     */
    public function testAdminEditUserEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'NewUser_modif@email.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("NewUser_modif@email.fr") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page.
     * Test on editing an user with a USER_ADMIN. must succeed.
     */
    public function testAdminEditUserRole()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[roles]'][1]->tick();
        $this->client->submit($form);

        $crawler=$this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'NewUser modif';
        $form['_password'] = 'password2';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $array = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser()->getRoles();

        static::assertEquals(1, $crawler->filter('html:contains("vos tâches sans effort")')->count());
        $this->assertContains('ROLE_ADMIN', $array);
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an username as null. must failed.
     */
    public function testAdminEditHimselfUsernameNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = null;
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an existing username. must failed.
     */
    public function testAdminEditHimselfUsernameNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'user';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet username est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with 2 different passwords. must failed.
     */
    public function testAdminEditHimselfPasswordError()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password2';
        $crawler = $this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an email as null. must failed.
     */
    public function testAdminEditHimselfEmailNull()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = null;
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an existing email. must failed.
     */
    public function testAdminEditHimselfEmailNotUnique()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'user@email.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Cet email est déjà utilisé.") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the user with an existing email. must failed.
     */
    public function testAdminEditHimselfInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'useremail.fr';
        $crawler=$this->client->submit($form);

        static::assertEquals(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the email with a valid address. must succeed.
     */
    public function testAdminEditHimselfEmail()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[email]'] = 'admin_modif@email.fr';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
        static::assertEquals(1, $crawler->filter('html:contains("admin_modif@email.fr") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the username. must succeed.
     */
    public function testAdminEditHimselfUsernameOK()
    {
        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'admin modif';
        $this->client->submit($form);

        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin modif',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $this->client->request('GET', '/users');

        static::assertEquals(1, $crawler->filter('html:contains("admin modif")')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the password with valid datas. must succeed.
     */
    public function testAdminEditHimselfPassword()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin modif',
            'PHP_AUTH_PW'   => 'password',
        ));

        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[password][first]'] = 'password2';
        $form['user[password][second]'] = 'password2';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié") ')->count());
    }

    /**
     * Test on "/users/{id}/edit" page on his own profile.
     * Test on editing the role. must succeed.
     */
    public function testAdminEditHimselfRole()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin modif',
            'PHP_AUTH_PW'   => 'password2',
        ));

        $crawler = $this->client->request('GET', '/users');

        $link = $crawler->selectLink('Edit')->first()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[roles]'][0]->tick();
        $form['user[roles]'][1]->untick();
        $this->client->submit($form);

        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin modif',
            'PHP_AUTH_PW'   => 'password2',
        ));

        $this->client->request('GET', '/users');

        static::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
