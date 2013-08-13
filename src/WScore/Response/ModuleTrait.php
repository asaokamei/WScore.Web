<?php
namespace WScore\Response;

use \WScore\Response\ModuleInterface;

trait ModuleTrait
{
    /**
     * @var ModuleInterface
     */
    public $parent;

    /**
     * @var \WScore\Response\Request
     */
    public $request = null;

    /**
     * @param ModuleInterface $parent
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
     * @return ModuleInterface
     */
    public function getRoot()
    {
        $root = $this;
        while( $root->parent ) {
            $root = $this->parent;
        }
        return $root;
    }

    /**
     * @return ModuleInterface
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
