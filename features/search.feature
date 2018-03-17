Feature: Search
  In order to find products dinosaurs love
  As a web user
  I need to be able to search for products

  @javascript
  Scenario: Searching for a product that exists
    Given I am on "/"
    When I fill in "searchTerm" with "Samsung"
    And I press "search_submit"
    Then I should see "Samsung Galaxy"
