<?php
namespace WScore\Web\Session;

/**
 * Class Manager
 * @package WScore\Web\Session
 *
 * @singleton
 */
class Session implements SessionInterface
{
    protected $started = false;

    public $storage = array();

    /**
     * @param bool $falseStart
     * @throws \RuntimeException
     * @return bool
     */
    public function start( $falseStart=false )
    {
        if( $falseStart ) {
            $this->started = true;
            return true;
        }
        if( $this->isStarted() ) return true;
        if( ini_get( 'session.use_cookies' ) && headers_sent( $file, $line ) ) {
            throw new \RuntimeException(sprintf('headers already been sent by "%s" at line %d.', $file, $line ) );
        }
        if (!session_start() ) {
            throw new \RuntimeException( 'Failed to start the session' );
        }
        $this->storage = & $_SESSION;
        return true;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        if( $this->started ) return true;;
        $started = false;
        if( function_exists( 'session_status' ) && session_status() === \PHP_SESSION_ACTIVE ) $started = true;
        if( isset( $_SESSION ) ) $started = true;
        if( $started ) {
            $this->started = $started;
        }
        return $started;
    }

    /**
     * get session ID.
     *
     * @return string
     */
    public function getId() {
        return session_id();
    }

    /**
     * set the session ID
     *
     * @param string $id
     */
    public function setId( $id ) {
        session_id( $id );
    }

    /**
     * @return mixed The session name.
     */
    public function getName() {
        return session_name();
    }

    /**
     * Sets the session name.
     *
     * @param $name
     * @param string $name
     */
    public function setName( $name ) {
        session_name( $name );
    }

    /**
     * Migrates the current session to a new session id while maintaining all
     * session attributes.
     *
     * @param Boolean $destroy
     * @return bool
     */
    public function migrate( $destroy=false )
    {
        return session_regenerate_id( $destroy );
    }
}