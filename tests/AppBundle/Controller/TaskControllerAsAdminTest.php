<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;

class TaskControllerAsAdminTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

//////////////////////////////////////////// fonctionne
    public function testTaskList()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        static::assertEquals(3, $crawler->filter('form:contains("Supprimer")')->count());
    }

//////////////////////////////////////////// fonctionne
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
    }

//////////////////////////////////////////// fonctionne
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

//////////////////////////////////////////// fonctionne
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

//////////////////////////////////////////// fonctionne
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
        static::assertSame(1, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

//////////////////////////////////////////// fonctionne
    public function testTaskDeleteAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Supprimer")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! La tâche a bien été supprimée.")')->count());
        static::assertEquals(0, $crawler->filter('html:contains("une tâche test modifiée")')->count());
    }

//////////////////////////////////////////// fonctionne
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

//////////////////////////////////////////// fonctionne
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
        static::assertEquals(0, $crawler->filter('html:contains("Détails de la tâche 3")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("description modifiée")')->count());
    }

//////////////////////////////////////////// fonctionne
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
        static::assertSame(1, $crawler->filter('form:contains("Marquer non terminée")')->count());
    }

//////////////////////////////////////////// fonctionne
    public function testTaskDeleteNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Supprimer")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("Superbe ! La tâche a bien été supprimée.")')->count());
        static::assertEquals(0, $crawler->filter('html:contains("Titre modifié")')->count());
        static::assertEquals(2, $crawler->filter('form:contains("Supprimer")')->count());
    }
}
