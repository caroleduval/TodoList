<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;


/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

//    /**
//     * @Given there is a user :username with password :password
//     */
//    public function thereIsAUserWithPassword($username, $password)
//    {
//        $user = new \AppBundle\Entity\User();
//        $user->setUsername($username);
//        $user->setPassword($password);
//        $user->setEmail("example@mail.com");
//
//        $em = $this->getContainer()->get('doctrine')->getManager();
//        $em->persist($user);
//        $em->flush();
//    }
}
