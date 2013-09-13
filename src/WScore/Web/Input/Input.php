<?php
namespace WScore\Web\Input;

use WScore\Validation\Validation;
use WScore\Web\Session\Token;
use WScore\Web\Session\Storage;
use WScore\Validation\Rules;

class Input
{
    /**
     * @Inject
     * @var \WScore\Validation\Validation
     */
    public $validation;

    /**
     * @Inject
     * @var \WScore\Web\Session\Token
     */
    public $token;

    /**
     * @Inject
     * @var \WScore\Web\Session\Storage
     */
    public $storage;
    
    /**
     * the source data to validate values. maybe $_POST. 
     * 
     * @var array
     */
    protected $source = array();
    
    /**
     * pre determined rules for input values. 
     * $rules = array( 'valueName' => [ 'rule' => $rule, 'message => 'message text' ], ... );
     * 
     * @var array
     */
    protected $rules = array();

    /**
     * @var bool
     */
    protected $error = false;

    // +----------------------------------------------------------------------+
    /**
     * @param Validation $validation
     * @param Token      $token
     * @param Storage    $storage
     * @return \WScore\Web\Input\Input
     */
    public function __construct( $validation=null, $token=null, $storage=null )
    {
        if( $validation ) $this->validation = $validation;
        if( $token ) $this->token = $token;
        if( $storage ) $this->storage = $storage;
    }

    /**
     * @return bool
     */
    public function isError() {
        return $this->error;
    }

    /**
     * @param bool $error
     */
    public function setError( $error=true ) {
        $this->error = $error;
    }

    // +----------------------------------------------------------------------+
    //  validating value.
    // +----------------------------------------------------------------------+
    /**
     * @param array $source
     * @return $this
     */
    public function source( $source ) {
        $this->source = & $source;
        $this->validation->source( $this->source );
        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    public function savePost( $key='_input_save_' )
    {
        $values = $this->pop();
        $values = serialize( $values );
        return "<input type=\"hidden\" name=\"{$key}\" value=\"{$values}\" />\n";
    }

    /**
     * @param string $key
     */
    public function loadPost( $key='_input_save_' )
    {
        if( isset( $this->source[ $key ] ) ) {
            $values = unserialize( $this->source[ $key ] );
            $this->source = array_merge( $this->source, $values );
        }
    }

    /**
     * @param string $key
     */
    public function saveSession( $key='_input_save_' )
    {
        $values = $this->pop();
        $this->storage->setup( $key );
        foreach( $values as $key => $val ) {
            $this->storage->set( $key, $val );
        }
    }

    /**
     * @param string $key
     */
    public function loadSession( $key='_input_save_' )
    {
        if( isset( $this->source[ $key ] ) ) {
            $this->storage->setup( $key );
            $values = $this->storage->all();
            $this->source = array_merge( $this->source, $values );
        }
    }
    
    // +----------------------------------------------------------------------+
    //  validation 
    // +----------------------------------------------------------------------+
    /**
     * set rules and message for input value $name. 
     * 
     * @param string       $name
     * @param array|Rules  $rules
     * @param null|string  $message
     * @return $this
     */
    public function set( $name, $rules=array(), $message=null )
    {
        $this->rules[ $name ] = array(
            'rule' => $rules,
            'message' => $message,
        );
        return $this;
    }

    /**
     * apply pre set rules on source data. 
     * 
     * @return bool
     */
    public function apply()
    {
        foreach( $this->rules as $name => $rule ) {
            $this->push( $name, $rule[ 'rule' ], $rule[ 'message' ] );
        }
        return $this->isError();
    }
    
    /**
     * apply rules on value $name.
     * pre-set rules and messages are used if exist.
     * 
     * @param string       $name
     * @param array|Rules  $rules
     * @param null|string  $message
     * @return mixed
     */
    public function push( $name, $rules=array(), $message=null ) 
    {
        if( isset( $this->rules[ $name ] ) ) {
            if( !$message ) $message = $this->rules[$name]['message'];
            $rules = array_merge( $this->rules[$name['rule']], $rules );
        }
        $value = $this->validation->push( $name, $rules, $message );
        if( !$this->validation->isValid() ) {
            $this->setError();
        }
        return $value;
    }

    /**
     * pops input. set $safe=false to get all values including invalidated ones. 
     * 
     * @param null|string  $key
     * @param bool         $safe
     * @return array
     */
    public function pop( $key=null, $safe=true )
    {
        if( $key ) {
            return $this->validation->pop( $key );
        } elseif( $safe ) {
            return $this->validation->popSafe();
        } else {
            return $this->validation->pop();
        }
    }

    /**
     * pops html-safe validated input values. 
     * 
     * @param null $key
     * @return array
     */
    public function popHtml( $key=null )
    {
        $html = $this->pop( $key );
        array_walk_recursive( $html, function( &$value ) {
            $value = htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
        } );
        return $html;
    }
    // +----------------------------------------------------------------------+
    //  C.S.R.F. tokens
    // +----------------------------------------------------------------------+
    /**
     * 
     */
    public function pushToken() 
    {
        $token = $this->token->generateToken();
        $this->validation->pushValue( '_token', $token );
    }

    /**
     * @return bool
     */
    public function verifyToken() 
    {
        $token = $this->source[ '_token' ];
        $ok = $this->token->verifyToken( $token );
        if( !$ok ) {
            $this->setError();
        }
        return $ok;
    }
    // +----------------------------------------------------------------------+
}
