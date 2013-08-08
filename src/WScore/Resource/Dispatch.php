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
        if( $pos !== false ) {
            $namespace = substr( $class, 0, $pos );
        } else {
            $namespace = '';
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
    public function setRoute( $route )
    {
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
        if( !isset( $match[ 'page' ] ) ) {
            return null;
        }
        $pageUri = $match[ 'page' ];
        // get response.
        $response = $this->loadPage( $pageUri, $match );
        if( !$response ) {
            $this->response->assign( $match );
            $response = $this->loadView( $pageUri, $this->response );
        }
        return $response;
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
        $resource  = $this->container->get( $class );
        if( !$resource ) return null;

        $response = $resource->setParent( $this )->setRequest( $this->request )->respond( $match );
        if( $response ) {
            $this->loadView( $pageUri, $response );
        }
        return $response;
    }

    /**
     * find class name for Page objects to load.
     *
     * @param string $appInfo
     * @return string
     */
    private function getPageClass( $appInfo )
    {
        if( strpos( $appInfo, '.' ) !== false ) {
            $appInfo = substr( $appInfo, 0, strpos( $appInfo, '.' ) );
        }
        $list  = explode( '/', $appInfo );
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
     * @param string $viewUri
     * @param ResponseInterface $response
     * @return null|ResponseInterface
     */
    private function loadView( $viewUri, $response )
    {
        if( !$template = $this->getViewFile( $viewUri ) ) {
            return null;
        }
        $response->setTemplate( $template );
        return $response;
    }

    /**
     * find view (template) file to render.
     *
     * @param string $appUrl
     * @return string
     */
    private function getViewFile( $appUrl )
    {
        $extensions = array( '', '.php', '.html', '.htm', '.txt', '.txt.php', '.text', '.md', '.md.php', 'markdown' );
        if( substr( $appUrl, 0, 1 ) !== '/' ) $appUrl = '/'.$appUrl;
        foreach( $extensions as $ext ) {
            $template = $this->viewRoot . $appUrl . $ext;
            if( file_exists( $template ) ) return $template;
        }
        return false;
    }
    // +----------------------------------------------------------------------+
}