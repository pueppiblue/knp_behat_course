Feature: Product Admin Area
  In order to maintain the products shown on the site
  As an admin user
  I need to be able to create/edit/delete products

  Scenario: List available products
    Given there aree 5 products
    And I am on "/admin"
    When I click "Products"
    Then I should see products

  Scenario: Add a new product
    Given I am on "/admin/products"
    When I click "New Product"
    And I fill in "Name" with "Veloci-chew toy"
    And I fill in "Price with "100"
    And I fill in "Description" with "Have your raptor chew on this instead!"
    And I press "Save"
    Then I should see "Product created FTW!"
