<?php
namespace WScore\Response;

use WScore\DiContainer\ContainerInterface;

abstract class ChainAbstract implements ResponsibleInterface
{
    use ResponsibleTrait;

    /**
     * @var array
     */
    public $responsibles = array();

    /**
     * @var null|ResponseInterface
     */
    public $response = null;

    /**
     * @Inject
     * @var ContainerInterface
     */
    public $service = null;

    // +----------------------------------------------------------------------+
    //  main respond method
    // +----------------------------------------------------------------------+
    /**
     * @return null|ResponseInterface
     * @throws \RuntimeException
     */
    public function respond()
    {
        if( empty( $this->responsibles ) ) {
            throw new \RuntimeException( 'no loaders.' );
        }
        foreach( $this->responsibles as $info )
        {
            if( $this->loadModule( $info ) === false ) {
                continue;
            }
            $request   = $this->getAppRequest( $info );
            $responsible = $this->getResponder( $info );
            $response  = $responsible->setParent( $this )->setRequest( $request )->respond();
            if( $response ) $this->response = $response;
        }
        return $this->response;
    }

    /**
     * @param array $info
     * @return Request
     */
    private function getAppRequest( $info )
    {
        if( !$this->request ) return null;
        if( is_string( $info[ 'appUrl' ] ) ) {
            return $this->request->copy( $info[ 'appUrl' ] );
        }
        return clone( $this->request );
    }

    // +----------------------------------------------------------------------+
    //  managing Responsibility objects
    // +----------------------------------------------------------------------+
    /**
     * @param ResponsibleInterface|string $responder
     * @param null|string      $appUrl
     * @return $this
     */
    public function addResponder( $responder, $appUrl=null )
    {
        $info = array(
            'module' => $responder,
            'appUrl' => $appUrl,
            'always' => false,
        );
        if( $appUrl === true ) $info[ 'always' ] = true;
        $this->responsibles[] = $info;
        return $this;
    }

    /**
     * check if module should be loaded.
     *
     * @param array $info
     * @return bool|string
     */
    private function loadModule( $info )
    {
        $appUrl = $info[ 'appUrl' ];
        $always = $info[ 'always' ];
        if( $this->response && !$always ) {
            // if response is set, then skip subsequent responsibilities unless $always is true.
            return false;
        }
        if( is_string( $appUrl ) && !$this->request->match( $appUrl ) ) {
            // ignore the module with appUrl which does not match with pathInfo.
            return false;
        }
        return true;
    }

    /**
     * @param array $info
     * @return ResponsibleInterface
     */
    private function getResponder( $info ) {
        if( is_string( $info[ 'module' ] ) ) {
            return $this->service->get( $info[ 'module' ] );
        }
        return $info[ 'module' ];
    }

    /**
     * @return $this|void
     */
    public function instantiate()
    {
        if( empty( $this->responsibles ) ) return $this;
        foreach( $this->responsibles as $key => $info ) {
            $responsible = $this->getResponder( $info );
            $responsible->instantiate();
            $this->responsibles[ $key ][ 'module' ] = $responsible;
        }
        return $this;
    }

}