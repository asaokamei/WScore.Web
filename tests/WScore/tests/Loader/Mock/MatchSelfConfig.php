<?php
namespace WScore\tests\Loader\Mock;

class MatchSelfConfig extends \WScore\Web\Loader\Matcher
{
    public function __construct()
    {
        $routes = array(
            '/self' => array( 'found' => 'self' ),
        );
        $this->setRoute( $routes );
    }
}