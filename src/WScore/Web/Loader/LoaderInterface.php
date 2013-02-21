<?php
namespace WScore\Web\Loader;

interface LoaderInterface
{
    /**
     * call this method before load. 
     */
    public function pre_set();
    
    /**
     * sets routes to match.
     * 
     * @param array $route
     * @return mixed
     */
    public function setRoute( $route );
    
    /**
     * Loads response if pathinfo matches with routes. 
     *
     * @param string $pathInfo
     * @return null|string
     */
    public function load( $pathInfo );

    /**
     * returns name of the loader. 
     * 
     * @return string
     */
    public function name();
}