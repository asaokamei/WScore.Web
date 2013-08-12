<?php
namespace WScore\tests\Response\Mocks;

use WScore\Response\DispatchAbstract;

class DispatchMe extends DispatchAbstract
{
    public function __construct( $viewDir=null )
    {
        parent::__construct( __DIR__ );
        $this->setRoute( array(
            '/ViewOnly' => [],
            '/PageMe'   => [],
        ));
    }
}