<?php

use Behat\Behat\Context\Context;
use Behat\Mink\Element\DocumentElement;
use Behat\MinkExtension\Context\RawMinkContext;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context
{
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

    /**
     * @When I fill in the search box with :term
     */
    public function iFillInTheSearchBoxWith($term)
    {
        $searchBox = $this->getPage()
            ->find('css', '[name=searchTerm]');

        assertNotNull($searchBox, 'Search box not found.');

        $searchBox->setValue($term);
    }

    /**
     * @When I press the search button
     */
    public function iPressTheSearchButton()
    {
        $searchButton = $this->getPage()
            ->find('css', '#search_submit');

        assertNotNull($searchButton, 'Search button not found.');

        $searchButton->press();
    }

    /**
     * Shortcut:
     * Returns a page from the session object
     */
    private function getPage(): DocumentElement
    {
        return $this->getSession()->getPage();
    }
}
