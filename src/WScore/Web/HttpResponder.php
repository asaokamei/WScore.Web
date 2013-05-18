<?php
namespace WScore\Web;

use WScore\Web\Respond\Chain;

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
     * set request based on _SERVER and httpRequest. 
     * 
     * @param array $server
     * @param array $data
     * @return $this
     */
    public function setHttpRequest( $server, $data )
    {
        $this->httpRequest->setServer( $server );
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
}
