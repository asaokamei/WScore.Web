<?php
namespace WScore\Response;

use \WScore\Response\ResponsibilityInterface;

trait ResponsibilityTrait
{
    /**
     * @var ResponsibilityInterface
     */
    public $parent;

    /**
     * @var \WScore\Response\Request
     */
    public $request = null;

    /**
     * @param ResponsibilityInterface $parent
     * @return $this
     */
    public function setParent( $parent ) {
        $this->parent = $parent;
        return $this;
    }

    /**
     * sets request info.
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest( $request )
    {
        $this->request = $request;
        return $this;
    }
    
    public function getRequest() {
        return $this->request;
    }

    /**
     * @return ResponsibilityInterface
     */
    public function getRoot()
    {
        $root = $this;
        while( isset( $this->parent ) ) {
            $root = $this->parent;
        }
        return $root;
    }

    /**
     * @return ResponsibilityInterface
     */
    public function getParent() {
        return $this->parent;
    }

}
