<?php
namespace Tests\Selenium;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

require_once __DIR__ . '/../../TestLinkAPIClient.php';

class BaseTest extends TestCase
{
    protected $driver;
    protected $baseUrl = "http://localhost/projumi";
    protected $tl; 
    protected $testPlanId = 13;
    protected $buildId = 1;
    protected $devKey = "3d8abad2f729384a69eb6abfa1bdec2c";

    public function setUp(): void
    {
        parent::setUp();

        // TestLink API
        $this->tl = new \TestLinkAPIClient(
            "http://127.0.0.1/testlink/lib/api/xmlrpc/v1/xmlrpc.php",
            $this->devKey
        );

        // Selenium
        $this->driver = RemoteWebDriver::create(
            "http://localhost:57918",
            DesiredCapabilities::chrome()
        );
    }

    public function tearDown(): void
    {
        try { $this->driver->quit(); } catch (\Exception $e) {}
        parent::tearDown();
    }

    // Métodos generales reutilizables
    protected function openLoginModal(): void
    {
        $btn = $this->driver->findElement(WebDriverBy::cssSelector('[data-bs-target="#loginModal"]'));
        $btn->click();

        $modal = $this->driver->findElement(WebDriverBy::id('loginModal'));
        $this->driver->wait(5)->until(WebDriverExpectedCondition::visibilityOf($modal));
        sleep(1);
    }

    public function reportTestResult($testCaseExternalId, $passed, $notes = "")
    {
        $status = $passed ? "p" : "f";
        return $this->tl->reportTCResult(
            $testCaseExternalId,
            $this->testPlanId,
            $this->buildId,
            $status,
            $notes
        );
    }
}
