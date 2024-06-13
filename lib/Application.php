<?php
namespace OCA\Checkin\AppInfo;

use OCP\AppFramework\App;
use OCP\IL10N;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('checkin', $urlParams);

        $container = $this->getContainer();

        $container->registerService('L10N', function($c) {
            return $c->query(IL10N::class)->get('checkin');
        });
        
    }
}
