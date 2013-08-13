<?php
namespace WScore\Web;

use WScore\Response\Request;

class WebRequest extends Request
{
    public $baseURL;

    public $pathInfo;

    /**
     * set baseURL and use it as requestRoot.
     *
     * @param $url
     * @return $this
     */
    public function setBaseUrl( $url )
    {
        $this->setInfo( 'baseURL', $url );
        $this->path( $url );
        return $this;
    }

    /**
     * set pathInfo and use it as a requestURI.
     *
     * @param $path
     * @return $this
     */
    public function setPathInfo( $path )
    {
        $this->setInfo( 'pathInfo', $path );
        $this->uri( $path );
        return $this;
    }

    /**
     * set dataType or set from PathInfo's extension, such as html, text, json, etc.
     *
     * @param null $type
     * @return $this
     */
    public function setDataType( $type=null )
    {
        if( $type ) {
            $this->setInfo( 'requestType', $type );
        }
        elseif( $this->getInfo( 'pathInfo' ) && $type = pathinfo( $this->getInfo( 'pathInfo' ), PATHINFO_EXTENSION ) ) {
            $this->setInfo( 'requestType', $type );
        }
        return $this;
    }
}