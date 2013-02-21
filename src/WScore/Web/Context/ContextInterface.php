<?php
namespace WScore\Web\Context;

interface ContextInterface
{
    public function setContext( $name, $context );
    
    public function context( $name );
    
    public function run( $entity, $action=null );
    
    public function restoreData( $name );
    
    function setActName( $name );
    
}