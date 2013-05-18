<?php
namespace WScore\Web\Respond;

use WScore\Web\Respond\Request;

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
     * @return RespondInterface
     */
    public function retrieveRoot()
    {
        $root = $this;
        while( isset( $this->app ) ) {
            $root = $this->app;
        }
        return $root;
    }
}