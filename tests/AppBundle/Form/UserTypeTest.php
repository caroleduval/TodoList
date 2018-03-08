<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;;
use Symfony\Component\Form\PreloadedExtension;

class UserTypeTest extends TypeTestCase
{
    private $authorizationChecker;

    protected function setUp()
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        parent::setUp();
    }


    protected function getExtensions()
    {
        $type = new UserType($this->authorizationChecker);

        return array(
            new PreloadedExtension(array($type), array()),
        );
    }

    public function testSubmitValidData()
    {
        $formData = array(
            'username' => 'username test',
            'password' => 'password',
            'password_second' => 'password',
            'email' => 'test@mail.fr'
        );

        $this->authorizationChecker
            ->method('isGranted')
            ->with(['ROLE_ADMIN'])
            ->willReturn(true);

        $user=new User();
        $user->setRoles(['ROLE_ADMIN']);

        $form = $this->factory->create(UserType::class);

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