<?php
namespace WScore\tests;

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

    function test_frontMC_self_loads()
    {
        $this->assertArrayHasKey( 0, $this->front->loaders );
        $this->assertEquals( 'WScore\tests\Mock\SelfLoader1', get_class( $this->front->loaders[0] ) );
        $this->assertEquals( 'WScore\tests\Mock\SelfLoader2', get_class( $this->front->loaders[1] ) );
        $this->assertEquals( 'SelfLoader1', $this->front->loaders[2]->name() );
    }

    function test_loops_all_loaders()
    {
        $this->front->pathInfo( 'test/loop' );
        $this->front->run();

        /** @var $loader1 \WScore\tests\Mock\SelfLoader1 */
        $loader1 = $this->front->loaders[0];
        $this->assertTrue( $loader1->pre_load );
        $this->assertTrue( $loader1->post_load );
        $this->assertTrue( $loader1->loaded );
        $this->assertEquals( 'test/loop', $loader1->path );

        /** @var $loader2 \WScore\tests\Mock\SelfLoader2 */
        $loader2 = $this->front->loaders[1];
        $this->assertTrue( $loader2->pre_load );
        $this->assertTrue( $loader2->post_load );
        $this->assertTrue( $loader2->loaded );
        $this->assertEquals( 'test/loop', $loader2->path );
    }
}
