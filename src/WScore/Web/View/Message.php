<?php
namespace WScore\Web\View;

/**
 * Class Message
 *
 * @package WScore\Web\View
 * 
 * @cacheable
 */
class Message
{
    /**
     * @var string
     */
    public $type = 'info';
    
    /**
     * @var string
     */
    public $message = '';

    /**
     * @return string
     */
    public function message() {
        return $this->message;
    }
    
    /**
     * @return mixed
     */
    public function type() {
        return $this->type;
    }

    /**
     * @param $type
     * @param $message
     */
    public function set( $type, $message )
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * @param $message
     */
    public function success( $message ) {
        $this->set( 'success', $message );
    }

    /**
     * @param $message
     */
    public function notice( $message ) {
        $this->set( 'notice', $message );
    }

    /**
     * @param $message
     */
    public function error( $message ) {
        $this->set( 'error', $message );
    }
}