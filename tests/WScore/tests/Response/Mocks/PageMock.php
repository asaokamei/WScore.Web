<?php
namespace WScore\tests\Response\Mocks;

use WScore\Response\PageAbstract;

class PageMock extends PageAbstract
{
    public $onList = array();

    function onGet( $data=array() )
    {
        $this->onList[] = 'onGet';
        $this->set( 'onGet', 'onGet was here.' );
    }

    function onTest( $data=array() )
    {
        $this->onList[] = 'onTest';
    }
}