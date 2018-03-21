Feature: Product Admin Area
  In order to maintain the products shown on the site
  As an admin user
  I need to be able to create/edit/delete products

  Background:
    Given I am logged in as an admin

  Scenario: List available products
    Given there are 5 products
    And there is 1 products
    And I am on "/admin"
    When I click "Products"
    Then I should see 6 products

  Scenario: Products show author
    Given I author 5 products
    When I am on "/admin/products"
    Then I should not see "Anonymous"

  Scenario:
    Given the following products exits:
      | name | is published |
      | Foo1 | yes          |
      | Bar1 | no           |
    When I go to "/admin/products"
   # *todo*

  @javascript
  Scenario: Add a new product
    And I am on "/admin/products"
    When I click "New Product"
    And I wait for the modal to load
    And I fill in "Name" with "Veloci-chew toy"
    And I fill in "Price" with "100"
    And I fill in "Description" with "Have your raptor chew on this instead!"
    And I press "Save"
    Then I should see "Product created FTW!"
    And I should be on "/admin/products"
    And I should see "Veloci-chew toy"
    And I should not see "Anonymous"
