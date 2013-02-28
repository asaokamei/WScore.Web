<?php
namespace WScore\Web\Loader;

use \WScore\Template\TemplateInterface;
use \WScore\DiContainer\ContainerInterface;
use \WScore\Web\Loader\Renderer;

class Pager extends Renderer
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var \WScore\Web\Http\Request
     */
    protected $request;

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
    
    /**
     * Loads response if pathinfo matches with routes.
     *
     * @param string $pathInfo
     * @return null|string
     */
    public function load( $pathInfo )
    {
        if( !$match = $this->router->match( $pathInfo ) ) {
            return null;
        }
        $match = array(
            $pathInfo, $pathInfo
        );
        $this->pager( $match );
        return $this->render( $match );
    }

    /**
     * loads Page object and calls onMethod.
     *
     * @param $match
     */
    public function pager( $match )
    {
        $data = array();
        $appUrl = $match[1];
        $class = $this->getClass( $appUrl );
        $method = $this->request->getMethod();
        $method = 'on' . ucwords( $method );

        $page = $this->container->get( $class );
        $data = (array) $page->$method( $match );

        $this->template->assign( $data );
    }

    /**
     * find class name for Page objects to load.
     *
     * @param $appUrl
     * @return string
     */
    private function getClass( $appUrl )
    {
        $appUrl = substr( $appUrl, 0, strpos( $appUrl, '.' ) );
        $list = explode( '/', $appUrl );
        $class = $this->getPageRoot();
        foreach( $list as $name ) {
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
        $class = substr( $class, 0, strrpos( $class, '\\' ) );
        return $class . '\\Page';
    }
}