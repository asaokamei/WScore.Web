<?php
namespace WScore\Resource;

class Resource implements ResponsibilityInterface
{
    use ResponsibilityTrait;

    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @param array $match
     * @return ResponseInterface|null|bool
     */
    public function respond( $match = array() )
    {
    }

    /**
     * @return $this
     */
    public function instantiate() {}

}