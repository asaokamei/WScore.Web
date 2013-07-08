<?php
namespace WScore\Web\Respond;

use WScore\Web\Respond\Request;
use WScore\Web\Respond\Response;
use WScore\DiContainer\ContainerInterface;

class Chain extends RespondAbstract implements RespondInterface
{
    /**
     * @var array
     */
    public $responders = array();
    
    /**
     * @var null|Response
     */
    public $response = null;

    /**
     * @Inject
     * @var ContainerInterface
     */
    public $service = null;

    /**
     * @param RespondInterface|string $responder
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
     * responds to a request.
     * returns null if there is no response.
     *
     * @param array $match
     * @throws \RuntimeException
     * @return $this|null
     */
    public function respond( $match=array() )
    {
        if( empty( $this->responders ) ) {
            throw new \RuntimeException( 'no loaders.' );
        }
        foreach( $this->responders as $info )
        {
            $appUrl = $this->loadModule( $info );
            if( $appUrl === false ) {
                continue;
            }
            $request   = $this->getAppRequest( $appUrl );
            $responder = $this->getResponder( $info );
            $responder->prepare( $this );
            $response  = $responder->request( $request, $this->post )->respond( $match );
            if( $response ) $this->response = $response;
        }
        return $this->response;
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
        $pathInfo = $this->request->appInfo;
        if( $this->response && !$always ) {
            // if response is back, then skip subsequent modules unless $always is true. 
            return false;
        }
        if( is_null( $appUrl ) || is_numeric( $appUrl ) || is_bool( $appUrl ) ) {
            // load module if it's just a simple array module entry. 
            return true;
        }
        if( $this->request->match( $appUrl ) ) {
            // ignore the module with appUrl which does not match with pathInfo. 
            return $appUrl;
        }
        return false;
    }

    /**
     * @param string $appUrl
     * @return Request
     */
    private function getAppRequest( $appUrl ) 
    {
        if( !$this->request ) return null;
        $request = clone( $this->request );
        if( is_numeric( $appUrl ) || is_bool( $appUrl ) ) return $request;
        $request->modAppUrl( $appUrl );
        return $request;
    }

    /**
     * @param array $info
     * @return RespondInterface
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