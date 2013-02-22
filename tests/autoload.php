<?php
namespace WScore\tests;

include( __DIR__ . '/../vendor/autoload.php' );
$loader = new \Composer\Autoload\ClassLoader();
$loader->add( 'WScore\Web', dirname( __DIR__ ).'/src' );
$loader->add( 'WScore\tests', __DIR__ );
$loader->register();
