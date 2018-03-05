<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ElementNotFoundException;

class WebContext extends MinkContext
{
    /**
     * @When I wait for :arg1 seconds
     */
    public function iWaitForSeconds($arg1)
    {
        $this->getSession()->wait($arg1 * 1000);
    }

    /**
     * @When I scroll
     */
    public function iScroll()
    {
        $this->getSession()->executeScript('window.scrollTo(2000,2000);');
    }

    /**
     * Looks for a table, then looks for a row that contains the given text.
     * Once it finds the right row, it clicks a link in that row.
     *
     * Really handy when you have a generic "Edit" link on each row of
     * a table, and you want to click a specific one (e.g. the "Edit" link
     * in the row that contains "Item #2")|
     *
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