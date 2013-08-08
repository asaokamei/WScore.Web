<?php
namespace WScore\Response;

use \WScore\Response\Page;
use \WScore\DiContainer\ContainerInterface;

class Dispatch implements ResponsibilityInterface
{
    use ResponsibilityTrait;

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
     * @var \WScore\Response\ResponseInterface
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

    /**
     * @return $this
     */
    public function instantiate() {
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  main respond method
    // +----------------------------------------------------------------------+
    /**
     * responds to a request.
     * returns Response object, or null if nothing to respond.
     *
     * @param array $match
     * @return ResponseInterface|null|bool
     */
    public function respond( $match = array() )
    {
        if( !$match = $this->match() ) {
            return null;
        }
        return $this->dispatch( $match );
    }

    /**
     * match requested uri against routes.
     * 
     * @return array|null
     */
    public function match()
    {
        if( !$match = $this->router->match( $this->request->requestUri ) ) {
            return null;
        }
        if( !isset( $match[ 'render' ] ) && !isset( $match[1] ) ) {
            return null;
        }
        // make sure render column is set.
        if( !isset( $match[ 'render' ] ) ) {
            $match[ 'render' ] = $match[1];
        }
        return $match;
    }

    /**
     * dispatch page object or view template.
     *
     * @param array $match
     * @return null|ResponseInterface
     */
    public function dispatch( $match=array() )
    {
        $pageUri = $match[ 'render' ];
        // get response.
        if( $response = $this->loadPage( $pageUri, $match ) ) {
            return $response;
        }
        if( $template = $this->getViewFile( $pageUri ) ) {
            $this->response->assign( (array) $this->request );
            $this->response->assign( $match );
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
     * @param array  $match
     * @return ResponseInterface|null
     */
    private function loadPage( $pageUri, $match )
    {
        $class = $this->getPageClass( $pageUri );
        /** @var $page \WScore\Response\Page */
        if( !$page = $this->container->get( $class ) ) return null;

        $response = $page->setParent( $this )->setRequest( $this->getRequest() )->respond( $match );
        if( $template = $this->getViewFile( $pageUri ) ) {
            $response->setTemplate( $template );
        }
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
        if( substr( $viewUri, 0, 1 ) === '.' ) return null;
        $extensions = array( '', '.php', '.html', '.htm', '.txt', '.txt.php', '.text', '.md', '.md.php', 'markdown' );
        if( substr( $viewUri, 0, 1 ) !== '/' ) $viewUri = '/'.$viewUri;
        foreach( $extensions as $ext ) {
            $template = $this->viewRoot . $viewUri . $ext;
            if( file_exists( $template ) ) return $template;
        }
        return false;
    }
    // +----------------------------------------------------------------------+
}