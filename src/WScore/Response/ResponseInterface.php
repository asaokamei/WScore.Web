<?php
namespace WScore\Response;

/**
 * Class ResponseInterface
 *
 * All Respond::respond returns ResponseInterface.
 *
 * @package WScore\Response
 */
interface ResponseInterface
{
    // +----------------------------------------------------------------------+
    //  http response header and contents
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function setHeader( $name, $value );

    /**
     * @param mixed $content
     * @return mixed
     */
    public function setContent( $content );

    /**
     * @param int $status
     * @return mixed
     */
    public function setStatus( $status );

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function set( $name, $value );

    /**
     * @param null|string $name
     * @return mixed
     */
    public function get( $name=null );

    /**
     * @param array $data
     * @return mixed
     */
    public function assign( $data );

    // +----------------------------------------------------------------------+
    //  rendering content
    // +----------------------------------------------------------------------+
    /**
     * @param object $render
     * @return $this
     */
    public function setRenderer( $render );

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate( $template );

    /**
     * @return mixed
     */
    public function render();

    // +----------------------------------------------------------------------+
    //  http response as invalid request 
    // +----------------------------------------------------------------------+
    /**
     * set when input values are invalid to process request.
     *
     * @param string $alert
     */
    public function invalidParameter( $alert='' );

    /**
     * method not allowed
     */
    public function invalidMethod();

    // +----------------------------------------------------------------------+
    //  other useful response cases
    // +----------------------------------------------------------------------+
    /**
     * downloads content as a file (or inline).
     *
     * @param string $name
     * @param bool $inline
     */
    public function download( $name, $inline=true );

    /**
     * jump to uri. set status to 302 (found).
     *
     * @param string $uri
     */
    public function jumpTo( $uri );

    // +----------------------------------------------------------------------+
}
