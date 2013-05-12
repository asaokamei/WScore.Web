<?php
namespace WScore\Web;

class Request
{
    public $requestURI = '';
    
    public $baseURL = '';
    
    public $pathInfo = '';
    
    public $appURL = '';
    
    public $appInfo = '';
    
    public $method = 'get';
    
    public $what = 'html';

    // +----------------------------------------------------------------------+
    //  setting request parameters. 
    // +----------------------------------------------------------------------+
    /**
     * @param array|string $request
     * @return $this
     */
    public function set( $request )
    {
        if( is_array( $request ) ) {
            $this->setInfo( $request );
        }
        elseif( is_string( $request ) ) {
            $this->setPath( $request );
        }
        
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath( $path )
    {
        $this->requestURI = $this->baseURL  = $this->appURL  = $this->pathInfo = $this->appInfo = $path;
        return $this;
    }

    /**
     * @param array $info
     * @return $this
     */
    public function setInfo( $info )
    {
        $this->requestURI = $info[ 'requestURI' ];
        $this->method     = $info[ 'method' ];
        $this->baseURL    = $this->appURL  = $info[ 'baseURL' ];
        $this->pathInfo   = $this->appInfo = $info[ 'pathInfo' ];
        if( isset( $info[ 'what' ] ) ) $this->what = $info[ 'what' ];
        return $this;
    }
    // +----------------------------------------------------------------------+
}