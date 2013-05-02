<?php
namespace WScore\Web\Auth;

interface AuthInterface
{
    /**
     * @param array $post
     * @return $this
     */
    function with( $post );

    function getAuth();

    /**
     * @return $this
     */
    function logout();

    /**
     * @return bool
     */
    function isLoggedIn();
}
