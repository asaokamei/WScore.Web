<?php
namespace WScore\Web\Respond;

use WScore\Web\Request;
use WScore\Web\Respond\RespondInterface;
use WScore\Web\Response;

abstract class PageAbstract extends Response implements RespondInterface
{
    const RELOAD_SELF       = true;
    const JUMP_TO_APP_ROOT  = '';
    const RENDER_PAGE       = null;
    const RENDER_NOTHING    = false;
    
    /**
     * @var Request
     */
    public $request;

    /**
     * @var array
     */
    public $post = array();

    // +----------------------------------------------------------------------+
    //  response object to behave like a page object. 
    // +----------------------------------------------------------------------+
    /**
     * @param Request $request
     * @param array $post
     * @return $this
     */
    public function request( $request, $post=array() )
    {
        $this->request = $request;
        $this->post    = $post;
        return $this;
    }

    /**
     * loads response object (for backward compatibility).
     *
     * @param array $match
     * @param array $post
     * @return $this|null
     */
    public function load( $match=array(), $post=array() )
    {
        if( isset( $match[ 'render' ] ) ) {
            $this->request->appInfo = $match[ 'render' ];
        }
        // prepare post data.
        $this->post   = array_merge( $this->post, $post );
        // get the response result. 
        $result = $this->respond( $match );
        if( $result === null ) {
            return null;
        }
        if( $result === self::RENDER_NOTHING ) {
            return null;
        }
        if( $result === self::JUMP_TO_APP_ROOT ) {
            $this->loadAppRoot();
        }
        elseif( $result === self::RELOAD_SELF ) {
            $this->reload();
        }
        elseif( is_string( $result ) ) {
            $this->jumpTo( $result );
        }
        elseif( is_array( $result ) ) {
            $this->assign( $result );
        }
        return $this;
    }

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
        $result = self::RENDER_NOTHING;
        if( method_exists( $this, $method ) ) {
            $result = $this->$method( $match, $this->post );
        }
        if( $result === self::RENDER_NOTHING ) {
            return null;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set( $name, $value ) {
        $this->data[ $name ] = $value;
    }
    // +----------------------------------------------------------------------+
    //  configure response. 
    // +----------------------------------------------------------------------+
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
    // +----------------------------------------------------------------------+
}