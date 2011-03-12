<?php

/**
 * @file StatisticsSocket.inc.php
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StatisticsSocket
 * @ingroup plugins_generic_statisticsAggregation
 *
 * @brief A helper class to create a Socket to send data
 */

// $Id$

class StatisticsSocket {

	var $socketUrl = 'warhammer.hil.unb.ca';
	var $socketPort = 80;
	var $socketTimeout = 30;

	function send($code, $data) {

		// a trap to prevent stats collection during plugin setup (since all hooks fire)
		if ($code == '') {
			return;
		}

		$_requestData = "code=" . urlencode($code) . "&data=" . urlencode($data);
		$fp = fsockopen($this->socketUrl, $this->socketPort, $errno, $errstr, $this->socketTimeout);
		if (!$fp) {
			return false;
		} else {
			$content = "POST /index.php/track HTTP/1.1\r\n";
			$content .= "Host: " . $this->socketUrl . "\r\n";
			$content .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$content .= "Content-Length: " . strlen($_requestData) . "\r\n\r\n";
			$content .= $_requestData;

			fwrite($fp, $content);
			$return = '';
			while (($buffer = fgets($fp, 1)) !== false) {
				$return .= $buffer;
			}

			fclose($fp);
			return $return;
		}
	}
}
?>
