<?php
namespace WScore\tests\Respond\test;

use WScore\Web\Respond\Dispatch;

class DispatchTester extends Dispatch
{
    public function __construct()
    {
        parent::__construct( __DIR__ );
        $routes = array(
            '/matched' => array(),
        );
        $this->setRoute( $routes );
    }

}