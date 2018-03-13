<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;

class TaskControllerAsAdminTest extends WebTestCase
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
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

    /**
     * Test on "/tasks" pages as Admin. must succeed
     */
    public function testTaskList()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        static::assertEquals(4, $crawler->filter('form:contains("Marquer comme faite")')->count());
        static::assertEquals(0, $crawler->filter('form:contains("Marquer non terminée")')->count());

        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);

        static::assertEquals(0, $crawler->filter('form:contains("Marquer comme faite")')->count());
        static::assertEquals(1, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

    /**
     * Test on "/tasks/create" page as Admin.
     * Test on posting a task without title. must failed.
     */
    public function testTaskCreatewithoutTitle()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Créer une tâche')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[content]'] = 'description d\'une tâche';
        $this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/tasks\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/tasks/create" page as Admin.
     * Test on posting a task without content. must failed.
     */
    public function testTaskCreatewithoutContent()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Créer une tâche')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'une tâche test admin';
        $this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('/\/tasks\/create$/', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/tasks/create" page as Admin.
     * Test on posting a task. must succeed.
     */
    public function testTaskCreate()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Créer une tâche')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'une tâche test admin';
        $form['task[content]'] = 'description d\'une tâche';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(1, $crawler->filter('html:contains("une tâche test admin")')->count());
        static::assertEquals(5, $crawler->filter('form:contains("Marquer comme faite")')->count());

        $form = $crawler->filter('button:contains("Supprimer")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertSame(1, $crawler->filter('html:contains("Superbe ! La tâche a bien été supprimée.")')->count());
        static::assertEquals(0, $crawler->filter('html:contains("une tâche test admin")')->count());
        static::assertEquals(4, $crawler->filter('form:contains("Marquer comme faite")')->count());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing a task title with null. must failed.
     */
    public function testTaskEditTitleWithNullAsOwner()
    {
        $crawler = $this->client->request('GET','tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'une tâche test admin';
        $form['task[content]'] = 'description d\'une tâche';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $link = $crawler->selectLink('une tâche test admin')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = null;
        $this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing a task title. must succeed.
     */
    public function testTaskEditTitleAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('une tâche test admin')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'une tâche test modifiée';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('a:contains("une tâche test admin")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("une tâche test modifiée")')->count());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing a task content with null. must failed.
     */
    public function testTaskEditContentWithNullAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('une tâche test modifiée')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[content]'] = null;
        $this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing a task content. must succeed.
     */
    public function testTaskEditContentAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('une tâche test modifiée')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[content]'] = 'description modifiée';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('html:contains("description d\'une tâche")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("description modifiée")')->count());
    }

    /**
     * Test on "/tasks/toggle" page as Admin.
     * Test on editing a task status. must succeed.
     */
    public function testTaskToggleAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Marquer comme faite")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("a bien été marquée comme faite.")')->count());
        static::assertEquals(4, $crawler->filter('form:contains("Marquer comme faite")')->count());

        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);

        static::assertSame(2, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

    /**
     * Test on "/tasks/{id}/delete" page as Admin.
     * Test on deleting one of his task. must succeed.
     */
    public function testTaskDeleteAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Supprimer")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! La tâche a bien été supprimée.")')->count());
        static::assertEquals(0, $crawler->filter('html:contains("une tâche test modifiée")')->count());
        static::assertEquals(1, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing the title of a task of another user with null. must failed.
     */
    public function testTaskEditTitleWithNullNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Passer la soutenance')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = null;
        $this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing the content of a task of another user with null. must failed.
     */
    public function testTaskEditContentWithNullNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Passer la soutenance')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[content]'] = null;
        $this->client->submit($form);

        static::assertSame(0, $crawler->filter('div.alert.alert-success')->count());
        static::assertRegExp('#edit#', $this->client->getRequest()->getUri());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing the title of a task of another user. must succeed.
     */
    public function testTaskEditTitleNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Passer la soutenance')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Titre modifié';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('html:contains("Passer la soutenance")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("Titre modifié")')->count());
    }

    /**
     * Test on "/tasks/edit" page as Admin.
     * Test on editing the content of a task of another user. must succeed.
     */
    public function testTaskEditContentNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Titre modifié')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[content]'] = 'description modifiée';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('html:contains("Détails de la tâche 5")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("description modifiée")')->count());
    }

    /**
     * Test on "/tasks/toggle" page as Admin.
     * Test on editing the status of a task of another user. must succeed.
     */
    public function testTaskToggleNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Marquer comme faite")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("a bien été marquée comme faite.")')->count());
        static::assertSame(3, $crawler->filter('form:contains("Marquer comme faite")')->count());

        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);

        static::assertSame(2, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

    /**
     * Test on "/tasks/{id}/delete" page as Admin.
     * Test on deleting the task of another user. must succeed.
     */
    public function testTaskDeleteNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Supprimer")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! La tâche a bien été supprimée.")')->count());
        static::assertSame(1, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

    /**
     * Test on "/tasks/{id}/delete" page as Admin.
     * Test on deleting an inexistant task. must failed.
     */
    public function testInvalidTaskDelete()
    {
        $this->client->request('DELETE', '/tasks/99/delete');

        static::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
