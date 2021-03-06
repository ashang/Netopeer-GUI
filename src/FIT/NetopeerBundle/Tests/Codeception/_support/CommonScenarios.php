<?php

namespace FIT\NetopeerBundle\Tests\Codeception\_support;

use \WebGuy;

class CommonScenarios {

	public static $deviceUser = "netopeergui";
	public static $devicePass = "netopeergui";

	public static function login($I)
	{
		/**
		 * @var WebGuy $I
		 */
		$I->maximizeWindow();
		$I->wantTo('log in as admin user');
		$I->amOnPage('/logout');
		$I->fillField('Username', 'admin');
		$I->fillField('Password', 'pass');
		$I->click('Log in');
		$I->expect('I am redirected to connections page');
		$I->seeCurrentUrlMatches('/connections/');
		$I->see('List of active connections');
	}

	public static function connectToLocalhostDevice($I)
	{
		/**
		 * @var WebGuy $I
		 */
		$I->wantTo('connect to localhost device');
		$I->amOnPage('/connections/');
		$I->fillField('Host', 'localhost');
		$I->fillField('Port', '830');
		$I->fillField('User', CommonScenarios::$deviceUser);
		$I->fillField('Password', CommonScenarios::$devicePass);
		$I->click('Connect');
		$I->waitForText('Loading...', 10);
		$I->waitForText('Configure device', 50);
		$I->waitForText('History of connected devices', 10);
		$I->waitForText('localhost:830', 2);
		self::checkNumberOfFlashes($I, 3);
	}

	public static function checkNumberOfFlashes($I, $number, $kind = 'success', $message = null) {
		/**
		 * @var WebGuy $I
		 */
		$I->expectTo('see '.$number.' of '.$kind.' flashes');
		$I->wait(1);
		$I->click('.ico-alerts');
		$I->wait(2);
		$I->waitForElementVisible('#block--alerts');
		$I->seeNumberOfElements('.message.'.$kind, $number);
		if (!is_null($message)) {
			$I->canSee($message);
		}
		$I->click('.ico-alerts');
		$I->wait(3);
//		$I->waitForElementNotVisible('#block--alerts');
	}

	public static function waitAndClickInTypeahead($I, $link) {
		/**
		 * @var WebGuy $I
		 */
		$I->waitForElement('.typeahead');
		$I->waitForText($link, 5, '.typeahead');
		$I->click($link, '.typeahead');
		$I->wait(3);
	}
} 