<?php
namespace WScore\tests\Web\Loader;

use \WScore\Web\Loader\Matcher;
require_once( __DIR__ . '/../../../autoload.php' );

class Matcher_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Web\Loader\Matcher */
    var $matcher;
    function setUp()
    {
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $this->matcher = $container->get( '\WScore\Web\Loader\Matcher' );
    }
    
    function test_matches_with_test()
    {
        $this->matcher->setRoute( array( '/test/' => array( 'found' => 'test' ) ) );
        $loaded = $this->matcher->load( '/test/' );
        $this->assertArrayHasKey( 'found', $loaded );
        $this->assertEquals( 'test', $loaded['found'] );
        $this->assertEquals( '/test/', $loaded[0] );
    }
    
    function test_matches_fail()
    {
        $this->matcher->setRoute( array( '/test/' => array( 'found' => 'test' ) ) );
        $loaded = $this->matcher->load( '/not-found/' );
        $this->assertNull( $loaded );
    }
    
    function test_matches_with_parameter()
    {
        $this->matcher->setRoute( array( '/test/:id' => array( 'found' => 'id-test' ) ) );
        $loaded = $this->matcher->load( '/test/5' );
        $this->assertArrayHasKey( 'found', $loaded );
        $this->assertEquals( 'id-test', $loaded['found'] );
        $this->assertEquals( '/test/5', $loaded[0] );
        $this->assertEquals( '/test', $loaded[1] );
        $this->assertArrayHasKey( 'id', $loaded );
        $this->assertEquals( '5', $loaded['id'] );
    }
    
    function test_matcher_name()
    {
        $name = $this->matcher->name();
        $this->assertEquals( 'Matcher', $name );
        
        $refC = new \ReflectionClass( '\WScore\Web\Loader\Matcher' );
        $refP = $refC->getProperty( 'name' );
        $refP->setAccessible( true );
        $refP->setValue( $this->matcher, 'Test-Name' );
        
        $name = $this->matcher->name();
        $this->assertEquals( 'Test-Name', $name );
    }
}
