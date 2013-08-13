<?php
namespace WScore\Web;

use WScore\Response\Request;

class WebRequest extends Request
{
    public $baseURL;

    public $pathInfo;

    public $extTypes = array(
        'htm' => 'html',
        'php' => 'html',
        'txt' => 'text',
        'md'  => 'html',
        'markdown' => 'html',
    );

    /**
     * set baseURL and use it as requestRoot.
     *
     * @param $url
     * @return $this
     */
    public function setBaseUrl( $url )
    {
        $this->baseURL = $url;
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
        $this->pathInfo = $path;
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
            $this->dataType = $type;
        }
        elseif( $this->pathInfo && $ext = pathinfo( $this->pathInfo, PATHINFO_EXTENSION ) ) {
            $ext = strtolower( $ext );
            $ext = isset( $this->extTypes[ $ext ] ) ? $this->extTypes[ $ext ] : $ext;
            $this->dataType = $ext;
        }
        return $this;
    }
}