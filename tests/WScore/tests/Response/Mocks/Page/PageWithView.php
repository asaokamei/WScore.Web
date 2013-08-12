<?php
namespace WScore\tests\Response\Mocks\Page;

use WScore\Response\PageAbstract;
use WScore\Response\ResponseInterface;

class PageWithView extends PageAbstract implements ResponseInterface
{
    public function onGet( $data=array() )
    {
        $this->set( 'onGet', 'PageWithView was here.' );
    }
}