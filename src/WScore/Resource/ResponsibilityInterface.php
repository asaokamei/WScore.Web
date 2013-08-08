<?php
namespace WScore\Resource;

/**
 * Class ResponsibilityInterface
 *
 * @package WScore\Resource
 */
interface ResponsibilityInterface
{
    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @param array $match
     * @return ResponseInterface|null
     */
    public function respond( $match=array() );

    /**
     * sets request info.
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest( $request );

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param ResponsibilityInterface $parent
     * @return $this
     */
    public function setParent( $parent );

    /**
     * @return ResponsibilityInterface
     */
    public function getParent();

    /**
     * returns root of all Respond objects.
     *
     * @return ResponsibilityInterface
     */
    public function getRoot();

    /**
     * @return $this
     */
    public function instantiate();
}