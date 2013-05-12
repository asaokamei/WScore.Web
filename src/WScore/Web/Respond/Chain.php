<?php
namespace WScore\Web\Respond;

use WScore\Web\Request;
use WScore\Web\Response;

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
     * @param RespondInterface $responder
     * @param null|string      $appUrl
     * @return $this
     */
    public function setModule( $responder, $appUrl=null )
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
            /** @var $responder RespondInterface */
            $responder = $info[ 'module' ];
            $request   = $this->getAppRequest( $appUrl );
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
        if( is_numeric( $appUrl ) || is_bool( $appUrl ) ) {
            // load module if it's just a simple array module entry. 
            return true;
        }
        if( strncmp( $pathInfo, $appUrl, strlen( $appUrl ) ) ) {
            // ignore the module with appUrl which does not match with pathInfo. 
            return false;
        }
        return $appUrl;
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
        $request->appURL .= $appUrl;
        $request->appInfo = substr( $request->appInfo, strlen( $appUrl ) );
        return $request;
    }
}