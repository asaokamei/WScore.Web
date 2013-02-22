<?php
namespace WScore\tests\Web\Loader;

require_once( __DIR__ . '/../../../autoload.php' );

class MatchSelfConfig_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Web\Loader\Matcher */
    var $matcher;
    function setUp()
    {
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $this->matcher = $container->get( '\WScore\tests\Loader\Mock\MatchSelfConfig' );
    }

    function test1()
    {
        $loaded = $this->matcher->load( '/self' );
        $this->assertArrayHasKey( 'found', $loaded );
        $this->assertEquals( 'self', $loaded['found'] );
        $this->assertEquals( '/self', $loaded[0] );
        $this->assertEquals( '/self', $loaded[1] );

        $loaded = $this->matcher->load( '/test' );
        $this->assertNull( $loaded );
    }
}
