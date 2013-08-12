<?php
namespace WScore\Response;

/**
 * Class ResponsibilityInterface
 *
 * @package WScore\Response
 */
interface ResponsibleInterface
{
    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @return ResponseInterface|null
     */
    public function respond();

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
     * @param ResponsibleInterface $parent
     * @return $this
     */
    public function setParent( $parent );

    /**
     * @return ResponsibleInterface
     */
    public function getParent();

    /**
     * returns root of all Respond objects.
     *
     * @return ResponsibleInterface
     */
    public function getRoot();

    /**
     * @return $this
     */
    public function instantiate();
}