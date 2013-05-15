<?php
namespace WScore\Web\Respond;

/**
 * Class ResponseInterface
 * 
 * All Respond::respond returns ResponseInterface.
 *
 * @package WScore\Web\Respond
 */
interface ResponseInterface
{
    public function setHeader( $name, $value );
    public function setContent( $content );
    public function setStatus( $status );
    public function set( $name, $value );
    public function get( $name=null );
    public function assign( $data );
    public function setRenderer( $render );
    public function render();
}
