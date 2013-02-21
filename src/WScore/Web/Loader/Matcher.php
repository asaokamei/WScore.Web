<?php
namespace WScore\Web\Loader;

class Matcher extends LoaderAbstract
{
    
    /**
     * Loads response if pathinfo matches with routes.
     *
     * @param string $pathInfo
     * @return null|string
     */
    public function load( $pathInfo )
    {
        return $this->router->match( $pathInfo );
    }

}