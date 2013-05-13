<?php
namespace WScore\Web\Respond;

use WScore\Template\TemplateInterface;

abstract class PageAbstract extends RespondAbstract
{
    const RENDER_NOTHING    = false;
    
    /**
     * http status code.
     *
     * @var int
     */
    public $statusCode = 200;

    /**
     * http headers
     *
     * @var array
     */
    public $headers = array();

    /**
     * content as a string.
     *
     * @var string
     */
    public $content = '';

    /**
     * data to transfer.
     *
     * @var array
     */
    public $data = array();

    /**
     * renderer to generate content.
     *
     * @var TemplateInterface
     */
    public $renderer;

    // +----------------------------------------------------------------------+
    //  response object to behave like a page object. 
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
     * @param $data
     */
    public function assign( $data )
    {
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $this->data[ $key ] = $val;
            }
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setHeader( $name, $value ) {
        $this->headers[ $name ] = $value;
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
    // +----------------------------------------------------------------------+
    //  setting rendering
    // +----------------------------------------------------------------------+
    /**
     * @param TemplateInterface $render
     * @return $this
     */
    public function setRenderer( $render )
    {
        $this->renderer = $render;
        return $this;
    }

    /**
     * @return $this
     */
    public function render()
    {
        $this->content = $this->renderer->render();
        return $this;
    }
    // +----------------------------------------------------------------------+
}