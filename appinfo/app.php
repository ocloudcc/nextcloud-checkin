<?php
namespace OCA\Checkin\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('checkin', $urlParams);
        $container = $this->getContainer();
        $container->registerService('PageController', function(IAppContainer $c) {
            return new \OCA\Checkin\Controller\PageController(
                $c->getAppName(),
                $c->query('Request'),
                $c->query('UserSession'),
                $c->query('DatabaseConnection')
            );
        });
    }
}

