<?php
namespace WScore\Web\Loader;

interface LoaderInterface
{
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
     * @return mixed
     */
    public function name();
}