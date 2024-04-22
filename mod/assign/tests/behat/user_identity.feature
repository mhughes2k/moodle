@core
Feature: An appropriate authorised user can see custom user identity fields
  on assignment areas.

  Background:
    Given the following "custom profile fields" exist:
      | datatype  | shortname           | name                |
      | text      | registrationnumber  | Registration Number |
    And the following "users" exist:
      | username | firstname | lastname | email           | department | registrationnumber | firstnamephonetic |
      | teacher1 | Teacher   | 1        | teacher1@example.com |       |                    |                   |
      | user1    | User      | One      | one@example.com | Attack     | 12345678901        | Yewzer            |
      | user2    | User      | Two      | two@example.com | Defence    | 12345678902        | Yoozare           |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | user1 | C1 | student |
    And the following "activity" exists:
      | activity                                      | assign                  |
      | course                                        | C1                      |
      | name                                          | Test assignment name    |
      | intro                                         | Submit your online text |
      | submissiondrafts                              | 0                       |
      | assignsubmission_onlinetext_enabled           | 1                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |
    And I log in as "admin"

  Scenario: Assignment grading table displays custom identity fields
    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    Then I should see "Registration Number" in the "table" "css_element"
    And I should see "12345678901"
