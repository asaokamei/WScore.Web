<?php
namespace WScore\Response;

class Page implements ResponsibilityInterface, ResponseInterface
{
    use ResponsibilityTrait;
    use ResponseTrait;

    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @param array $match
     * @return ResponseInterface|null|bool
     */
    public function respond( $match = array() )
    {
        $method = $this->request->method ?: 'get';
        $method = 'on' . ucwords( $method );
        if( !method_exists( $this, $method ) ) {
            $this->invalidMethod();
            return $this;
        }
        $this->data = array_merge( $this->data, (array) $this->request );
        $result = $this->$method( $match, $this->request->data );
        if( $result === false ) {
            return null;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function instantiate() {
        return $this;
    }

    /**
     * reload the same page.
     */
    public function reload()
    {
        $uri = $this->request->requestUri;
        $this->jumpTo( $uri );
    }

    /**
     * load (jump to) appRoot.
     */
    public function loadAppRoot()
    {
        $uri = $this->request->requestRoot;
        $this->jumpTo( $uri );
    }

}