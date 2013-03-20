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
    
    /** @var string */
    public $baseUrl;
    
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
            $this->baseUrl  = $this->request->getBaseUrl();
        }
        elseif( is_string( $config ) ) {
            $this->pathInfo = $config;
            $this->baseUrl  = '/';
        }
        return $this;
    }

    /**
     * @throws FrontMcNotFoundException
     * @return \WScore\Web\Http\Response|null
     */
    public function run()
    {
        if( empty( $this->loaders ) ) {
            throw new FrontMcNotFoundException( 'no loaders.' );
        }
        foreach( $this->loaders as $appUrl => $loader ) {
            if( is_numeric( $appUrl ) ) {
                $appUrl = null;
            } elseif( strncmp( $this->pathInfo, $appUrl, strlen( $appUrl ) ) ) {
                continue;
            }
            $loader->pre_load( $this, $appUrl );
            $response = $loader->load( $this->pathInfo );
            $loader->post_load( $this );
            if( $response ) return $response;
        }
        return null;
    }
}