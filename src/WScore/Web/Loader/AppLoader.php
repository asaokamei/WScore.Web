<?php
namespace WScore\Web\Loader;

use \WScore\Template\TemplateInterface;
use \WScore\DiContainer\ContainerInterface;
use \WScore\Web\Loader\Renderer;

class AppLoader extends Renderer
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var \WScore\Web\Http\Response
     */
    protected $response;

    /**
     * @Inject
     * @var TemplateInterface
     */
    public $template;

    /** @var string */
    public $templateRoot;
    
    /**
     * this method should be called from front-end dispatcher.
     *
     * @param \WScore\Web\FrontMC $front
     * @param string $appUrl
     */
    public function pre_load( $front, $appUrl )
    {
        $this->front = $front;
        $this->appUrl = $appUrl;
        $this->template->set( 'appUrl',  $appUrl );
        $this->template->set( 'appRoot', $front->baseUrl . $appUrl );
    }

    /**
     * Loads response if pathinfo matches with routes.
     *
     * @param string $pathInfo
     * @return null|string
     */
    public function load( $pathInfo )
    {
        $pathInfo = substr( $pathInfo, strlen( $this->appUrl ) );
        if( !$match = $this->router->match( $pathInfo ) ) {
            return null;
        }
        if( !isset( $match[ 'render' ] ) ) $match[ 'render' ] = $match[1];
        $match[ 'appUrl'  ] = $this->appUrl;
        $match[ 'appRoot' ] = $this->front->baseUrl . $this->appUrl;
        
        // load page object.
        $data = $this->pager( $match );
        
        // process returned $data. 
        if( $data === true ) {
            // reload it self.
            $this->response->setHttpHeader( 'Location', $this->front->request->getRequestUri() );
            return $this->response;
        } 
        elseif( is_string( $data ) || $data === '' ) {
            // redirect with Location header. $data must be a url w.r.t. appUrl. 
            $this->response->setHttpHeader( 'Location', $this->front->baseUrl . $this->appUrl . $data );
            return $this->response;
        }
        return $this->render( $match, $data );
    }

    /**
     * loads Page object and calls onMethod.
     *
     * @param array $match
     * @throws \Exception
     * @return array
     */
    public function pager( $match )
    {
        // set up data.
        $method = $this->front->request->getMethod();
        $method = 'on' . ucwords( $method );
        // find class to construct a page data.
        $render = $match[ 'render' ];
        $class  = $this->getClass( $render );
        if( !class_exists( $class ) ) {
            return array(
                'onMethod' => $method,
            );
        }
        $page = $this->container->get( $class );
        if( !method_exists( $page, $method ) ) {
            throw new \Exception( 'method not found: '. $method, 400 );
        }
        // construct page data.
        $data = $page->$method( $match );
        if( is_array( $data ) ) {
            $data[ 'onMethod' ] = $method;
        } 
        elseif( is_null( $data ) ) {
            $data = array( 'onMethod' => $method );
        }
        return $data;
    }

    /**
     * find class name for Page objects to load.
     *
     * @param $render
     * @return string
     */
    private function getClass( $render )
    {
        if( strpos( $render, '.' )!==false ) {
            $render = substr( $render, 0, strpos( $render, '.' ) );
        }
        $list = explode( '/', $render );
        $class = $this->getPageRoot();
        foreach( $list as $name ) {
            if( !$name ) continue;
            $class .= '\\' . ucwords( $name );
        }
        return $class;
    }

    /**
     * gets root class name for Page objects.
     * @return string
     */
    private function getPageRoot() {
        $class = get_called_class();
        $class = substr( $class, 0, strrpos( $class, '\\' ) );
        return $class . '\\Page';
    }

    /**
     * @param array $match
     * @param array $data
     * @throws \RuntimeException
     * @return \WScore\Web\Http\Response
     */
    protected function render( $match, $data=array() )
    {
        if( isset( $match[ 'parent' ] ) ) {
            $this->template->setParent( $match[ 'parent' ] );
        }
        if( isset( $match[ 'addParent' ] ) ) {
            $this->template->addParent( $match[ 'addParent' ] );
        }
        $template = $this->templateRoot . $match[ 'render' ] . '.php';
        if( !file_exists( $template ) ) {
            throw new \RuntimeException( 'file not found', 404 );
        }
        $this->template->assign( $data );
        $this->template->setTemplate( $template );
        $content = $this->template->render();
        $this->response->setContent( $content );
        return $this->response;
    }
}