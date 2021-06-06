<?php
declare(strict_types=1);

namespace OCA\MopidyPlayer\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCA\MopidyPlayer\Middleware\CensorMiddleware;
use OCA\MopidyPlayer\Dashboard\DemoWidget;

class Application extends App implements IBootstrap {
    public const APP_ID = 'mopidyplayer';
    /**
     * Define your dependencies in here
     */
    public function __construct(array $urlParams = []){
        parent::__construct(self::APP_ID, $urlParams);

        /* @var IEventDispatcher $eventDispatcher */
        /*$dispatcher = $this->getContainer()->query(IEventDispatcher::class);
        $dispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script(self::APP_ID, 'app');  // adds js/app.js
        });*/
    }

    public function register(IRegistrationContext $context): void {
		/**
         * Middleware
         */
        $context->registerService('CensorMiddleware', function($c){
            return new CensorMiddleware($c->query('\OCP\ILogger'));
        });

        // executed in the order that it is registered
        //$context->registerMiddleware('CensorMiddleware');

        // Register the Dashboard panel
		//$context->registerDashboardWidget(DemoWidget::class);
	}

	public function boot(IBootContext $context): void {
		//$context->getAppContainer()->get(NotesHooks::class)->register();
	}
}