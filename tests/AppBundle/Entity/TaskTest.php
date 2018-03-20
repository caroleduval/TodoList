<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    private $validator;

    private $user;

    public function setUp()
    {
        self::bootKernel();

        $this->validator = static::$kernel->getContainer()->get('validator');

        $this->user = new User();
        $this->user->setUsername("usernamefortest");
        $this->user->setEmail("usernamefortest@email.fr");
        $this->user->setPassword("password");
        $this->user->setRoles(['ROLE_USER']);
    }

    public function testSettingCreatedAt()
    {
        $task = new Task();
        static::assertInstanceOf("DateTime", $task->getCreatedAt());
        $task->setCreatedAt("01/01/2018");
        static::assertSame("01/01/2018", $task->getCreatedAt());
    }

    public function testSettingTitle()
    {
        $task = new Task();
        static::assertSame(null, $task->getTitle());
        $task->setTitle("mon titre");
        static::assertSame("mon titre", $task->getTitle());
    }

    public function testSettingContent()
    {
        $task = new Task();
        static::assertSame(null, $task->getContent());
        $task->setContent("mon contenu");
        static::assertSame("mon contenu", $task->getContent());
    }

    public function testSettingIsDone()
    {
        $task = new Task();
        static::assertSame(false, $task->isDone());
        $task->toggle(true);
        static::assertSame(true, $task->isDone());
        $task->toggle(false);
        static::assertSame(false, $task->isDone());
    }

    public function testSettingAuthor()
    {
        $task = new Task();
        static::assertSame(null, $task->getAuthor());
        $task->setAuthor($this->user);
        static::assertSame($this->user, $task->getAuthor());
    }

    public function testValidateTitle()
    {
        $task= new Task();
        $task->setTitle(null);
        $task->setContent("content");
        $task->setAuthor($this->user);

        $errors = $this->validator->validate($task);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateContent()
    {
        $task= new Task();
        $task->setTitle("titre");
        $task->setContent(null);
        $task->setAuthor($this->user);
        $task->isDone(false);

        $errors = $this->validator->validate($task);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateAuthor()
    {
        $task= new Task();
        $task->setTitle("titre");
        $task->setContent("content");
        $task->setAuthor(null);
        $task->isDone(false);

        $errors = $this->validator->validate($task);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateCreatedAt()
    {
        $task= new Task();
        $task->setTitle("titre");
        $task->setContent("content");
        $task->setAuthor($this->user);
        $task->setCreatedAt("d day");
        $task->isDone(false);

        $errors = $this->validator->validate($task);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateisDone()
    {
        $task= new Task();
        $task->setTitle("titre");
        $task->setContent(null);
        $task->setAuthor($this->user);
        $task->isDone("false");

        $errors = $this->validator->validate($task);
        $this->assertEquals(1, count($errors));
    }
}