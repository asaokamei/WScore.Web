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
     * @return $this
     */
    public function setContent( $content ) {
        $this->content = $content;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus( $status ) {
        $this->statusCode = $status;
        return $this;
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
        return $this;
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
     * @return TemplateInterface
     */
    public function getRenderer() {
        return $this->renderer;
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
        if( $this->renderer && $this->template ) {
            $this->renderer->assign( $this->data );
            $this->renderer->setTemplate( $this->template );
            $this->content = $this->renderer->render();
        }
        return $this;
    }

    /**
     * set when input values are invalid to process request.
     *
     * @param string $alert
     * @return mixed
     */
    public function invalidParameter( $alert = '' ) {
        if( $alert ) $this->set( 'alert', $alert );
        return $this->setStatus( 422 );
    }

    /**
     * method not allowed
     *
     * @return $this
     */
    public function invalidMethod() {
        return $this->setStatus( 405 );
    }

    /**
     * downloads content as a file (or inline).
     *
     * @param string $name
     * @param bool $inline
     * @return $this
     */
    public function download( $name, $inline = true )
    {
        $type = $inline ? 'inline' : 'attachment';
        $this->setHeader( 'Content-Disposition', "{$type}; filename=\"{$name}\"" );
        $this->setHeader( 'Content-Type', 'application/octet-stream' );
        $this->setHeader( 'Content-Transfer-Encoding', 'binary' );
        return $this;
    }

    /**
     * jump to uri. set status to 302 (found).
     *
     * @param string $uri
     * @return $this
     */
    public function jumpTo( $uri )
    {
        $this->setStatus( 302 );
        $this->setHeader( 'Location', $uri );
        // when jump to a url, clear renderer (template) and content.
        $this->renderer = null;
        $this->content  = null;
        $this->template = null;
        return $this;
    }
}