<?php
namespace WScore\Web\Module;

use \WScore\Template\TemplateInterface;
use \WScore\DiContainer\ContainerInterface;
use \WScore\Web\Page\PageInterface;

class AppLoader extends ModuleAbstract
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    public $container;

    /**
     * @Inject
     * @var \WScore\Web\Http\Response
     */
    public $response;

    /**
     * @Inject
     * @var TemplateInterface
     */
    public $template;

    /**
     * @var string
     */
    protected $pageRoot;

    /**
     * @var string
     */
    protected $viewRoot;

    // +----------------------------------------------------------------------+
    /**
     *
     */
    public function __construct( $dir=null )
    {
        $class = get_called_class();
        $pos   = strrpos( $class, '\\' ); 
        if( $pos !== false ) {
            $namespace = substr( $class, 0, $pos );
        } else {
            $namespace = '';
        }
        $this->pageRoot = $namespace . '\Page'; // root for class name
        $this->viewRoot = $dir       . '/View'; // root for template file
    }

    /**
     * Loads response if pathInfo matches with routes.
     *
     * @param string $pathInfo
     * @return null|\WScore\Web\Http\Response
     */
    public function load( $pathInfo )
    {
        $this->pathInfo = $pathInfo;
        if( !$match = $this->router->match( $pathInfo ) ) {
            return null;
        }
        if( !isset( $match[1] ) ) $match[1] = $match[0];
        if( !isset( $match[ 'render' ] ) ) $match[ 'render' ] = $match[1];
        $match[ 'appUrl'  ] = $this->appUrl;
        $match[ 'appRoot' ] = $this->appRoot;

        // load page object.
        $data     = $this->loadPage( $match );
        $response = $this->renderPage( $match, $data );
        return $response;
    }

    /**
     * loads Page object and calls onMethod.
     *
     * @param array $match
     * @return array
     */
    public function loadPage( $match )
    {
        // set up page class and method to load.
        $method = $this->method ?: 'get';
        $method = 'on' . ucwords( $method );
        $class  = $this->getPageClass( $match[ 'render' ] );
        if( !class_exists( $class ) || !method_exists( $class, $method ) ) {
            return PageInterface::RENDER_PAGE;
        }
        // construct page data.
        $page = $this->container->get( $class );
        $data = $this->dispatch( $page, $method, $match );
        if( $data === PageInterface::RENDER_PAGE ) {
            $data = array( 'onMethod' => $method );
        }
        elseif( is_array( $data ) && !isset( $data[ 'onMethod' ] ) ) {
            $data[ 'onMethod' ] = $method;
        }
        return $data;
    }

    /**
     * @param $page
     * @param $method
     * @param $match
     * @return mixed
     */
    private function dispatch( $page, $method, $match )
    {
        return $page->$method( $match, $this->post );
    }

    /**
     * @param array $match
     * @param array $data
     * @return bool|\WScore\Web\Http\Response
     */
    public function renderPage( $match, $data=array() )
    {
        // process returned $data. 
        if( $data === PageInterface::RENDER_NOTHING ) {
            return false;
        }
        if( $data === PageInterface::RELOAD_SELF ) {
            // reload it self.
            $this->response->setHttpHeader( 'Location', $this->front->request->getRequestUri() );
            return $this->response;
        }
        if( $data === PageInterface::JUMP_TO_APP_ROOT ) {
            // reload it self.
            $this->response->setHttpHeader( 'Location', $this->appRoot );
            return $this->response;
        }
        if( is_string( $data ) ) {
            // redirect with Location header. $data must be a url w.r.t. appUrl. 
            $this->response->setHttpHeader( 'Location', $this->appRoot . $data );
            return $this->response;
        }
        return $this->render( $match, $data );
    }

    // +----------------------------------------------------------------------+
    //  helpers
    // +----------------------------------------------------------------------+
    /**
     * @param array $match
     * @param array $data
     * @throws \RuntimeException
     * @return \WScore\Web\Http\Response
     */
    private function render( $match, $data=array() )
    {
        $this->template->set( 'appUrl',  $this->appUrl );
        $this->template->set( 'appRoot', $this->appRoot );
        if( isset( $match[ 'parent' ] ) ) {
            $this->template->setParent( $match[ 'parent' ] );
        }
        if( isset( $match[ 'addParent' ] ) ) {
            $this->template->addParent( $match[ 'addParent' ] );
        }
        $template = $this->getViewFile( $match[ 'render' ] );
        if( !$template ) {
            return $template;
        }
        if( $data && is_array( $data ) ) $this->template->assign( $data );
        $this->template->setTemplate( $template );
        // render template. 
        $content = $this->template->render();
        // set content to response object. 
        $this->response->setContent( $content );
        return $this->response;
    }
    
    /**
     * find class name for Page objects to load.
     *
     * @param string $render
     * @return string
     */
    private function getPageClass( $render )
    {
        if( strpos( $render, '.' ) !== false ) {
            $render = substr( $render, 0, strpos( $render, '.' ) );
        }
        $list  = explode( '/', $render );
        $class = $this->pageRoot;
        foreach( $list as $name ) {
            if( !$name ) continue;
            $class .= '\\' . ucwords( $name );
        }
        return $class;
    }

    /**
     * @param $render
     * @throws \RuntimeException
     * @return string
     */
    private function getViewFile( $render )
    {
        $extensions = array( '', '.php', '.html', '.htm', '.txt', '.txt.php', '.text', '.md', '.md.php', 'markdown' );
        if( substr( $render, 0, 1 ) !== '/' ) $render = '/'.$render;
        foreach( $extensions as $ext ) {
            $template = $this->viewRoot . $render . $ext;
            if( file_exists( $template ) ) return $template;
        }
        return false;
    }
    // +----------------------------------------------------------------------+
}