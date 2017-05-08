<?php

namespace Tests\Browser\Pages\Payment;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use PHPUnit_Framework_Assert as PHPUnit;

class P24ChooseBank extends BasePage
{
	/**
	 * Get the URL for the page.
	 *
	 * @return string
	 */
	public function url()
	{
		return 'https://sandbox.przelewy24.pl/trnRequest';
	}

	/**
	 * Assert that the browser is on the page.
	 *
	 * @return void
	 */
	public function assert(Browser $browser)
	{
		PHPUnit::assertStringStartsWith($this->url(), $browser->driver->getCurrentURL());
	}

	/**
	 * Get the element shortcuts for the page.
	 *
	 * @return array
	 */
	public function elements()
	{
		return [
			'@ing-logo'        => '.bank-logo-112',
			'@accept-tou'      => '#reagulation-accept-button',
			'@login-button'    => 'button[type="submit"]',
			'@confirm-payment' => '#pay_by_link_pay',
		];
	}
}
