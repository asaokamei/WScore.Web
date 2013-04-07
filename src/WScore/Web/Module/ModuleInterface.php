<?php
namespace WScore\Web\Module;

interface ModuleInterface
{
    /**
     * call this method before load.
     *
     * @param ModuleAbstract $front
     * @param string $appUrl
     */
    public function pre_load( $front, $appUrl );

    /**
     * call this method after load.
     * @param ModuleAbstract $front
     */
    public function post_load( $front );

    /**
     * sets routes to match.
     *
     * @param array $route
     * @return self
     */
    public function setRoute( $route );

    /**
     * @param array $post
     * @return self
     */
    public function with( $post );

    /**
     * @param string $method
     * @return self
     */
    public function on( $method );
    
    /**
     * Loads response if pathinfo matches with routes.
     *
     * @param string $pathInfo
     * @return null|\WScore\Web\Http\Response
     */
    public function load( $pathInfo );

}