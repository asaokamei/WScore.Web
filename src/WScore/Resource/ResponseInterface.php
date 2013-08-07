<?php
namespace WScore\Resource;

/**
 * Class ResponseInterface
 *
 * All Respond::respond returns ResponseInterface.
 *
 * @package WScore\Resource
 */
interface ResponseInterface
{
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

    /**
     * @param object $render
     * @return mixed
     */
    public function setRenderer( $render );

    /**
     * @return mixed
     */
    public function render();
}
