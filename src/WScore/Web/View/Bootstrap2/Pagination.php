<?php
namespace WScore\Web\View\Bootstrap2;

/**
 * Class PaginateBootstrap
 * @package WScore\Web\View
 *
 * @cacheable
 */
class Pagination
{
    /** @var array */
    private $url;

    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param array $url
     * @return self
     */
    public function setUrls( $url ) {
        $this->url = $url;
        $this->checkTopAndLast();
        return $this;
    }
    /**
     */
    public function checkTopAndLast()
    {
        return;
        /* // probably I need really complicated logic to do this...
        if( !$this->url[ 'top_page' ] && !$this->url[ 'last_page' ] ) {
            // none of top and last page are present. i.e. delete them.
            unset( $this->url[ 'top_page' ] );
            unset( $this->url[ 'last_page' ] );
        }
         */
    }

    /**
     * @param $label
     * @param $url
     * @return string
     */
    protected function getList( $label, $url )
    {
        if( !$url ) {
            $url = '#';
            $class = ' class="disabled"';
        } else {
            $class = '';
        }
        $list = "<li{$class}><a href=\"{$url}\">{$label}</a></li>\n";
        return $list;
    }

    /**
     * @param $name
     * @return null
     */
    protected function url( $name ) {
        return array_key_exists( $name, $this->url ) ? $this->url[$name] : null;
    }

    /**
     * @return string
     */
    public function draw()
    {
        $draw = '';
        $draw .= $this->getList( 'top', $this->url( 'top_page' ) );
        $draw .= $this->getList( '&lt;&lt;', $this->url( 'prev_page' ) );
        foreach( $this->url['pages'] as $page => $url ) {
            $draw .= $this->getList( $page, $url );
        }
        $draw .= $this->getList( '&gt;&gt;', $this->url( 'next_page' ) );
        $draw .= $this->getList( 'last', $this->url( 'last_page' ) );
        $draw  = "
        <div class=\"pagination\"><ul>
          {$draw}
        </ul></div>
        ";
        return $draw;
    }
    // +----------------------------------------------------------------------+
}