Feature: NTP Daemon
    As a Server
    I want to have a ntp daemon
    So that my Time is always correct
    
Scenario: The NTP Daemon Installation and Configuration
    Given i have the "ntp" Package installed
    Then the file "/etc/ntp.conf" should exist
    And needs to have atleast "4" "server" lines
    And they have to match to "ntp.org"
    
Scenario: The NTP Daemon operation
    When i check the processlist 
    Then the output should match "ntpd"
    And the PID file "/var/run/ntpd.pid" should match the Processes PID
    And the file "/var/lib/ntp/ntp.drift" should exist

    When i execute "ntpq -pn"
    Then the output should match "^\*"
    
