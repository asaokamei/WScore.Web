<?php
namespace WScore\Web\View;

class NavBarBootstrap
{
    protected $menu = array();
    
    protected $max_score = null;
    
    protected $tabs = array(
        'topUl'   => '<ul class="nav nav-tabs">',
        'subUl'   => '<ul class="dropdown-menu">',
        'divider' => '<li class="divider"></li>',
        'endUl'   => '</ul>',
        'liItem'  => '<li class="%3$s"><a href="%2$s">%1$s</a></li>',
        'liBlank' => '<li class="%3$s"><a href="%2$s" target="_blank">%1$s</a></li>',
        'liSub'   => '<li class="dropdown%3$s">
            <a class="dropdown-toggle" data-toggle="dropdown">%1$s<b class="caret"></b></a>%2$s
            </li>',
        'icon' => '<i class="icon-%s"></i>',
    );
    
    protected $pill = array();
    
    protected $tags = array();

    function __construct() 
    {
        $this->pill = $this->tabs;
        $this->pill[ 'topUl' ] = '<ul class="nav nav-pills">';
        $this->tags = $this->tabs;
    }
    
    /**
     * @param array|ScoreMenu $menu
     * @param $max_score
     * @return $this
     */
    function setMenu( $menu, $max_score=null ) 
    {
        if( $menu instanceof ScoreMenu ) {
            $this->menu      = $menu->getMenu();
            $this->max_score = $menu->getScore();
        } else {
            $this->menu      = $menu;
            $this->max_score = $max_score;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    function setTags( $name ) 
    {
        if( isset( $name ) && isset( $this->$name ) && is_array( $this->$name ) ) {
            $this->tags = & $this->$name;
        }
        return $this;
    }

    /**
     * @return string
     */
    function draw() 
    {
        $html  = $this->ul( $this->menu );
        return $html;
    }

    /**
     * @param string $name
     * @return mixed|string
     */
    private function text( $name ) 
    {
        $text = '';
        if( isset( $this->tags[ $name ] ) ) 
        {
            $text = $this->tags[ $name ];
            if( func_num_args() > 1 ) {
                $args = func_get_args();
                $args[0] = $text;
                $text = call_user_func_array( 'sprintf', $args );
            }
        }
        return $text;
    }

    /**
     * @param array $menu
     * @param string $ulType
     * @return string
     */
    private function ul( $menu, $ulType='topUl' ) 
    {
        $html = $this->text( $ulType );
        $html .= $this->li( $menu );
        $html .= $this->text( 'endUl' );
        return $html;
    }
    /**
     * @param array $menu
     * @return string
     */
    private function li( $menu ) 
    {
        $html = '';
        foreach( $menu as $item ) 
        {
            $url   = ( isset( $item[ 'url' ] ) ) ? $item[ 'url' ] : '';
            $title = ( isset( $item[ 'title' ] ) ) ? $item[ 'title' ] : '';
            $score = ( isset( $item[ 'score' ] ) ) ? $item[ 'score' ] : 0;
            $active = ( $this->max_score && $score >= $this->max_score ) ? ' active' : '';
            if( isset( $item[ 'icon' ] ) ) $title = $this->text( 'icon', $item['icon'] ) . $title;
            if( isset( $item[ 'pages' ] ) ) {
                $sub = $this->ul( $item['pages'], 'subUl' );
                $html .= $this->text( 'liSub', $title, $sub, $active );
            }
            elseif( isset( $item[ 'divider' ] ) ) {
                $html .= $this->text( 'divider' );
            }
            elseif( isset( $item[ 'target' ] ) && $item[ 'target' ] == 'blank' ) {
                $html .= $this->text( 'liBlank', $title, $url, $active );
            }
            else {
                $html .= $this->text( 'liItem', $title, $url, $active );
            }
        }
        return $html;
    }
}