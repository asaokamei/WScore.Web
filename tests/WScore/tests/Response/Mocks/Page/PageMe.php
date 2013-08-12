<?php
namespace WScore\tests\Response\Mocks\Page;

use WScore\Response\PageAbstract;
use WScore\Response\ResponseInterface;

class PageMe extends PageAbstract implements ResponseInterface
{
    public function onGet( $data=array() )
    {
        $this->set( 'onGet', 'PageMe was here.' );
    }

    public function onView()
    {
        $this->set( 'onView', 'PageMe was here.' );
        $this->template = dirname( __DIR__ ) . '/View/ViewOnly.php';
    }
}