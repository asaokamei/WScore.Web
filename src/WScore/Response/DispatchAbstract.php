<?php
namespace WScore\Response;

use \WScore\Response\PageAbstract;
use \WScore\DiContainer\ContainerInterface;

abstract class DispatchAbstract implements ModuleInterface
{
    use ModuleTrait;

    /**
     * @Inject
     * @var \WScore\Web\Router
     */
    public $router;

    /**
     * @Inject
     * @var ContainerInterface
     */
    public $container;

    /**
     * @Inject
     * @var \WScore\Response\Response
     */
    public $response;

    /**
     * @var string    location of page object.
     */
    public $pageRoot = '';

    /**
     * @var string    location of view templates.
     */
    public $viewRoot = '';

    /**
     * matched parameter by route match.
     *
     * @var array
     */
    public $match = array();

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param string $viewDir    location of view (template) files.
     */
    public function __construct( $viewDir=null )
    {
        $class = get_called_class();
        $pos   = strrpos( $class, '\\' );
        $namespace = '';
        if( $pos !== false ) {
            $namespace = substr( $class, 0, $pos );
        }
        $this->pageRoot = $namespace . '\Page'; // root for class name
        $this->viewRoot = $viewDir   . '/View'; // root for template file
    }

    /**
     * sets routes to match.
     *
     * @param array $route
     * @return $this
     */
    public function setRoute( $route ) {
        $this->router->set( $route );
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  main respond method
    // +----------------------------------------------------------------------+
    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @return ResponseInterface|null|bool
     */
    public function respond()
    {
        if( !$pageUri = $this->match() ) {
            return null;
        }
        return $this->dispatch( $pageUri );
    }

    /**
     * match requested uri against routes.
     * returns uri string to dispatch.
     *
     * @return string|null
     */
    public function match()
    {
        if( !$match = $this->router->match( $this->request->getInfo( 'requestUri' ) ) ) {
            return null;
        }
        if( !isset( $match[ 'render' ] ) && !isset( $match[1] ) ) {
            return null;
        }
        $this->match = $match;
        return ( isset( $match[ 'render' ] ) ) ? $match[ 'render' ] : $match[1];
    }

    /**
     * dispatch page object or view template.
     *
     * @param string $pageUri
     * @return null|ResponseInterface
     */
    public function dispatch( $pageUri )
    {
        if( $response = $this->loadPage( $pageUri ) ) {
            return $response;
        }
        if( $this->response && $template = $this->getViewFile( $pageUri ) ) {
            $this->response->assign( $this->request->getInfo() );
            $this->response->setTemplate( $template );
            return $this->response;
        }
        return null;
    }
    // +----------------------------------------------------------------------+
    //  loading Page (resource) object
    // +----------------------------------------------------------------------+
    /**
     * @param string $pageUri
     * @return ResponseInterface|null
     */
    private function loadPage( $pageUri )
    {
        if( !$class = $this->getPageClass( $pageUri ) ) return null;
        if( !$page = $this->container->get( $class ) ) return null;
        /** @var $page \WScore\Response\PageAbstract */

        if( $template = $this->getViewFile( $pageUri ) ) {
            $page->setTemplate( $template );
        }
        $response = $page->setRequest( $this->request )->prepare( $this )->respond( $this->match );
        $response->assign( $this->request->getInfo() );
        return $response;
    }

    /**
     * find class name for Page objects to load.
     *
     * @param string $pageUri
     * @return string
     */
    private function getPageClass( $pageUri )
    {
        if( strpos( $pageUri, '.' ) !== false ) {
            $pageUri = substr( $pageUri, 0, strpos( $pageUri, '.' ) );
        }
        $list  = explode( '/', $pageUri );
        $class = $this->pageRoot;
        foreach( $list as $name ) {
            if( !$name ) continue;
            $class .= '\\' . ucwords( $name );
        }
        return $class;
    }

    // +----------------------------------------------------------------------+
    //  loading View (template) file
    // +----------------------------------------------------------------------+
    /**
     * find view (template) file to render.
     *
     * @param string $viewUri
     * @return string
     */
    private function getViewFile( $viewUri )
    {
        $list = explode( DIRECTORY_SEPARATOR, $viewUri );
        foreach( $list as $key => $name ) {
            if( substr( $name, 0, 1 ) === '.' ) return null;
        }
        $viewUri = implode( DIRECTORY_SEPARATOR, $list );
        $extensions = array( '', '.php', '.html', '.htm', '.txt', '.txt.php', '.text', '.md', '.md.php', 'markdown' );
        if( substr( $viewUri, 0, 1 ) !== DIRECTORY_SEPARATOR ) $viewUri = DIRECTORY_SEPARATOR.$viewUri;
        foreach( $extensions as $ext ) {
            $template = $this->viewRoot . $viewUri . $ext;
            if( file_exists( $template ) ) return $template;
        }
        return false;
    }
    // +----------------------------------------------------------------------+
}