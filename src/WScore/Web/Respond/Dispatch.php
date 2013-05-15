<?php
namespace WScore\Web\Respond;

use \WScore\Template\TemplateInterface;
use \WScore\DiContainer\ContainerInterface;

class Dispatch extends RespondAbstract
{
    /**
     * @var string    location of page object. 
     */
    public $pageRoot = '';

    /**
     * @var string    location of view templates. 
     */
    public $viewRoot = '';

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
     * @var \WScore\Web\Respond\Response
     */
    public $response;

    /**
     * @Inject
     * @var TemplateInterface
     */
    public $template;

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
     * responds to a request.
     * returns null if there is no response.
     *
     * @param array $match
     * @return RespondInterface|null
     */
    public function respond( $match = array() )
    {
        // match against routes. 
        $appInfo = $this->request->appInfo;
        if( !$match = $this->router->match( $appInfo ) ) {
            return null;
        }
        // prepare $match. 'render', or 1st match overwrites appUrl.
        if( isset( $match[ 'render' ] ) ) {
            $this->request->appInfo = $match[ 'render' ];
        } elseif( isset( $match[ 1 ] ) ) {
            $this->request->appInfo = $match[ 1 ];
        }
        // get response. 
        $response = $this->loadPage( $match );
        if( !$response ) {
            $response = $this->loadView( $match );
        }
        return $response;
    }

    /**
     * @param $match
     * @return RespondInterface|null
     */
    private function loadPage( $match )
    {
        $class = $this->getPageClass( $this->request->appInfo );
        $page  = $this->container->get( $class );
        if( !$page ) return null;
        /** @var $page ResponsePage */
        if( $this->setupTemplate( $match ) ) {
            $page->renderer = $this->template;
        }
        $response = $page->request( $this->request, $this->post )->respond( $match );
        return $response;
    }

    /**
     * @param array $match
     * @return null|Response
     */
    private function loadView( $match )
    {
        if( !$this->setupTemplate( $match ) ) {
            return null;
        }
        $response = $this->response;
        $response->renderer = $this->template;
        return $response;
    }
    
    /**
     * set up template renderer. 
     * returns false if view (template) file not found. 
     * 
     * @param array $match
     * @return bool
     */
    private function setupTemplate( $match )
    {
        if( isset( $match[ 'parent' ] ) ) {
            $this->template->setParent( $match[ 'parent' ] );
        }
        if( isset( $match[ 'addParent' ] ) ) {
            $this->template->addParent( $match[ 'addParent' ] );
        }
        $template = $this->getViewFile( $this->request->appInfo );
        if( !$template ) {
            return false;
        }
        $this->template->assign( $match );
        $this->template->setTemplate( $template );
        return true;
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

}