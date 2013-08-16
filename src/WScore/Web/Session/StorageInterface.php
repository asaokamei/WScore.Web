<?php
namespace WScore\Web\Session;

/**
 * Class SessionInterface
 * manages $_SESSION array.
 *
 * @package WScore\Web\Session
 *
 * API are taken from Symfony 2.
 */
interface StorageInterface
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
     * @param string $config
     * @throws \RuntimeException
     * @return $this
     */
    public function setup( $config );

    /**
     * @param string $name The attribute name
     * @return Boolean true if the attribute is defined, false otherwise
     */
    public function has($name);

    /**
     * @param string $name    The attribute name
     * @param mixed  $default The default value if not found.
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * @param string $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value);

    /**
     * @return array Attributes
     */
    public function all();

    /**
     * @param string $name
     * @return mixed The removed value
     */
    public function remove($name);

    /**
     */
    public function clear();

}