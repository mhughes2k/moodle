@mod @mod_assign @javascript
Feature: Disclose description and activity instructions based on Allow
  submissions from date, and display on the view page (in addition to edit
  submission page).
  As a teacher
  I need to be ensure that students see the relevant information at the
  correct moment in time.

  Background:
    Given I login as "admin"
    And I set the following administration settings values:
      | Enable timed assignments | 1 |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activity" exists:
      | activity                           | assign                 |
      | course                             | C1                     |
      | name                               | Assignment name        |
      | description                        | Assignment description |
      | activity                           | Activity instructions  |
      | assignsubmission_file_enabled      | 1                      |
      | assignsubmission_file_maxfiles     | 1                      |
      | assignsubmission_file_maxsizebytes | 0                      |
      | submissiondrafts                   | 0                      |
    Given the following "activity" exists:
      | activity                            | assign                               |
      | course                              | C1                                   |
      | name                                | Test late assignment with time limit |
      | description                         | Assignment description               |
      | activity                            | Activity instructions                |
      | assignsubmission_onlinetext_enabled | 1                                    |
      | assignsubmission_file_enabled       | 1                                    |
      | assignsubmission_file_maxfiles      | 1                                    |
      | assignsubmission_file_maxsizebytes  | 1000000                              |
      | submissiondrafts                    | 0                                    |
      | allowsubmissionsfromdate_enabled    | 0                                    |
      | duedate_enabled                     | 0                                    |
      | cutoffdate_enabled                  | 0                                    |
      | gradingduedate_enabled              | 0                                    |

  Scenario: Description and Additional instructions should only be displayed once
    Allow Submissions From Date has elapsed.

  Scenario: Description should always appear if "Always show description" is set
    and Additional instructions should only be displayed once
    Allow Submissions From Date has elapsed.

  Scenario: Description and Additional instructions should always appear
