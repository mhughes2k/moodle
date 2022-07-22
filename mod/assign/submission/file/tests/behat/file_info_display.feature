@mod @mod_assign @assignsubmission_file @assignsubmission_file_65325
Feature: In assignment, display to the student the details of the accepted
  file submission.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Any file allowed information is displayed for assignment
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | submissiondrafts                    | 0                       |
      | assignsubmission_onlinetext_enabled | 0                       |
      | assignsubmission_file_enabled       | 1                       |
      | assignsubmission_file_maxfiles      | 2                       |
      | assignsubmission_file_maxsizebytes  | 1048576                 |
      | assignsubmission_file_filetypes     |                         |
    And I am on the "Test assignment name" Activity page logged in as student1
    Then I should see "You may submit up to 2 files."
    And I should see "Any type of file may be submitted"

  @javascript
  Scenario: Spreadsheet File types allowed information is displayed for assignment
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | submissiondrafts                    | 0                       |
      | assignsubmission_onlinetext_enabled | 0                       |
      | assignsubmission_file_enabled       | 1                       |
      | assignsubmission_file_maxfiles      | 2                       |
      | assignsubmission_file_maxsizebytes  | 1048576                 |
      | assignsubmission_file_filetypes     | spreadsheet             |
    And I am on the "Test assignment name" Activity page logged in as student1
    Then I should see "You may submit up to 2 files."
    And I should see "You may submit files of the following types: "
    And I should see "spreadsheet"
