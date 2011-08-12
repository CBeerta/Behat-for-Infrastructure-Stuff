Feature: tzdata Configuration
    As a Server
    I want to have a tzdata installation
    So that my calculations for various timezones are always correct
    
Scenario: The tzdata data Installation and Configuration

    Given i have the "tzdata" Package installed
    Then the directory "/usr/share/zoneinfo" should exist
    And the "tzdata" Package Version should match "2011(d|e)"
    And the file "/usr/share/zoneinfo/localtime" should exist
    And the file "/etc/localtime" should exist

Scenario: The tzdata checks for correct times
    
    When i execute "date"
    Then the output should match "CEST|CET"
    
    When i execute "date  --utc -d '2006-08-07 12:34:56-06:00'"
    Then the output should match "UTC"
    And the output should match "18:34:56"
    
    When i execute "TZ=Europe/London date -d '2006-08-07 12:34:56-06:00'"
    Then the output should match "BST|GMT"
    And the output should match "19:34:56"
