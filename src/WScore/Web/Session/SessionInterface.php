<?php
namespace WScore\Web\Session;

/**
 * Class ManagerInterface
 * manages PHP session.
 *
 * @package WScore\Web\Session
 *
 * API are taken from Symfony 2.
 */
interface SessionInterface
{
    /**
     * @return Boolean True if session started.
     * @throws \RuntimeException If session fails to start.
     */
    public function start();

    /**
     * @return Boolean
     */
    public function isStarted();

    /**
     * @param null $config
     * @return array
     */
    public function storage( $config=null );

    /**
     * Returns the session ID.
     *
     * @return string The session ID.
     */
    public function getId();

    /**
     * Sets the session ID
     *
     * @param string $id
     */
    public function setId( $id );

    /**
     * Returns the session name.
     *
     * @return mixed The session name.
     *
     * @api
     */
    public function getName();

    /**
     * Sets the session name.
     *
     * @param $name
     * @param string $name
     */
    public function setName( $name );

    /**
     * Migrates the current session to a new session id while maintaining all
     * session attributes.
     *
     * @param Boolean $destroy
     * @return bool
     */
    public function migrate( $destroy = false );


}