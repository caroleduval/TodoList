<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testSettingCreatedAt()
    {
        $task = new Task();
//        $this->assertSame("", $task->getCreatedAt());
        $task->setCreatedAt("01/01/2018");
        static::assertSame("01/01/2018", $task->getCreatedAt());
    }

    public function testSettingTitle()
    {
        $task = new Task();
        $this->assertSame(null, $task->getTitle());
        $task->setTitle("mon titre");
        $this->assertSame("mon titre", $task->getTitle());
    }

    public function testSettingContent()
    {
        $task = new Task();
        $this->assertSame(null, $task->getContent());
        $task->setContent("mon contenu");
        $this->assertSame("mon contenu", $task->getContent());
    }

    public function testSettingIsDone()
    {
        $task = new Task();
        $this->assertSame(false, $task->isDone());
        $task->toggle(true);
        $this->assertSame(true, $task->isDone());
        $task->toggle(false);
        $this->assertSame(false, $task->isDone());
    }
}