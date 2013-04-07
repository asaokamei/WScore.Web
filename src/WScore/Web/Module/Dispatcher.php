<?php
namespace WScore\Web\Module;

class FrontMcNotFoundException extends \Exception {}

class Dispatcher extends ModuleAbstract
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
        if( $appUrl ) {
            $this->modules[ $appUrl ] = $module;
        } else {
            $this->modules[] = $module;
        }
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
        
        if( empty( $this->loaders ) ) {
            throw new FrontMcNotFoundException( 'no loaders.' );
        }
        foreach( $this->modules as $appUrl => $module ) 
        {
            if( !is_numeric( $appUrl ) && strncmp( $this->pathInfo, $appUrl, strlen( $appUrl ) ) ) {
                continue;
            }
            $module->pre_load( $this, $appUrl );
            $response = $module->with( $this->post )->on( $this->method )->load( $this->pathInfo );
            $module->post_load( $this );
            if( $response ) $this->response = $response;
        }
        return $this->response;
    }
}
