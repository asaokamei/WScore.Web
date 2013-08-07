<?php
namespace WScore\Resource;

/**
 * Class Request
 *
 * @package WScore\Resource
 */
class Request
{
    /**
     * root path of the request uri. such as /dir/to/
     * @var string
     */
    public $requestRoot = '';

    /**
     * request uri for this resource. such as /resources/$id.
     * @var string
     */
    public $requestUri = '';

    /**
     * method, such as get, post, put, delete.
     * @var string
     */
    public $method = 'get';

    public $what = 'html';

    public $data = array();

    // +----------------------------------------------------------------------+
    //  managing url and pathInfo.
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * checks if the given $path matches with the requestUri.
     *
     * @param string $path
     * @return bool
     */
    public function match( $path )
    {
        if( strncmp( $this->requestUri, $path, strlen( $path ) ) ) {
            // ignore the module with appUrl which does not match with pathInfo.
            return false;
        }
        return true;
    }

    /**
     * modifies request{Uri|Root} to be handled by next chain of resource.
     *
     * @param string $path
     */
    public function modifyUri( $path )
    {
        $this->requestRoot .= $path;
        $this->requestUri = substr( $this->requestUri, strlen( $path ) );
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
     * @param string $method
     * @return $this
     */
    public function on( $method ) {
        $this->method = strtolower( $method );
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function with( $data ) {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $what
     * @return $this
     */
    public function what( $what ) {
        $this->what = strtolower( $what );
        return $this;
    }

    /**
     * @param string $uri
     * @return $this
     */
    public function setUri( $uri )
    {
        $this->requestUri = $uri;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath( $path ) {
        $this->requestRoot = $path;
        return $this;
    }

    /**
     * @param array $info
     * @return $this
     */
    public function setInfo( $info )
    {
        if( isset( $info[ 'method'     ] ) ) $this->on( $info[ 'method' ] );
        if( isset( $info[ 'requestURI' ] ) ) $this->setUri( $info[ 'requestURI' ] );
        if( isset( $info[ 'baseURL'    ] ) ) $this->setPath( $info[ 'baseURL' ] );
        if( isset( $info[ 'what'       ] ) ) $this->what( $info[ 'what' ] );
        return $this;
    }
    // +----------------------------------------------------------------------+
}