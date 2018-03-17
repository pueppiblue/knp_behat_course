<?php

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Mink\Element\DocumentElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context
{
    use KernelDictionary;

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
     * @BeforeScenario
     */
    public function clearDataBase()
    {
        $em = $this->getEntityManager();
        $purger = new ORMPurger($em);
        $purger->purge();
    }

    /**
     * @Given there is an admin user with username :username and password :password
     */
    public function thereIsAnAdminUserWithUsernameAndPassword($username, $password)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
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
     * @Given there are :count products
     */
    public function thereAreProducts(int $count)
    {
        $em = $this->getEntityManager();
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product->setName('Testproduct'.$i);
            $product->setPrice(random_int(1, 100) + $i);
            $product->setDescription('Test description '.$i);
            $em->persist($product);
        }

        $em->flush();
    }

    /**
     * @When I click :linkText
     */
    public function iClick($linkText)
    {
        $this->getPage()->clickLink($linkText);
        //$this->getPage()->findLink($linkText)->click();
    }

    /**
     * @Then I should see :count products
     */
    public function iShouldSeeProducts(int $count)
    {
        $table = $this->getPage()->find('css', 'table.table');
        assertNotNull($table, 'Table could not be found.');
        $rows = $table->findAll('css', 'tbody tr');
        assertNotNull($rows, 'Table had no columns');

        assertCount($count, $rows, 'Table did not have correct number of rows');
    }

    /**
     * @Given /^I am logged in as an admin$/
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $this->thereIsAnAdminUserWithUsernameAndPassword('admin', 'admin');
        $this->visitPath('/login');
        $this->getPage()->fillField('Username', 'admin');
        $this->getPage()->fillField('Password', 'admin');
        $this->getPage()->pressButton('Login');
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
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
