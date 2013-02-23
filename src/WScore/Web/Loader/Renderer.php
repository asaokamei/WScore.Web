<?php
namespace WScore\Web\Loader;

use \WScore\Template\Template;

class Renderer extends LoaderAbstract
{
    /**
     * @Inject
     * @var \WScore\Web\Http\Response
     */
    protected $response;

    /**
     * @Inject
     * @var Template
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
        return $this->render( $match );
    }

    /**
     * @param array $match
     * @return \WScore\Web\Http\Response
     */
    protected function render( $match )
    {
        $this->template->setTemplate( $match[1] );
        $content = (string) $this->template;
        $this->response->setContent( $content );
        return $this->response;
    }
}