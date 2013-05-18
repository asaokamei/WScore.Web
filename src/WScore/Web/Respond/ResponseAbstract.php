<?php
namespace WScore\Web\Respond;

use WScore\Template\TemplateInterface;

/**
 * Class ResponseAbstract
 * 
 * @package WScore\Web\Respond
 */
abstract class ResponseAbstract implements ResponseInterface
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
    //  setting data and contents
    // +----------------------------------------------------------------------+
    /**
     * @param int $status
     */
    public function setStatus( $status ) {
        $this->statusCode = $status;
    }
    /**
     * @param string $name
     * @param mixed $value
     */
    public function setHeader( $name, $value ) {
        $this->headers[ $name ] = $value;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set( $name, $value ) {
        $this->data[ $name ] = $value;
    }

    /**
     * @param null|string $name
     * @return mixed|null
     */
    public function get( $name=null ) {
        if( is_null( $name ) ) return $this->data;
        if( array_key_exists( $name, $this->data ) ) {
            return $this->data[ $name ];
        }
        return null;
    }
    
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
    
    // +----------------------------------------------------------------------+
    //  setting rendering
    // +----------------------------------------------------------------------+
    /**
     * @param $content
     * @return $this
     */
    public function setContent( $content ) {
        $this->content = $content;
        return $this;
    }
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
        if( $this->renderer ) {
            $this->content = $this->renderer->render();
        }
        return $this;
    }
    // +----------------------------------------------------------------------+

}
