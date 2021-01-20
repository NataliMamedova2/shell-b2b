<?php

declare(strict_types=1);

namespace Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
use Codeception\Module;
use Codeception\Exception\ModuleException;
use Codeception\TestCase;
use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 *
 */
class Acceptance extends Module
{

    /**
     * @var RemoteWebDriver
     */
    private $webDriver = null;

    /**
     * @var Module\WebDriver
     */
    private $webDriverModule = null;

    /**
     * Event hook before a test starts.
     *
     * @param TestCase $test
     *
     * @throws Exception
     */
    public function _before(TestCase $test)
    {
        if (!$this->hasModule('WebDriver') && !$this->hasModule('Selenium2')) {
            throw new Exception('PageWait uses the WebDriver. Please be sure that this module is activated.');
        }

        // Use WebDriver
        if ($this->hasModule('WebDriver')) {
            $this->webDriverModule = $this->getModule('WebDriver');
            $this->webDriver = $this->webDriverModule->webDriver;
        }
    }

    public function waitPageLoad($timeout = 10)
    {

        $this->webDriverModule->waitForJs('return document.readyState == "complete"', $timeout);
    }

    public function dontSeeJsError()
    {
        $logs = $this->webDriver->manage()->getLog('browser');
        foreach ($logs as $log) {
            if ($log['level'] == 'SEVERE') {
                throw new ModuleException($this, 'Some error in JavaScript: ' . json_encode($log));
            }
        }
    }
}
