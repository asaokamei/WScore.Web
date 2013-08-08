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
     * experimental support for http's options method.
     *
     * @param array $match
     */
    public function onOptions( $match=array() )
    {
        $reflect = new \ReflectionClass( $this );
        $methods = $reflect->getMethods();
        $options = array();
        foreach( $methods as $method ) {
            if( substr( $method, 0, 2 ) === 'on' ) {
                $options[] = strtoupper( substr( $method, 2 ) );
            }
        }
        $allow = implode( ', ', $options );
        $this->setHeader( 'ALLOW', $allow );
        return ;
    }

    /**
     * experimental support for http's head method.
     *
     * @param array $match
     * @return $this
     */
    public function onHead( $match=array() )
    {
        if( !method_exists( $this, 'onGet' ) && !$this->onGet( $match ) ) {
            return $this->invalidMethod();
        }
        $this->setContent( null );
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