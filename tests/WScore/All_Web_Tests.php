<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class All_Web_Tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s Web' );

        $suite->addTestFile( __DIR__ . '/tests/RouterTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/HttpResponderTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/Http/RequestTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/Response/ChainTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/Response/DispatchTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/Response/PageTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/Response/RequestTest.php' );
        $suite->addTestFile( __DIR__ . '/tests/Authenticate/StorePost_Test.php' );
        $suite->addTestFile( __DIR__ . '/tests/Authenticate/StoreSession_Test.php' );
        $suite->addTestFile( __DIR__ . '/tests/Authenticate/Authenticate_Test.php' );
        return $suite;
    }
}