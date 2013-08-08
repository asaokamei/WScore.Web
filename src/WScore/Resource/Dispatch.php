<?php
namespace WScore\Resource;

use \WScore\Resource\Resource;
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
     * @var \WScore\Resource\ResponseInterface
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
        // match against routes.
        $uri = $this->request->requestUri;
        if( !$match = $this->router->match( $uri ) ) {
            return null;
        }
        // prepare $match. 'page' is the page/view file to load.
        if( !isset( $match[ 'page' ] ) && !isset( $match[1] ) ) {
            return null;
        }
        $pageUri = $match[ 'page' ] ? $match[ 'page' ] : $match[1];
        // get response.
        if( $response = $this->loadPage( $pageUri, $match ) ) {
            return $response;
        }
        if( $template = $this->getViewFile( $pageUri ) ) {
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
        /** @var $resource \WScore\Resource\Resource */
        if( !$resource = $this->container->get( $class ) ) return null;

        $response = $resource->setParent( $this )->setRequest( $this->getRequest() )->respond( $match );
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