<?php
namespace WScore\Web;

use WScore\Template\TemplateInterface;
use WScore\Web\Http\Response as HttpResponse;

class Response
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
     * @var string
     */
    public $httpResponse = '\WScore\Web\Http\Response';

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

    /**
     * @param null|string $response
     * @return HttpResponse
     */
    public function send( $response=null )
    {
        $response = $response ?: $this->httpResponse;
        $response = new $response( $this->content, $this->statusCode, $this->headers );
        return $response;
    }
    // +----------------------------------------------------------------------+
}