<?php
namespace WScore\Response;

use \WScore\Response\ResponsibleInterface;

trait ResponsibleTrait
{
    /**
     * @var ResponsibleInterface
     */
    public $parent;

    /**
     * @var \WScore\Response\Request
     */
    public $request = null;

    /**
     * @param ResponsibleInterface $parent
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
     * @return ResponsibleInterface
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
     * @return ResponsibleInterface
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return $this
     */
    public function instantiate() {
        return $this;
    }

}
