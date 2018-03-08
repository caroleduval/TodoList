<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
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

    public function testHydrate()
    {
        $formData = array(
            'username' => 'my username test',
            'password' => 'my password test',
            'email' => 'my email test',
            'roles' => ['ROLE_ADMIN']
        );

        $user = new User();

        $user->hydrate($formData);
        static::assertSame('my username test',$user->getUsername());
        static::assertSame('my password test',$user->getPassword());
        static::assertSame('my email test',$user->getEmail());
        static::assertSame(['ROLE_ADMIN'],$user->getRoles());
    }
}