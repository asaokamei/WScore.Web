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
        $render = isset( $match[ 'load' ] ) ? $match[ 'load' ] : $pathInfo;
        $render = $this->pager( $render );
        $match[ 'load' ] = $render;
        return $this->render( $match );
    }

    /**
     * loads Page object and calls onMethod.
     *
     * @param string $render
     * @return string
     */
    public function pager( $render )
    {
        $class = $this->getClass( $render );
        $method = $this->front->request->getMethod();
        $method = 'on' . ucwords( $method );

        $page = $this->container->get( $class );
        $data = (array) $page->$method( $render );
        $data[ 'onMethod' ] = $method;

        $this->template->assign( $data );
        return $render;
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
     * @return \WScore\Web\Http\Response
     */
    protected function render( $match )
    {
        if( isset( $match[ 'parent' ] ) ) {
            $this->template->setParent( $match[ 'parent' ] );
        }
        if( isset( $match[ 'addParent' ] ) ) {
            $this->template->addParent( $match[ 'addParent' ] );
        }
        $template = $this->templateRoot . $match[ 'load' ] . '.php';
        $this->template->setTemplate( $template );
        $content = $this->template->render();
        $this->response->setContent( $content );
        return $this->response;
    }
}