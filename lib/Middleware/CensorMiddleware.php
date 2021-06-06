<?php

namespace OCA\MopidyPlayer\Middleware;

use \OCP\ILogger;
use \OCP\AppFramework\Middleware;


class CensorMiddleware extends Middleware {

    private $logger;

    public function __construct(ILogger $logger){
        $this->logger = $logger;
    }

    /**
     * this replaces "bad words" with "********" in the output
     */
    public function beforeOutput($controller, $methodName, $output){
        $this->logger->warning("Bad words filter...");
        return str_replace('Hello', '********', $output);
    }

}