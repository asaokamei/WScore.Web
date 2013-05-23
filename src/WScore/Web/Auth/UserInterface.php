<?php
namespace WScore\Web\Auth;

interface UserInterface
{
    public function getPassword( $id );
    public function getInfo( $id );
}