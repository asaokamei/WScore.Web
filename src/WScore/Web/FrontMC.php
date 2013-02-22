<?php
namespace WScore\Web;

use \WScore\DiContainer\ContainerInterface;

class FrontMcNotFoundException extends \Exception {}

/**
 * simple front-end mini-controller.
 */
class FrontMC
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    public $container;

    /**
     * @Inject
     * @var \WScore\Web\Http\Request
     */
    public $request;

    /** @var Loader\LoaderInterface[] */
    public $loaders = array();

    /** @var string  */
    public $pathInfo;
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param array|string $config
     * @return \WScore\Web\FrontMC
     */
    public function pathInfo( $config )
    {
        if( is_array( $config ) ) {
            $this->pathInfo = $this->request->getPathInfo( $config );
        }
        elseif( is_string( $config ) ) {
            $this->pathInfo = $config;
        }
        return $this;
    }

    /**
     * @throws FrontMcNotFoundException
     * @return \WScore\Web\Http\Response
     */
    public function run()
    {
        if( empty( $this->loaders ) ) {
            throw new FrontMcNotFoundException( 'no loaders.' );
        }
        foreach( $this->loaders as $loader ) {
            $loader->pre_load( $this );
            $response = $loader->load( $this->pathInfo );
            $loader->post_load( $this );
            if( $response ) return $response;
        }
        throw new FrontMcNotFoundException( 'file not found.' );
    }
}