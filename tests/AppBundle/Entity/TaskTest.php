<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
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
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('1');

        $task = new Task();
        static::assertSame(null, $task->getAuthor());
        $task->setAuthor($user);
        static::assertSame('1', $task->getAuthor()->getId());
    }

    public function testHydrate()
    {
        $formData = array(
            'title' => 'my title test',
            'content' => 'my content test',
        );

        $task = new Task();

        $task->hydrate($formData);
        static::assertSame('my title test',$task->getTitle());
        static::assertSame('my content test',$task->getContent());
    }
}