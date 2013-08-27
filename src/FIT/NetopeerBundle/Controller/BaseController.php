<?php
/**
 * BaseController as parent of  all controllers in this bundle handles all common functions
 * such as assigning template variables, menu structure...
 *
 * @file BaseController.php
 * @author David Alexa <alexa.david@me.com>
 *
 * Copyright (C) 2012-2013 CESNET
 *
 * LICENSE TERMS
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 * 3. Neither the name of the Company nor the names of its contributors
 *    may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * ALTERNATIVELY, provided that this notice is retained in full, this
 * product may be distributed under the terms of the GNU General Public
 * License (GPL) version 2 or later, in which case the provisions
 * of the GPL apply INSTEAD OF those given above.
 *
 * This software is provided ``as is'', and any express or implied
 * warranties, including, but not limited to, the implied warranties of
 * merchantability and fitness for a particular purpose are disclaimed.
 * In no event shall the company or contributors be liable for any
 * direct, indirect, incidental, special, exemplary, or consequential
 * damages (including, but not limited to, procurement of substitute
 * goods or services; loss of use, data, or profits; or business
 * interruption) however caused and on any theory of liability, whether
 * in contract, strict liability, or tort (including negligence or
 * otherwise) arising in any way out of the use of this software, even
 * if advised of the possibility of such damage.
 *
 */
namespace FIT\NetopeerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Response;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * BaseController - parent of all other controllers in this Bundle.
 *
 * Defines common functions for all controllers, such as assigning template variables etc.
 */
class BaseController extends Controller
{
	/**
	 * @var int  Active section key
	 */
	private $activeSectionKey;
	/**
	 * @var string  url of submenu
	 */
	private $submenuUrl;
	/**
	 * @var array   array of all variables assigned into template
	 */
	private $twigArr;
	/**
	 * @var array   array of template blocks for ajax requests
	 */
	private $ajaxBlocksArr;

	/**
	 * Assignees variable to array, which will be send to template
	 * @param  mixed $key   key of the associative array
	 * @param  mixed $value value of the associative array
	 */
	protected function assign($key, $value) {
		$this->twigArr[$key] = $value;
	}

	/**
	 * Prepares variables to template, sort flashes and prepare menu
	 * @param bool $getOnlyArray  if true, no response will be returned - only PHP array of values
	 * @return array|Response     array of assigned variables to template or AjaxBlockResponse
	 */
	protected function getTwigArr($getOnlyArray = false) {

		$this->prepareGlobalTwigVariables();

		if (!$getOnlyArray) {
			if ($this->getRequest()->isXmlHttpRequest() || $this->getRequest()->getSession()->get('isAjax') === true) {
				$this->getRequest()->getSession()->remove('isAjax');
				return $this->getAjaxBlocksResponse();
			}
			$this->getRequest()->getSession()->remove('isAjax');
		}

		return $this->twigArr;
	}

	/**
	 * Prepares global and common variables for twig
	 */
	protected function prepareGlobalTwigVariables() {
		if ( $this->getRequest()->getSession()->get('singleColumnLayout') == null ) {
			$this->getRequest()->getSession()->set('singleColumnLayout', true);
		}

		// if singleColumnLayout is not set, we will set default value
		if ( !array_key_exists('singleColumnLayout', $this->twigArr) ) {
			$this->assign('singleColumnLayout', $this->getRequest()->getSession()->get('singleColumnLayout'));
		}

		$this->prepareAndAssignFlashes();

		$this->assign("topmenu", array());
		$this->assign("submenu", array());
		if ($this->getRequest()->get('_route') !== '_home' &&
				!strpos($this->getRequest()->get('_controller'), 'AjaxController')) {
			$dataClass = $this->get('DataModel');
			$dataClass->buildMenuStructure($this->activeSectionKey);
			$this->assign('topmenu', $dataClass->getModels());
			$this->assign('submenu', $dataClass->getSubmenu($this->submenuUrl, $this->getRequest()->get('key')));
		}

		try {
			if ($this->getRequest()->get('key') != "") {
				$conn = $this->getRequest()->getSession()->get('session-connections');
				$conn = unserialize($conn[$this->getRequest()->get('key')]);
				if ($conn !== false) {
					$this->assign('lockedConn', $conn->locked);
					$this->assign('sessionStatus', $conn->sessionStatus);
					$this->assign('sessionHash', $conn->hash);
				}
			}
		} catch (\ErrorException $e) {
			$this->get('logger')->notice('Trying to use foreign session key', array('error' => $e->getMessage()));
			$this->getRequest()->getSession()->setFlash('error', "Trying to use unknown connection. Please, connect to the device.");
		}
	}

	/**
	 * constructor, which instantiate empty class variables
	 */
	public function __construct () {
		$this->twigArr = array();	
		$this->activeSectionKey = null;
		$this->ajaxBlocksArr = array();
	}

	/**
	 * sets current section key
	 *
	 * @param int     $key          key of connected server
	 */
	public function setActiveSectionKey($key) {
		$this->activeSectionKey = $key;
	}

	/**
	 * gets current section key
	 *
	 * @return int|null   section key
	 */
	public function getActiveSectionKey() {
		return $this->activeSectionKey;
	}

	/**
	 * sets submenu URL.
	 *
	 * @param string $submenuUrl  URL for submenu
	 */
	public function setSubmenuUrl($submenuUrl) {
		$this->submenuUrl = $submenuUrl;
	}

	/**
	 * @param string $templateNamespace     namespace of template, where blockId is defined
	 * @param string $blockId               block name from template
	 */
	protected function addAjaxBlock($templateNamespace, $blockId) {
		$this->ajaxBlocksArr[$blockId] = array(
			'template' => $templateNamespace,
			'blockId' => $blockId,
		);
	}

	/**
	 * @return array    array with definition of ajax blocks
	 */
	protected function getAjaxBlocks() {
		return $this->ajaxBlocksArr;
	}

	/**
	 * prepares flashes into right template variables
	 */
	protected function prepareAndAssignFlashes() {
		$session = $this->getRequest()->getSession();
		$flashes = array_merge($session->getFlashes(), $session->getShortFlashes());
		$stateFlashes = $configFlashes = $leftPaneFlashes = $singleFlashes = $allFlashes = array();

		// divide flash messages according to key into categories
		foreach ($flashes as $key => $message) {
			$isInfoMessage = false;

			// a little bit tricky - if key contains word state, condition will be pass
			if ( strpos($key, 'tate') && strpos($key, "info") ) { // key contains word state
				$stateFlashes[$key] = $message;
				$isInfoMessage = true;
			} elseif ( strpos($key, 'onfig') && strpos($key, "info") ) { // key contains word config
				$configFlashes[$key] = $message;
				$isInfoMessage = true;
			} elseif ( strpos($key, 'eftPane') ) { // key contains word leftPane
				$leftPaneFlashes[$key] = $message;
			} else { // key contains word single
				$singleFlashes[$key] = $message;
			}

			if (!$isInfoMessage) $allFlashes[$key] = $message;
			$session->removeFlash($key);
		}

		$this->assign("stateInfoFlashes", $stateFlashes);
		$this->assign("configInfoFlashes", $configFlashes);
		$this->assign("allInfoFlashes", array_merge($stateFlashes, $configFlashes));
		$this->assign("singleFlashes", $singleFlashes);
		$this->assign("allFlashes", $allFlashes);
	}

	/**
	 * @return Response   json encoded array of ajax blocks
	 */
	protected function getAjaxAlertsRespose() {
		$this->addAjaxBlock('FITNetopeerBundle:Default:section.html.twig', 'alerts');
		$this->prepareAndAssignFlashes();
		return $this->getAjaxBlocksResponse();
	}

	/**
	 * @return Response   json encoded array of ajax blocks
	 */
	protected function getAjaxBlocksResponse() {
		$retArr = array();

		$this->prepareGlobalTwigVariables();

		// we have to assign global variables, which are known in Twig, because they are missing now...
		$app = array(
			'user' => $this->get('security.context')->getToken()->getUser(),
			'request' => $this->getRequest(),
		);
		$this->assign('app', $app);


		foreach ($this->getAjaxBlocks() as $blockId => $arr) {
			$template = $this->get('twig')->loadTemplate($arr['template']);
			$html = $template->renderBlock($arr['blockId'], $this->twigArr);
			$retArr['block--'.$blockId] = $html;
		}

		if (isset($retArr['block--title']) && isset($retArr['block--additionalTitle'])) {
			$retArr['block--title'] = $retArr['block--title'].' '.$retArr['block--additionalTitle'];
			unset($retArr['block--additionalTitle']);
		}

		if (in_array($this->getRequest()->get('_route'), array(
			'historyOfConnectedDevices',
			'profilesOfConnectedDevices',
			'_home',
		))) {
			$treeColumns = true;
		} else {
			$treeColumns = false;
		}
		$return = array(
			'snippets' => $retArr,
			'redirect' => '',
			'referer'  => $this->getRequest()->server->get('HTTP_REFERER'),
			'route'    => $this->getRequest()->get('_route'),
			'treeColumns' => $treeColumns,
			'historyHref' => isset($this->twigArr['historyHref']) ? $this->twigArr['historyHref'] : "",
			'dump'     => isset($this->twigArr['dump']) ? $this->twigArr['dump'] : "",
		);

		return new Response(json_encode($return));
	}

}
