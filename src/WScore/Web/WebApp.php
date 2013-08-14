<?php
namespace WScore\Web;

use WScore\Response\ChainAbstract;
use WScore\Response\ResponseInterface;
use WScore\Response\ModuleInterface;
use WScore\Template\TemplateInterface;

class WebApp extends ChainAbstract implements ModuleInterface
{
    /**
     * @Inject
     * @var \WScore\Http\Request
     */
    public $httpRequest;

    /**
     * @Inject
     * @var \WScore\Web\WebRequest
     */
    public $request;

    /**
     * @Inject
     * @var \WScore\Http\Response
     */
    public $httpResponse;

    /**
     * @Inject
     * @var TemplateInterface
     */
    public $renderer;

    /**
     * set request based on _SERVER and httpRequest.
     *
     * @param array $server
     * @param array $data
     * @return $this
     */
    public function setHttpRequest( $server, $data=array() )
    {
        // set up httpRequest with new $_SERVER variable.
        $this->httpRequest->setServer( $server );
        $this->httpRequest->setPost( $data );

        // copy some information to Request.
        $this->request->setPathInfo( $this->httpRequest->getPathInfo() );
        $this->request->setBaseUrl(  $this->httpRequest->getBaseUrl() );
        $this->request->setDataType();
        $this->request->on( $this->httpRequest->getMethod() );
        $this->request->data = $data;
        return $this;
    }

    /**
     * renders the response, and set the contents in response.
     *
     * @return $this
     */
    public function render()
    {
        if( $this->response instanceof ResponseInterface ) {
            if( !$this->response->getRenderer() && $this->renderer ) {
                $this->response->setRenderer( $this->renderer );
            }
            $this->response->render();
        }
        return $this;
    }

    /**
     * @return Http\Response
     */
    public function getHttpResponse()
    {
        /** @var $response \WScore\Response\Response */
        $response = $this->response;
        $this->httpResponse->setStatusCode( $response->statusCode );
        $this->httpResponse->setHttpHeader( $response->headers );
        $this->httpResponse->setContent(    $response->content );
        return $this->httpResponse;
    }

    public function emit()
    {
        $this->getHttpResponse()->send();
    }

}