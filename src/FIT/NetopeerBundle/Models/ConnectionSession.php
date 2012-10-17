<?php

namespace FIT\NetopeerBundle\Models;

class ConnectionSession {
	/**
	 * @var time of connection start
	 */
	public $time;

	/**
	 * @var identification key of connection
	 */
	public $hash;

	/**
	 * @var target hostname
	 */
	public $host;

	/**
	 * @var locked by us
	 */
	public $locked = false;

	function __construct($session_hash, $host)
	{
		$this->hash = $session_hash;
		$this->host = $host;
		$newtime = new \DateTime();
		$this->time = $newtime->format("d.m.Y H:i:s");
		$this->locked = false;
	}
}