<?php
namespace WScore\Web\Loader;

class Matcher implements LoaderInterface
{
    /**
     * @Inject
     * @var \WScore\Web\Router
     */
    protected $router;
    
    /** @var string */
    protected $name;
    
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
}