<?php
namespace WScore\Response;

use WScore\Template\TemplateInterface;

trait ResponseTrait
{
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

    /**
     * template file to be rendered.
     *
     * @var string
     */
    public $template;

    // +----------------------------------------------------------------------+
    //  setting data and contents
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function setHeader( $name, $value ) {
        $this->headers[ $name ] = $value;
    }

    /**
     * @param mixed $content
     * @return mixed
     */
    public function setContent( $content ) {
        $this->content = $content;
        return $this;
    }

    /**
     * @param int $status
     * @return mixed
     */
    public function setStatus( $status ) {
        $this->statusCode = $status;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function set( $name, $value ) {
        $this->data[ $name ] = $value;
    }

    /**
     * @param null|string $name
     * @return mixed
     */
    public function get( $name=null )
    {
        if( is_null( $name ) ) return $this->data;
        if( array_key_exists( $name, $this->data ) ) {
            return $this->data[ $name ];
        }
        return null;
    }

    /**
     * @param array $data
     * @return mixed
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
     * @param object $render
     * @return $this
     */
    public function setRenderer( $render )
    {
        $this->renderer = $render;
        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate( $template )
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        if( $this->renderer ) {
            $this->renderer->assign( $this->data );
            $this->content = $this->renderer->render();
        }
        return $this;
    }

    /**
     * set when input values are invalid to process request.
     *
     * @param string $alert
     */
    public function invalidParameter( $alert = '' ) {
        $this->setStatus( 422 );
        if( $alert ) $this->set( 'alert', $alert );
    }

    /**
     * method not allowed
     */
    public function invalidMethod() {
        $this->setStatus( 405 );
    }

    /**
     * downloads content as a file (or inline).
     *
     * @param string $name
     * @param bool $inline
     */
    public function download( $name, $inline = true )
    {
        $type = $inline ? 'inline' : 'attachment';
        $this->setHeader( 'Content-Disposition', "{$type}; filename=\"{$name}\"" );
        $this->setHeader( 'Content-Type', 'application/octet-stream' );
        $this->setHeader( 'Content-Transfer-Encoding', 'binary' );
    }

    /**
     * jump to uri. set status to 302 (found).
     *
     * @param string $uri
     */
    public function jumpTo( $uri )
    {
        $this->setStatus( 302 );
        $this->setHeader( 'Location', $uri );
        // when jump to a url, clear renderer (template) and content.
        $this->renderer = null;
        $this->content  = null;
    }
}