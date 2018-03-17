Feature: Search
  In order to find products dinosaurs love
  As a web user
  I need to be able to search for products

  Background:
    Given I am on "/"

  Scenario Outline:
    When I fill in "searchTerm" with "<term>"
    And I press "search_submit"
    Then I should see "<result>"
    Examples:
      | term    | result            |
      | Samsung | Samsung Galaxy    |
      | XBOX    | No products found |

#  Scenario: Searching for a product that exists
#    When I fill in "searchTerm" with "Samsung"
#    And I press "search_submit"
#    Then I should see "Samsung Galaxy"
#
#  Scenario: Searching for a product that does not exists
#    When I fill in "searchTerm" with "XBOX"
#    And I press "search_submit"
#    Then I should see "No products found"
