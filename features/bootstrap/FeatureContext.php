<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $output = null;
    
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }
    
    /**
     * @Given /^i have the "([^"]*)" Package installed$/
     */
    public function iHaveThePackageInstalled($argument1)
    {
        $command = "dpkg -l {$argument1}";
        exec($command, $output, $return);
        if ($return !== 0) {
            throw new Exception ("Package {$argument1} is not installed");
        }
    }

    /**
     * Then the file "/etc/ntp.conf" should exist # features/time.feature:7
     * And the file "/usr/share/zoneinfo/localtime" should exist # features/time.feature:28
     *
     * @Then /^the file "([^"]*)" should exist$/
     */
    public function theFileShouldExist($argument1)
    {
        if ( !is_readable($argument1) ) {
            throw new Exception ("File {$argument1} does not exist");
        }
    }

    /**
     * And they need to match to "ntp.org" # features/time.feature:9
     *
     * @Given /^they need to match to "([^"]*)"$/
     */
    public function theyNeedToMatchTo($argument1)
    {
        throw new PendingException();
    }

    /**
     * Then the directory "/usr/share/zoneinfo" should exist # features/time.feature:27
     *
     * @Then /^the directory "([^"]*)" should exist$/
     */
    public function theDirectoryShouldExist($argument1)
    {
        if ( !is_dir($argument1) ) {
            throw new Exception("Directory does not exist");
        }
    }

    /**
     * @When /^i check the processlist$/
     */
    public function iCheckTheProcesslist()
    {
        return $this->iExecute("ps -ef");
    }


    /**
     * When i execute "ntpq -pn" # features/time.feature:15
     * When i execute "date" # features/time.feature:31
     * When i execute "date  --utc -d '2006-08-07 12:34:56-06:00'" # features/time.feature:34
     * When i execute "TZ=Europe/London date -d '2006-08-07 12:34:56-06:00'" # features/time.feature:38
     *
     * @When /^i execute "([^"]*)"$/
     */
    public function iExecute($argument1)
    {
        $this->output = null;
        
        exec($argument1, $output, $return);
        if ( $return !== 0 ) {
            throw new Exception("Command did not return 0");
        }
        
        $this->output = $output;
    }

    /**
     * @Then /^the output should match "([^"]*)"$/
     */
    public function theOutputShouldMatch($match)
    {
        if ($this->output === null) {
            throw new Exception("Execution of previous command did not yield output");
        }
        
        foreach ($this->output as $line) {
        
            if (preg_match("#.*({$match}).*#i", $line, $matches)) {
                return true;
            }
        
        }
        
        throw new Exception("{$match} not found in output");
        
    }

    /**
     * @Given /^the "([^"]*)" Package Version .* match(es)? "([^"]*)"/
     */
    public function checkPackageVersion($package, $to_match)
    {
        exec("apt-cache show {$package}", $output, $return);
        
        if ( $return !== 0 ) {
            throw new Exception("Unable to get {$package} info");
        }
        
        foreach ($output as $line) {
            if (!preg_match("#^Version:\s?(.*)$#i", $line, $matches)) {
                continue;
            }
            
            if (preg_match("#^Version:\s?{$to_match}#i", $line) === False) {
                throw new Exception("Version {$to_match} is not correct");
            }            
        }
    }


    /**
     * @Given /^needs to have atleast "([^"]*)" "([^"]*)" lines$/
     */
    public function needsToHaveAtleastLines($argument1, $argument2)
    {
        throw new PendingException();
    }

    /**
     * @Given /^they have to match to "([^"]*)"$/
     */
    public function theyHaveToMatchTo($argument1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the PID file "([^"]*)" should match the Processes PID$/
     */
    public function thePIDFileShouldMatchTheProcessesPID($pidfile)
    {
        exec("pidof ntpd", $output, $return);
        
        if ($return !== 0 && !isset($output[0])) {
            throw new Exception("Unable to find PID of ntpd");
        }
        
        $pid = file_get_contents($pidfile);
        
        if (!is_numeric($pid) || $pid != $output[0]) {
            throw new Exception("pid file and process id don't match");
        }
    }

}
