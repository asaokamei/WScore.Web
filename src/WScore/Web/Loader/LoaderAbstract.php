<?php
namespace WScore\Web\Loader;

class LoaderAbstract implements LoaderInterface
{
    /**
     * @Inject
     * @var \WScore\Web\Router
     */
    protected $router;

    /** @var string */
    protected $name;

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
     * returns name of the loader.
     *
     * @return string
     */
    public function name()
    {
        if( isset( $this->name ) ) return $this->name;
        $class = get_called_class();
        return substr( $class, strrpos( $class, '\\' ) + 1 );
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