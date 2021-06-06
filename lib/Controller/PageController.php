<?php
namespace OCA\MopidyPlayer\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\Util;
use OCP\ILogger;

use OCA\MopidyPlayer\AppInfo\Application;

class PageController extends Controller {
	private $userId;
	private $logger;

	public function __construct($AppName, ILogger $logger, IRequest $request, $UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->logger = $logger;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		Util::addScript(Application::APP_ID, 'mopidyplayer-main');
		Util::addStyle(Application::APP_ID, 'style');

		$response = new TemplateResponse('mopidyplayer', 'index');  // templates/index.php

		$csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*');
        $response->setContentSecurityPolicy($csp);

		return $response;
	}

	/**
      * @NoAdminRequired
      * @NoCSRFRequired
      */
	public function btConnect() {
		$device = exec("bluetoothctl devices | awk '{ print $2 }'");
		$result = exec("bluetoothctl connect $device");
        return new JSONResponse([ 'result' => $result ]);
	}

	/**
      * @NoAdminRequired
      * @NoCSRFRequired
      */
	  public function btDisconnect() {
		$device = exec("bluetoothctl devices | awk '{ print $2 }'");
		$result = exec("bluetoothctl disconnect $device");
        return new JSONResponse([ 'result' => $result ]);
	}

}
