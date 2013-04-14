<?php
namespace WScore\Web;

class WebApp extends Module\AppChain
{

    /**
     * @Inject
     * @var \WScore\Web\Http\Request
     */
    public $request;

    /** 
     * @var string 
     */
    public $baseUrl;

    /**
     * @param array $post
     * @return $this
     */
    public function with( $post )
    {
        $this->request->setPost( $post );
        parent::with( $post );
        return $this;
    }
    
    /**
     * @param array|string $config
     * @return \WScore\Web\WebApp
     */
    public function pathInfo( $config )
    {
        if( is_array( $config ) ) {
            $this->pathInfo = $this->request->getPathInfo( $config );
            $this->baseUrl  = $this->request->getBaseUrl();
        }
        elseif( is_string( $config ) ) {
            $this->pathInfo = $config;
            $this->baseUrl  = '/';
        }
        $this->appUrl  = '';
        $this->appRoot = $this->baseUrl;
        return $this;
    }

    /**
     * @param null $pathInfo
     * @return null|void|Http\Response
     */
    public function load( $pathInfo=null )
    {
        if( !$pathInfo ) $pathInfo = $this->pathInfo;
        return parent::load( $pathInfo );
    }
}
