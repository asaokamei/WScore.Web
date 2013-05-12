<?php
namespace WScore\Web\Respond;

use WScore\Web\Request;

abstract class RespondAbstract implements RespondInterface
{
    /**
     * @var RespondInterface
     */
    public $app;
    
    /**
     * @var Request|null
     */
    public $request = null;

    /**
     * @var array
     */
    public $post = array();

    /**
     * @param RespondInterface $app
     * @return mixed|void
     */
    public function prepare( $app ) {
        $this->app = $app;
    }
    
    /**
     * sets request info.
     *
     * @param Request $request
     * @param array   $post
     * @return $this
     */
    public function request( $request, $post = array() )
    {
        $this->request = $request;
        $this->post    = $post;
        return $this;
    }

    /**
     * responds to a request with old style.
     * for backward compatibility.
     *
     * @param array $match
     * @param array $post
     * @return $this|null
     */
    public function load( $match, $post = array() )
    {
        $this->post = array_merge( $this->post, $post );
        return $this->respond( $match );
    }

}