<?php
namespace WScore\Web;

use \WScore\DiContainer\ContainerInterface;

class FrontMcNotFoundException extends \Exception {}

/**
 * todo: set multiple routes and loop through them.
 *
 * simple front-end mini-controller.
 * mostly from PerfectPHP book.
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

    /**
     */
    public function __construct()
    {
    }

    /**
     * @param string $pathInfo
     * @throws FrontMcNotFoundException
     * @return \WScore\Web\Http\Response
     */
    public function run( $pathInfo=null )
    {
        if( !$pathInfo ) $pathInfo = $this->request->getPathInfo();
        foreach( $this->loaders as $loader ) {
            $loader->pre_set( $this );
            $response = $loader->load( $pathInfo );
            if( $response ) return $response;
        }
        throw new FrontMcNotFoundException( '' );
    }
}