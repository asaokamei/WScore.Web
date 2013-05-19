<?php
namespace WScore\Web\Respond;
use WScore\Web\Respond\Request;

/**
 * Class Response
 *
 * a class to be extended by Page object. 
 * it is a ResponseInterface with Respond. 
 *
 * @package WScore\Web\Respond
 */
class ResponsePage extends ResponseAbstract implements RespondInterface
{
    /**
     * @var RespondInterface
     */
    public $app;

    /**
     * @var Request|null
     */
    public $request = null;

    /**
     * @var array
     */
    public $post = array();

    // +----------------------------------------------------------------------+
    //  for responding to a request. 
    // +----------------------------------------------------------------------+
    /**
     * responds to a request.
     * returns null if there is no response.
     *
     * @param array $match
     * @return $this|null
     */
    public function respond( $match=array() )
    {
        $method = $this->request->method ?: 'get';
        $method = 'on' . ucwords( $method );
        if( !method_exists( $this, $method ) ) {
            $this->invalidMethod();
            return $this;
        }
        $result = $this->$method( $match, $this->post );
        if( $result === self::RENDER_NOTHING ) {
            return null;
        }
        if( is_array( $result ) ) {
            $this->data = array_merge( $this->data, $result );
        }
        return $this;
    }

    /**
     * @param RespondInterface $app
     * @return mixed
     */
    public function prepare( $app )
    {
        $this->app = $app;
        return $this;
    }

    /**
     * sets request info.
     *
     * @param Request $request
     * @param array   $post
     * @return $this
     */
    public function request( $request, $post = array() )
    {
        $this->request = $request;
        $this->post    = $post;
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  error conditions.
    // +----------------------------------------------------------------------+
    /**
     * set when input values are invalid to process request.
     *
     * @param string $alert
     */
    public function invalidParameter( $alert='' ) {
        $this->setStatus( 422 );
        if( $alert ) $this->set( 'alert', $alert );
    }

    /**
     * method not allowed
     */
    public function invalidMethod() {
        $this->setStatus( 405 );
    }

    // +----------------------------------------------------------------------+
    //  re-location and downloading 
    // +----------------------------------------------------------------------+
    /**
     * downloads content as a file (or inline).
     *
     * @param string $name
     * @param bool $inline
     */
    public function download( $name, $inline=true )
    {
        $type = $inline ? 'inline' : 'attachment';
        $this->setHeader( 'Content-Disposition', "{$type}; filename=\"{$name}\"" );
        $this->setHeader( 'Content-Type', 'application/octet-stream' );
        $this->setHeader( 'Content-Transfer-Encoding', 'binary' );
    }

    /**
     * reload the same page.
     */
    public function reload()
    {
        $uri = $this->request->requestURI;
        $this->jumpTo( $uri );
    }

    /**
     * load (jump to) appRoot.
     */
    public function loadAppRoot() {
        $uri = $this->request->appURL;
        $this->jumpTo( $uri );
    }

    /**
     * jump to uri. set status to 302 (found).
     *
     * @param string $uri
     */
    public function jumpTo( $uri ) {
        $this->setStatus( 302 );
        $this->setHeader( 'Location', $uri );
    }

    // +----------------------------------------------------------------------+
    //  methods for RespondInterface. should use traits to inherit them. 
    // +----------------------------------------------------------------------+
    /**
     * @return RespondInterface
     */
    public function retrieveRoot()
    {
        $root = $this;
        while( isset( $this->app ) ) {
            $root = $this->app;
        }
        return $root;
    }

    /**
     * @return RespondInterface
     */
    public function retrieveApp() {
        return $this->app;
    }

    /**
     * @return null|Request
     */
    public function retrieveRequest() {
        return $this->request;
    }
    // +----------------------------------------------------------------------+
}