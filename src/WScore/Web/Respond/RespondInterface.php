<?php
namespace WScore\Web\Respond;

use WScore\Web\Request;

interface RespondInterface
{
    /**
     * @param RespondInterface $app
     * @return mixed
     */
    public function prepare( $app );
    
    /**
     * sets request info.
     * 
     * @param Request $request
     * @param array $post
     * @return $this 
     */
    public function request( $request, $post=array() );

    /**
     * responds to a request.
     * returns null if there is no response.
     *
     * @param array $match
     * @return $this|null
     */
    public function respond( $match=array() );

    /**
     * responds to a request with old style. 
     * for backward compatibility.
     * 
     * @param array $match
     * @param array $post
     * @return $this|null
     */
    public function load( $match, $post=array() );
}