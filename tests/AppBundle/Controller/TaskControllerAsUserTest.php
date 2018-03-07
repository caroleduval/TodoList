<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerAsUserTest extends WebTestCase
{
    private $client=null;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'password',
        ));
    }

//////////////////////////////////////////// fonctionne
    public function testTaskList()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        static::assertEquals(2, $crawler->filter('form:contains("Supprimer")')->count());
    }

////////////////////////////////////////// fonctionne
    public function testTaskCreate()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Créer une tâche')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'une tâche test user';
        $form['task[content]'] = 'description d\'une tâche';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(1, $crawler->filter('html:contains("une tâche test user")')->count());
    }

    public function testTaskEditTitleAsOwnerOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('une tâche test user')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'une tâche test modifiée';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('html:contains("une tâche test user")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("une tâche test modifiée")')->count());
    }

    public function testTaskEditContentAsOwnerOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('une tâche test modifiée')->link();
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

    public function testTaskToggleAsOwnerOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Marquer comme faite")')->last()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("a bien été marquée comme faite.")')->count());
    }

    public function testTaskEditTitleNotAsOwnerOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Réaliser le projet')->last()->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Autre tache modifiée';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('html:contains("Réaliser le projet")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("Autre tache modifiée")')->count());
    }

    public function testTaskEditContentNotAsOwnerOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $link = $crawler->selectLink('Autre tache modifiée')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[content]'] = 'Autre description modifiée';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        static::assertEquals(0, $crawler->filter('html:contains("Détails de la tache")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("Autre description modifiée")')->count());
    }

    public function testTaskToggleNotAsOwnerOK()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Marquer comme faite")')->first()->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        static::assertEquals(200, $this->client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('html:contains("a bien été marquée comme faite.")')->count());
    }

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
        static::assertEquals(0, $crawler->filter('html:contains("une tâche modifiée")')->count());
    }

    public function testTaskDeleteNotAsOwner()
    {
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);

        $form = $crawler->filter('button:contains("Supprimer")')->first()->form();
        $this->client->submit($form);

        static::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
