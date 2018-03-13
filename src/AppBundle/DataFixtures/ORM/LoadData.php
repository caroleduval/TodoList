<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;

class LoadData implements FixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $user1 = new User();
        $user1->setUsername('admin');
        $user1->setPassword('$2y$13$SFuhW5eNHstI7JyfFydotOzUwqhzCN98XiyDsrYCZEFLJLGC71EPi');
        $user1->setEmail('admin@email.fr');
        $user1->setRoles(['ROLE_ADMIN']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setPassword('$2y$13$7GJRmUVza83wFIGgHdN80OE0Ebsm91kSIaVLnhUMfary84eYvP9qG');
        $user2->setEmail('user@email.fr');
        $user2->setRoles(['ROLE_USER']);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('username');
        $user3->setPassword('$2y$13$j2tIf6A9Qsph.tVo8bzuFe7Jfr4aAuKj7yNSLmtM2Bh7JvfZQ0n3a');
        $user3->setEmail('username@email.fr');
        $user3->setRoles(['ROLE_USER']);
        $manager->persist($user3);

        $data_tasks = array(
            [$user1, '2018-02-13 16:24:30', 'Réaliser le projet', 'Détails de la tâche 1', true],
            [$user1, '2018-02-13 17:24:30', 'Analyser le projet', 'Détails de la tâche 2', false],
            [$user2, '2018-02-13 18:24:30', 'Travailler sur le front', 'Détails de la tâche 3', false],
            [$user1, '2018-02-13 19:24:30', 'Finaliser le projet', 'Détails de la tâche 4', false],
            [$user2, '2018-02-13 20:24:30', 'Passer la soutenance', 'Détails de la tâche 5', false],
        );

        foreach ($data_tasks as $data) {
            $task = new Task();
            $task->setAuthor($data[0]);
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data[1]);
            $task->setCreatedAt($date);
            $task->setTitle($data[2]);
            $task->setContent($data[3]);
            $task->toggle($data[4]);

            $manager->persist($task);
        }

        $manager->flush();
    }
}