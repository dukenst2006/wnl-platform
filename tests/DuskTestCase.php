<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Tests\BethinkBrowser;

abstract class DuskTestCase extends BaseTestCase
{
	use CreatesApplication;

	/**
	 * Prepare for Dusk test execution.
	 *
	 * @beforeClass
	 * @return void
	 */
	public static function prepare()
	{
		static::startChromeDriver();
	}

	/**
	 * Create the RemoteWebDriver instance.
	 *
	 * @return \Facebook\WebDriver\Remote\RemoteWebDriver
	 */
	protected function driver()
	{
		return RemoteWebDriver::create(
			'http://localhost:9515', DesiredCapabilities::chrome()
		);
	}

	/**
	 * Create a new Browser instance.
	 *
	 * @param  \Facebook\WebDriver\Remote\RemoteWebDriver $driver
	 * @return \Laravel\Dusk\Browser
	 */
	protected function newBrowser($driver)
	{
		return new BethinkBrowser($driver);
	}
}
