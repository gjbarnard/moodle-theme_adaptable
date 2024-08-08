Feature: Basic navigation scenarios through Moodle.

  Background: User "admin" logs into Moodle and then logs out
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And I log in as "admin"
    And I follow "View profile" in the user menu
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    Then I log out

  Scenario: Basic navigation without JavaScript

  @javascript
  Scenario: Basic navigation with JavaScript