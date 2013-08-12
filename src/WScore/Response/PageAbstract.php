<?php
namespace WScore\Response;

abstract class PageAbstract implements ResponsibleInterface, ResponseInterface
{
    use ResponsibleTrait;
    use ResponseTrait;

    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @return ResponseInterface|null|bool
     */
    public function respond()
    {
        $method = 'on' . ucwords( $this->request->method );
        if( !method_exists( $this, $method ) ) {
            return $this->invalidMethod();
        }
        $result = $this->$method( $this->request->data );
        if( $result === false ) {
            return null;
        }
        return $this;
    }

    /**
     * experimental support for http's options method.
     */
    public function onOptions()
    {
        $reflect = new \ReflectionClass( $this );
        $methods = $reflect->getMethods();
        $options = array();
        foreach( $methods as $method ) {
            $name = $method->getName();
            if( substr( $name, 0, 2 ) === 'on' ) {
                $options[] = strtoupper( substr( $name, 2 ) );
            }
        }
        $allow = implode( ', ', $options );
        $this->setHeader( 'ALLOW', $allow );
        return ;
    }

    /**
     * experimental support for http's head method.
     *
     * @param array $data
     * @return $this
     */
    public function onHead( $data=array() )
    {
        if( !method_exists( $this, 'onGet' ) && !$this->onGet( $data ) ) {
            return $this->invalidMethod();
        }
        $this->setContent( null );
        return $this;
    }

    /**
     * overwrite this method. or returns invalid method error.
     *
     * @param array $data
     * @return $this
     */
    public function onGet( $data=array() ) {
        return $this->invalidMethod();
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