<?php
/**
 * Created by PhpStorm.
 * User: dalexa
 * Date: 09/02/15
 * Time: 10:55
 */

namespace FIT\NetopeerBundle\Tests\_files\MockClass;

class Data extends \FIT\NetopeerBundle\Models\Data {

	public function getPathToModels() {
		return __DIR__ . '/../completeRequestTree/';
	}

} 