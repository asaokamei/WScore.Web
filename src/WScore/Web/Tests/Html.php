<?php
namespace WScore\Web\Tests;

class Html
{
    /**
     * @param string $file
     * @return string
     */
    public static function getFileContents( $file )
    {
        return file_get_contents( $file );
    }

    /**
     * @param \WScore\Web\WebApp  $app
     * @param array|string        $server
     * @param array               $post
     * @return string
     */
    public static function getAppContents( $app, $server, $post=array() )
    {
        $default = array(
            'REQUEST_METHOD' => 'GET',
        );
        if( !is_array( $server ) ) {
            $server = array(
                'REQUEST_URI'    => $server,
            );
        }
        $server = array_merge( $default, $server );
        //$app->pathInfo( $server );
        /** @var $response \WScore\Http\Response */
        $response = $app->with( $post )->on( $server['REQUEST_METHOD'] )->load( $server[ 'REQUEST_URI' ] );
        return $response->content;
    }

    /**
     * @param string $html
     * @return array
     */
    public static function extractHtmlTestMatches( $html )
    {
        $startTag = '<!-- HtmlTest: matchStart -->';
        $endTag   = '<!-- HtmlTest: matchEnd -->';
        if( preg_match_all( "/{$startTag}(.*?){$endTag}/ms", $html, $match ) ) {
            $html = $match[1];
        }
        return $html;
    }

    /**
     * @param string $html
     * @return array
     */
    public static function extractHtmlTestNeedles( $html )
    {
        $needles = array();
        $startTag = '<!-- HtmlTest: Needle=';
        $endTag   = ' -->';
        if( preg_match( "/({$startTag}.*?{$endTag})/ms", $html, $match ) ) {
            $needles = $match[1];
        }
        return $needles;
    }

}