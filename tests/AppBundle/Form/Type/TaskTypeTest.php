<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\Type\TaskType;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    /**
     * Test the TaskTypeForm
     */
    public function testSubmitValidData()
    {
        $formData = array(
            'title' => 'my title test',
            'content' => 'my content test',
        );

        $task0=new Task();
        $user= new User();
        $task0->setAuthor($user);

        $form = $this->factory->create(TaskType::class,$task0);

        $task= new Task();
        $task->SetTitle('my title test');
        $task->SetContent('my content test');
        $task->setAuthor($user);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($task->getTitle(), $form->getData()->getTitle());
        $this->assertEquals($task->getContent(), $form->getData()->getContent());
        $this->assertEquals($task->getAuthor(), $form->getData()->getAuthor());
        $this->assertInstanceOf('DateTime', $form->getData()->getCreatedAt());
        $this->assertEquals(false, $form->getData()->isDone());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}