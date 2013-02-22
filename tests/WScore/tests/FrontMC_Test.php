<?php
namespace WScore\tests\Web;

require_once( __DIR__ . '/../../autoload.php' );

class FrontMC_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Web\FrontMC */
    var $front;
    function setUp()
    {
        $container = include( __DIR__ . '/../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $this->front = $container->get( '\WScore\tests\Mock\FrontSelfLoad' );
    }

    function test_matches_with_test()
    {
        $this->assertArrayHasKey( 0, $this->front->loaders );
        $this->assertEquals( 'WScore\tests\Mock\SelfLoader1', get_class( $this->front->loaders[0] ) );
        $this->assertEquals( 'WScore\tests\Mock\SelfLoader2', get_class( $this->front->loaders[1] ) );
        $this->assertEquals( 'SelfLoader1', $this->front->loaders[2]->name() );
    }

}
