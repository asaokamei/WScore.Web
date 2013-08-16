<?php
namespace WScore\Response;

use WScore\DiContainer\ContainerInterface;

abstract class ChainAbstract implements ModuleInterface
{
    use ModuleTrait;

    /**
     * @var array
     */
    public $modules = array();

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
        if( empty( $this->modules ) ) {
            throw new \RuntimeException( 'no loaders.' );
        }
        foreach( $this->modules as $info )
        {
            if( $this->loadModule( $info ) === false ) {
                continue;
            }
            $request   = $this->getAppRequest( $info );
            $module    = $this->getModuleObject( $info );
            $response  = $module->setRequest( $request )->prepare( $this )->respond();
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
     * @param ModuleInterface|string $responder
     * @param null|string      $appUrl
     * @return $this
     */
    public function addModule( $responder, $appUrl=null )
    {
        $info = array(
            'module' => $responder,
            'appUrl' => $appUrl,
            'always' => false,
        );
        if( $appUrl === true ) $info[ 'always' ] = true;
        $this->modules[] = $info;
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
     * @return ModuleInterface
     */
    private function getModuleObject( $info ) {
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
        if( empty( $this->modules ) ) return $this;
        foreach( $this->modules as $key => $info ) {
            $module = $this->getModuleObject( $info );
            $module->instantiate();
            $this->modules[ $key ][ 'module' ] = $module;
        }
        return $this;
    }

}