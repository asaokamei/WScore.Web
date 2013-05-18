<?php
namespace WScore\Web;

use WScore\Web\Respond\Chain;

/**
 * Class HttpResponder
 * 
 * for web application. 
 *
 * @package WScore\Web
 */
class HttpResponder extends Chain
{
    /**
     * @Inject
     * @var \WScore\Web\Http\Request
     */
    public $httpRequest;

    /**
     * @Inject
     * @var \WScore\Web\Http\Response
     */
    public $httpResponse;

    /**
     * @Inject
     * @var \WScore\Web\Respond\Request
     */
    public $request;

    /**
     * set request based on _SERVER and httpRequest. 
     * 
     * @param array $server
     * @param array $data
     * @return $this
     */
    public function setHttpRequest( $server, $data=array() )
    {
        $this->httpRequest->setServer( $server );
        $this->httpRequest->setPost( $data );
        $this->request->set( $this->httpRequest->getInfo() );
        $this->post = $data;
        return $this;
    }

    /**
     * @return $this
     */
    public function render()
    {
        $this->response->render();
        return $this;
    }

    /**
     * @return Http\Response
     */
    public function getHttpResponse()
    {
        $this->httpResponse->setContent( $this->response->content );
        $this->httpResponse->setStatusCode( $this->response->statusCode );
        $this->httpResponse->setHttpHeader( $this->response->headers );
        return $this->httpResponse;
    }
    
    public function emit()
    {
        $this->getHttpResponse()->send();
    }
}
