<?php
namespace WScore\tests\Respond\test;

use WScore\Web\Respond\Dispatch;

class DispatchTester extends Dispatch
{
    public function __construct()
    {
        parent::__construct( __DIR__ );
        $routes = array(
            '/matched/:id' => array(),
            '/matched' => array(),
            '/other'   => array( 'render' => 'matched' ),
            '*' => array(),
        );
        $this->setRoute( $routes );
    }

}