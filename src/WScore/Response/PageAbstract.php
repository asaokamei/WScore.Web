<?php
namespace WScore\Response;

abstract class PageAbstract implements ModuleInterface, ResponseInterface
{
    use ModuleTrait;
    use ResponseTrait;

    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @param array $match
     * @return ResponseInterface|null|bool
     */
    public function respond( $match=array() )
    {
        $method = 'on' . ucwords( $this->request->getInfo( 'requestMethod' ) );
        if( !method_exists( $this, $method ) ) {
            return $this->invalidMethod();
        }
        $result = $this->$method( $match, $this->request->data );
        if( $result === false ) {
            return null;
        }
        elseif( is_array( $result ) ) {
            $this->assign( $result );
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
     * reload the same page.
     *
     * @return $this
     */
    public function reload()
    {
        $uri = $this->request->getInfo( 'requestRoot' ) . $this->request->getInfo( 'requestUri' );
        return $this->jumpTo( $uri );
    }

    /**
     * load (jump to) appRoot.
     *
     * @return $this
     */
    public function loadAppRoot()
    {
        $uri = $this->request->getInfo( 'requestRoot' );
        return $this->jumpTo( $uri );
    }


}