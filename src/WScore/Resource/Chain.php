<?php
namespace WScore\Resource;

use WScore\DiContainer\ContainerInterface;

class Chain implements ResponsibilityInterface
{
    use ResponsibilityTrait;

    /**
     * @var array
     */
    public $responders = array();

    /**
     * @var null|ResponseInterface
     */
    public $response = null;

    /**
     * @Inject
     * @var ContainerInterface
     */
    public $service = null;

    // +----------------------------------------------------------------------+
    //  main respond method
    // +----------------------------------------------------------------------+
    public function respond( $match=array() )
    {
        if( empty( $this->responders ) ) {
            throw new \RuntimeException( 'no loaders.' );
        }
        foreach( $this->responders as $info )
        {
            if( $this->loadModule( $info ) === false ) {
                continue;
            }
            $request   = $this->getAppRequest( $info );
            $responder = $this->getResponder( $info );
            $response  = $responder->setParent( $this )->setRequest( $request )->respond( $match );
            if( $response ) $this->response = $response;
        }
        return $this->response;

    }

    /**
     * @param array $info
     * @return Request
     */
    private function getAppRequest( $info )
    {
        if( !$this->request ) return null;
        $appUrl = $info[ 'appUrl' ];
        if( is_null( $appUrl ) || is_numeric( $appUrl ) || is_bool( $appUrl ) ) {
            return clone( $this->request );
        }
        return $this->request->copy( $appUrl );
    }

    // +----------------------------------------------------------------------+
    //  managing Responsibility objects
    // +----------------------------------------------------------------------+
    /**
     * @param ResponsibilityInterface|string $responder
     * @param null|string      $appUrl
     * @return $this
     */
    public function addResponder( $responder, $appUrl=null )
    {
        $info = array(
            'module' => $responder,
            'appUrl' => $appUrl,
            'always' => false,
        );
        if( $appUrl === true ) $info[ 'always' ] = true;
        $this->responders[] = $info;
        return $this;
    }

    /**
     * check if module should be loaded.
     *
     * @param array $info
     * @return bool|string
     */
    private function loadModule( $info )
    {
        $appUrl = $info[ 'appUrl' ];
        $always = $info[ 'always' ];
        if( $this->response && !$always ) {
            // if response is set, then skip subsequent responsibilities unless $always is true.
            return false;
        }
        if( is_null( $appUrl ) || is_numeric( $appUrl ) || is_bool( $appUrl ) ) {
            // load module if it's just a simple array module entry.
            return true;
        }
        if( $this->request->match( $appUrl ) ) {
            // ignore the module with appUrl which does not match with pathInfo.
            return true;
        }
        return false;
    }

    /**
     * @param array $info
     * @return ResponsibilityInterface
     */
    private function getResponder( $info )
    {
        if( is_string( $info[ 'module' ] ) ) {
            return $this->service->get( $info[ 'module' ] );
        }
        return $info[ 'module' ];
    }

    /**
     * @return $this|void
     */
    public function instantiate()
    {
        if( empty( $this->responders ) ) return $this;
        foreach( $this->responders as $key => $info ) {
            $responder = $this->getResponder( $info );
            $responder->instantiate();
            $this->responders[ $key ][ 'module' ] = $responder;
        }
        return $this;
    }

}