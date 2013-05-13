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

    /**
     * @var Http\Request
     */
    public $httpRequest;
    
    // +----------------------------------------------------------------------+
    //  managing url and pathInfo.
    // +----------------------------------------------------------------------+
    /**
     * @Inject
     * @param \WScore\Web\Http\Request $httpRequest
     */
    public function __construct( $httpRequest )
    {
        $this->httpRequest = $httpRequest;
        $this->set( $httpRequest );
    }

    /**
     * @param string $appUrl
     * @return bool
     */
    public function match( $appUrl ) 
    {
        if( strncmp( $this->appInfo, $appUrl, strlen( $appUrl ) ) ) {
            // ignore the module with appUrl which does not match with pathInfo. 
            return false;
        }
        return true;
    }

    /**
     * @param string $appUrl
     */
    public function modAppUrl( $appUrl )
    {
        $this->appURL .= $appUrl;
        $this->appInfo = substr( $this->appInfo, strlen( $appUrl ) );
    }
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
        $this->requestURI = $this->pathInfo = $this->appInfo = $path;
        $this->baseURL    = $this->appURL   =  '';
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