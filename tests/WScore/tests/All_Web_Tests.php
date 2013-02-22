<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class All_Web_Tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s Web' );
        $suite->addTestFile( __DIR__ . '/FrontMC_Test.php' );
        $suite->addTestFile( __DIR__ . '/Loader/Matcher_Test.php' );
        $suite->addTestFile( __DIR__ . '/Loader/MatchSelfConfig_Test.php' );

        return $suite;
    }
}