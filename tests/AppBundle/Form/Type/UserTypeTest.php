<?php

namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class UserTypeTest extends TypeTestCase
{
    private $validator;

    /**
     * @return array
     */
    protected function getExtensions()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $this->validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata('Symfony\Component\Form\Form')));

        return array(
            new ValidatorExtension($this->validator)
        );
    }

    /**
     * Test the UserType form
     */
    public function testSubmitValidData()
    {
        $user = new User();
        $form = $this->factory->create(UserType::class, $user);

        $formData = array(
            'username' => 'username',
            'password' => array(
                'first' => 'password',
                'second' => 'password',
            ),
            'email' => 'test@mail.fr',
            'roles' => ['ROLE_USER']
        );

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('username', $user->getUsername());
        $this->assertSame('test@mail.fr', $user->getEmail());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}