<?php
namespace WScore\Web\Module;

class FrontMcNotFoundException extends \Exception {}

class AppChain extends ModuleAbstract
{
    /**
     * @var ModuleInterface[]
     */
    public $modules = array();

    /**
     * @var \WScore\Web\Http\Response|mixed
     */
    public $response = null;

    /**
     * @param ModuleInterface  $module
     * @param null|string      $appUrl
     * @return $this
     */
    public function setModule( $module, $appUrl=null )
    {
        $info = array(
            'module' => $module,
            'appUrl' => $appUrl,
            'always' => false,
        );
        if( $appUrl === true ) $info[ 'always' ] = true;
        $this->modules[] = $info;
        return $this;
    }
    
    /**
     * Loads response if pathInfo matches with routes.
     *
     * @param string $pathInfo
     * @throws FrontMcNotFoundException
     * @return null|\WScore\Web\Http\Response
     */
    public function load( $pathInfo )
    {
        $this->pathInfo = $pathInfo;
        
        if( empty( $this->modules ) ) {
            throw new FrontMcNotFoundException( 'no loaders.' );
        }
        foreach( $this->modules as $info ) 
        {
            if( !$this->loadModule( $info ) ) {
                continue;
            }
            /** @var $module ModuleInterface */
            $module   = $info[ 'module' ];
            list( $appUrl, $pathInfo ) = $this->getPathUrl( $info );
            $module->pre_load( $this, $appUrl );
            $response = $module->with( $this->post )->on( $this->method )->load( $pathInfo );
            $module->post_load( $this );
            if( $response ) $this->response = $response;
        }
        return $this->response;
    }

    /**
     * check if module should be loaded. 
     * 
     * @param $info
     * @return bool
     */
    private function loadModule( $info )
    {
        $appUrl = $info[ 'appUrl' ];
        $always = $info[ 'always' ];
        if( $this->response && !$always ) {
            // if response is back, then skip subsequent modules unless $always is true. 
            return false;
        }
        if( is_numeric( $appUrl ) || is_bool( $appUrl ) ) {
            // load module if it's just a simple array module entry. 
            return true;
        }
        if( strncmp( $this->pathInfo, $appUrl, strlen( $appUrl ) ) ) {
            // ignore the module with appUrl which does not match with pathInfo. 
            return false;
        }
        return true;
    }

    /**
     * get appUrl and pathInfo for module.
     * 
     * @param $info
     * @return array
     */
    private function getPathUrl( $info )
    {
        $appUrl   = $info[ 'appUrl' ];
        $pathInfo = $this->pathInfo;
        if( is_numeric( $appUrl ) || is_bool( $appUrl ) ) {
            // load module if it's just a simple array module entry. 
            $appUrl = '';
        }
        else {
            $pathInfo = substr( $this->pathInfo, strlen( $appUrl ) );
        }
        return array( $appUrl, $pathInfo );
    }
}
