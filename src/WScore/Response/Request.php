<?php
namespace WScore\Response;

/**
 * Class Request
 *
 * @package WScore\Response
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

    public $dataType = 'html';

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
     * @param null|string $path
     * @return Request
     */
    public function copy( $path=null )
    {
        $request = clone( $this );
        $request->modifyUri( $path );
        return $request;
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
    public function type( $what ) {
        $this->dataType = strtolower( $what );
        return $this;
    }

    /**
     * @param string $uri
     * @return $this
     */
    public function uri( $uri )
    {
        $this->requestUri = $uri;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function path( $path ) {
        $this->requestRoot = $path;
        return $this;
    }
    // +----------------------------------------------------------------------+
}