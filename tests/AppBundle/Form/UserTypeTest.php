<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'username' => 'username test',
            'password' => 'password',
            'password_second' => 'password',
            'email' => 'test@mail.fr'
        );

        $user=new User();
        $user->setRoles(['ROLE_ADMIN']);
        $form = $this->factory->create(UserType::class,$user, array('role' => 'ROLE_ADMIN'));

        $user= new User();
        $user->hydrate($formData);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}