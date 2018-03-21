<?php

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
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

    /** @var User */
    private $currentUser;

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
    public function thereIsAnAdminUserWithUsernameAndPassword($username, $password): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
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
     * @Given there is/are :count product(s)
     */
    public function thereAreProducts(int $count)
    {
        $this->createProducts($count);
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
        $this->currentUser = $this->thereIsAnAdminUserWithUsernameAndPassword('admin', 'admin');
        $this->visitPath('/login');
        $this->getPage()->fillField('Username', 'admin');
        $this->getPage()->fillField('Password', 'admin');
        $this->getPage()->pressButton('Login');
    }

    /**
     * @Given /^I author (\d+) products$/
     */
    public function iAuthorProducts($count)
    {
        $this->createProducts($count, $this->currentUser);
    }

    /**
     * @Given /^I wait for the modal to load$/
     * @throws \InvalidArgumentException
     */
    public function iWaitForTheModalToLoad()
    {
        $page = $this->getPage();

        $page->waitFor(
            1,
            function () use ($page) {
                return $page->find('named', ['field', 'Name']);
            }
        );
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) == '') {
        }
        fwrite(STDOUT, "\033[u");
    }

    /**
     * Saving a screenshot
     *
     * @When I save a screenshot to :filename
     */
    public function iSaveAScreenshotIn($filename)
    {
        sleep(1);
        $this->saveScreenshot($filename, __DIR__.'/../../');
    }

    /**
     * @Given /^the following products exits:$/
     */
    public function theFollowingProductsExits(TableNode $table)
    {
        $em = $this->getEntityManager();

        foreach ($table as $row) {
            $product = new Product();
            $product->setName($row['name'] ?? '');
            $product->setDescription($row['description'] ?? '');
            $product->setPrice($row['price'] ?? random_int(10, 1000));
            $product->setIsPublished($row['is published'] === 'yes');

            $em->persist($product);
        }

        $em->flush();
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

    private function createProducts(int $count, User $author = null): void
    {
        $em = $this->getEntityManager();
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product->setName('Testproduct'.$i);
            $product->setPrice(random_int(1, 100) + $i);
            $product->setDescription('Test description '.$i);

            if ($author) {
                $product->setAuthor($author);
            }

            $em->persist($product);
        }

        $em->flush();
    }
}
