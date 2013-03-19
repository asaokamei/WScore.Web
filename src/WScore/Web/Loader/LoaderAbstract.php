<?php
namespace WScore\Web\Loader;

abstract class LoaderAbstract implements LoaderInterface
{
    /**
     * @Inject
     * @var \WScore\Web\Router
     */
    protected $router;

    /** @var \WScore\Web\FrontMC */
    protected $front;

    /** @var string */
    protected $appUrl;

    /**
     * this method should be called from front-end dispatcher.
     *
     * @param \WScore\Web\FrontMC $front
     * @param string $appUrl
     */
    public function pre_load( $front, $appUrl )
    {
        $this->front = $front;
        $this->appUrl = $appUrl;
    }
    
    /**
     * Loads response if pathinfo matches with routes.
     *
     * @param string $pathInfo
     * @return null|string
     */
    public function load( $pathInfo )
    {
    }

    /**
     * sets routes to match.
     *
     * @param array $route
     * @return mixed
     */
    public function setRoute( $route )
    {
        $this->router->set( $route );
    }

    /**
     * call this method after load.
     */
    public function post_load()
    {
    }
}