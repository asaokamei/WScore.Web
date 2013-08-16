<?php
namespace WScore\Web\View\Bootstrap2;

use WScore\Web\View\Message;

/**
 * Class Alert
 *
 * @package WScore\Web\View\Bootstrap2
 * 
 * @cacheable
 */
class Alert
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @var bool
     */
    public $close = true;

    /**
     * @param Message $message
     */
    public function set( $message ) {
        $this->message = $message;
    }

    /**
     * @return null|string
     */
    public function draw() 
    {
        $alert = '';
        if( is_array( $this->message ) ) {
            foreach( $this->message as $message ) {
                $alert .= $this->_draw();
            }
        } else {
            $alert = $this->_draw();
        }
        return $alert;
    }
    /**
     * @return null|string
     */
    private function _draw()
    {
        if( !$this->message ) return null;
        $type = $this->message->type();
        $message = $this->message->message();
        if( $this->close ) {
            $close = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        } else {
            $close = null;
        }
        $alert = "<div class=\"alert alert-{$type}\">
          {$close}
          {$message}
        </div>\n";
        
        return $alert;
    }
}