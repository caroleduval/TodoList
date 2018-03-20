<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private $validator;

    public function setUp()
    {
        self::bootKernel();

        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    public function testUsername()
    {
        $user = new User();
        $this->assertSame(null, $user->getUsername());
        $user->setUsername("MyUsername");
        $this->assertSame("MyUsername", $user->getUsername());
    }

    public function testPassword()
    {
        $user = new User();
        $this->assertSame(null, $user->getPassword());
        $user->setPassword("Mypassword");
        $this->assertSame("Mypassword", $user->getPassword());
    }

    public function testEmail()
    {
        $user = new User();
        $this->assertSame(null, $user->getEmail());
        $user->setEmail("email@mail.fr");
        $this->assertSame("email@mail.fr", $user->getEmail());
    }

    public function testRoles()
    {
        $user = new User();
        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertSame(['ROLE_ADMIN'], $user->getRoles());
        $user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testValidateUsernameNull()
    {
        $user= new User();
        $user->setUsername(null);
        $user->setEmail("user@email.com");
        $user->setPassword("password");
        $user->setRoles(['ROLE_USER']);

        $errors = $this->validator->validate($user);
        $this->assertEquals(1, count($errors));
    }


    public function testValidateUsernameNotUnique()
    {
        $user= new User();
        $user->setUsername("username");
        $user->setEmail("user@email.com");
        $user->setPassword("password");
        $user->setRoles(['ROLE_USER']);

        $errors = $this->validator->validate($user);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateEmailNull()
    {
        $user= new User();
        $user->setUsername("TestUsername");
        $user->setEmail(null);
        $user->setPassword("password");
        $user->setRoles(['ROLE_USER']);

        $errors = $this->validator->validate($user);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateEmailNotEmail()
    {
        $user= new User();
        $user->setUsername("TestUsername");
        $user->setEmail("username@email.fr");
        $user->setPassword("password");
        $user->setRoles(['ROLE_USER']);

        $errors = $this->validator->validate($user);
        $this->assertEquals(1, count($errors));
    }

    public function testValidateEmailInvalide()
    {
        $user= new User();
        $user->setUsername("TestUsername");
        $user->setEmail("usernameemail.fr");
        $user->setPassword("password");
        $user->setRoles(['ROLE_USER']);

        $errors = $this->validator->validate($user);
        $this->assertEquals(1, count($errors));
    }
}