<?php
namespace WScore\Web\Loader;

use \WScore\Template\TemplateInterface;

class Renderer extends LoaderAbstract
{
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
        if( !isset( $match[ 'render' ] ) ) {
            $match[ 'render' ] = $pathInfo;
        }
        return $this->render( $match );
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
        $this->template->setTemplate( $match['render'] );
        $content = $this->template->render();
        $this->response->setContent( $content );
        return $this->response;
    }
}