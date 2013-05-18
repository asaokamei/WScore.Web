<?php
namespace WScore\Web\Http;

class Response
{
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     */
    protected $content_type = 'text/html';
    /**
     * @var int
     */
    protected $status_code = 200;
    /**
     * @var array
     */
    protected $http_headers = array();
    
    /** 
     * @var string ResponseHelper 
     */
    protected $helper = '\WScore\Web\Http\ResponseHelper';

    /**
     * @param string $content
     * @param int    $code
     * @param array  $headers
     */
    public function __construct( $content='', $code=200, $headers=array() )
    {
        $this->setContent( $content );
        $this->setStatusCode( $code );
        $this->http_headers = $headers;
    }
    
    /**
     * sends out response header and content. 
     */
    public function send()
    {
        /** @var $helper ResponseHelper */
        $helper = $this->helper;
        $helper::emitStatus( $this->status_code );

        $mime = $helper::findMimeType( $this->content_type );
        header( 'Content-type: ' . $mime );

        foreach( $this->http_headers as $name => $value ) {
            header( $name . ': ' . $value );
        }
        header( 'Content-Length: ' . strlen( $this->content ) );
        echo $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent( $content ) {
        $this->content = $content;
    }

    /**
     * @param string $type
     */
    public function setContentType( $type ) {
        $this->content_type = $type;
    }

    /**
     * @param int $status_code
     */
    public function setStatusCode( $status_code ) {
        $this->status_code = $status_code;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setHttpHeader( $name, $value=null ) {
        if( is_array( $name ) ) {
            $this->http_headers = array_merge( $this->http_headers, $name );
        } else {
            $this->http_headers[ $name ] = $value;
        }
    }
    
    public function __toString() {
        $this->send();
        return '';
    }
}

