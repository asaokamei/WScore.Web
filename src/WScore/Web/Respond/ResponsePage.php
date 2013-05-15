<?php
namespace WScore\Web\Respond;
use WScore\Web\Request;

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
     * @var Request|null
     */
    public $request = null;

    /**
     * @var array
     */
    public $post = array();

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
            $this->statusCode = 405;
            return $this;
        }
        $result = $this->$method( $match, $this->post );
        if( $result === self::RENDER_NOTHING ) {
            return null;
        }
        return $this;
    }

    /**
     * set when input values are invalid to process request.
     *
     * @param string $alert
     */
    public function invalidParameter( $alert='' ) {
        $this->statusCode = 422;
        if( $alert ) $this->setHeader( 'alert', $alert );
    }

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
        $this->statusCode = 302;
        $this->setHeader( 'Location', $uri );
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

    /**
     * responds to a request with old style.
     * for backward compatibility.
     *
     * @param array $match
     * @param array $post
     * @return $this|null
     */
    public function load( $match, $post = array() )
    {
        // TODO: Implement load() method.
    }
}