<?php
namespace WScore\tests\Respond\test\Page;

use WScore\Web\Respond\PageAbstract;

class Matched extends PageAbstract
{
    public function onGet( $match )
    {
        $this->set( 'method', 'get' );
        $this->set( 'I-am', 'Matched' );
        
        if( isset( $match[ 'id' ] ) ) {
            $this->set( 'id', $match[ 'id' ] );
        }
    }
}