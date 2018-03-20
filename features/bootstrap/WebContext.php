<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ElementNotFoundException;

class WebContext extends MinkContext
{
    /**
     * @BeforeSuite
     */
    public static function beforeSuite()
    {
        exec('bin/console app:initialize-TDL --env=test');
    }

    /**
     * @Given I am connected as :username with password :password
     */
    public function iAmConnectedAsWithPassword($username, $password)
    {
        $this->visit('/login');
        $this->fillField('_username',$username);
        $this->fillField('_password',$password);
        $this->pressButton('Se connecter');
    }

    /**
     * @When /^I follow "([^"]*)" on the row containing "([^"]*)"$/
     */
    public function iClickLinkOnTheRowContaining($link, $text)
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $text));
        if (!$row) {
            throw new ElementNotFoundException($this->getSession(), 'element', 'css', $text);
        }
        $row->clickLink($link);
    }
}