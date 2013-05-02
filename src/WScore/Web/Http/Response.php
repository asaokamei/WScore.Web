<?php
namespace WScore\Web\Http;

class Response
{
    public $content;
    protected $content_type = 'text/html';
    protected $status_code = 200;
    protected $http_headers = array();
    /** @var string ResponseHelper */
    protected $helper = '\WScore\Web\Http\ResponseHelper';

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
        echo $this->content;
    }

    public function setContent( $content ) {
        $this->content = $content;
    }

    public function setContentType( $type ) {
        $this->content_type = $type;
    }

    public function setStatusCode( $status_code ) {
        $this->status_code = $status_code;
    }

    public function setHttpHeader( $name, $value ) {
        $this->http_headers[ $name ] = $value;
    }
}

