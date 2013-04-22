<?php
namespace WScore\Web\Module;

abstract class ModuleAbstract implements ModuleInterface
{
    /**
     * @Inject
     * @var \WScore\Web\Router
     */
    public $router;

    /** 
     * @var \WScore\Web\WebApp 
     */
    public $front;

    /**
     * @var string
     */
    public $appRoot;
    
    /** 
     * @var string 
     */
    public $appUrl;

    /**
     * @var string
     */
    public $pathInfo;

    /**
     * @var string
     */
    public $method;
    
    /**
     * @var array
     */
    public $post = array();

    // +----------------------------------------------------------------------+
    //  public interfaces
    // +----------------------------------------------------------------------+
    /**
     * this method should be called from front-end dispatcher.
     *
     * @param ModuleAbstract $front
     * @param string $appUrl
     * @return $this
     */
    public function pre_load( $front, $appUrl )
    {
        $this->front   = $front;
        $this->appUrl  = $appUrl;
        $this->appRoot = $front->appRoot . $appUrl;
        return $this;
    }
    
    /**
     * call this method after load.
     * 
     * @param \WScore\Web\FrontMC $front
     * @return $this
     */
    public function post_load( $front )
    {
        return $this;
    }

    /**
     * sets routes to match.
     *
     * @param array $route
     * @return $this
     */
    public function setRoute( $route )
    {
        $this->router->set( $route );
        return $this;
    }

    /**
     * @param array $post
     * @return $this
     */
    public function with( $post )
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function on( $method ) 
    {
        $this->method = $method;
        return $this;
    }
    // +----------------------------------------------------------------------+
}
