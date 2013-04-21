<?php
namespace WScore\Web\View;

class ScoreMenu
{
    public $pathInfo;
    
    public $baseUrl;
    
    public $score;
    
    public $menu = array();

    /**
     * @param array $menu
     * @param string $pathInfo
     * @return $this
     */
    public function setMenu( $menu, $pathInfo )
    {
        $this->pathInfo = $pathInfo;
        $this->score = $this->prepMenu( $menu );
        $this->menu = $menu;
        return $this;
    }

    /**
     * @return array
     */
    public function getMenu() {
        return $this->menu;
    }

    /**
     * @return int
     */
    public function getScore() {
        return $this->score;
    }
        
    /**
     * @param array $menu
     * @return int
     */
    private function prepMenu( &$menu )
    {
        $max_score = -1;
        foreach( $menu as &$item ) 
        {
            if( isset( $item[ 'url' ] ) ) {
                $item[ 'score' ] = $this->score( $item['url'] );
                if( $item[ 'score' ] >  $max_score ) $max_score = $item[ 'score' ];
            }
            if( isset( $item[ 'pages' ] ) && is_array( $item[ 'pages' ] ) ) {
                $score = $this->prepMenu( $item[ 'pages' ] );
                if( $score > $max_score ) $max_score = $score;
                if( $score > $item[ 'score' ] ) $item[ 'score' ] = $score;
            }
        }
        return $max_score;
    }
    
    /**
     * @param string $url
     * @return int
     */
    private function score( $url ) 
    {
        $pathInfo = $this->pathInfo;
        $pathLength = strlen( $pathInfo );
        for( $i = 0; $i < $pathLength; $i++ ) {
            if( !isset( $url[$i] ) || $pathInfo[$i] !== $url[$i] ) break;
        }
        $diff = min( 100, strlen( $url ) - $pathLength );
        $score = $i * 100 + 100 - $diff;
        return $score;
    }
}