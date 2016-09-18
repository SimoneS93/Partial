<?php

namespace Partial;

use OutOfRangeException;
use Partial\Exceptions\PartialException;

/**
 * Description of Partial
 *
 * @author Dev
 */
class Partial
{
    public static $d = false;

    /**
     * SKIP constant
     * Here we need a value to uniquely identify an "unset" value
     * NULL can't be used, since it is a valid argument for function calls
     * so we set it to a "almost-unique" string
     */
    const SKIP = '$2a$04$NmoLLFJGcJZjpxVX.39oe.yIX9aXrvdoHQ3UlFrTFSNbjArV/2iSu';
    
    /**
     *
     * @var callable
     */
    private $callable;
    
    /**
     *
     * @var array
     */
    private $bindings;
    
    /**
     *
     * @var int
     */
    private $current;
    
    /**
     *
     * @var int
     */
    private $numArgs;
    
    
    /**
     * 
     * @param callable $callable
     * @param array $arguments
     * @throws PartialException
     */
    public function __construct($callable, array $arguments = [])
    {
        if ( !is_callable($callable)) {
            throw new PartialException('$callable param must be callable');
        }
        
        $this->callable  = $callable;
        $this->current   = 0;
        $this->numArgs   = 0;
        $this->bindings = func_num_args() > 1 ? $arguments : NULL;
    }
    
    public function __invoke() {
        return $this->apply(func_get_args());
    }
    
    /**
     * Bind argument
     * 
     * @param mixed|variadic $argument
     * @return Partial\Partial
     */
    public function bind($argument)
    {
        if (func_num_args() > 1) {
            array_map([$this, 'bind'], func_get_args());
            
            return $this;
        }
        
        return $this->bindAt($this->current++, $argument);
    }
    
    /**
     * Bind argument at position
     * 
     * @param int $index
     * @param mixed $argument
     * @return \Partial\Partial
     * @throws OutOfRangeException
     */
    public function bindAt($index, $argument)
    {
        if ($index < 0) {
            throw new OutOfRangeException('$index must be >= 0');
        }
        
        $this->bindings[$index] = $argument;
        
        return $this;
    }
    
    /**
     * Unbind argument at position
     * 
     * @param int $index
     * @return \Partial\Partial
     * @throws OutOfRangeException
     */
    public function unbindAt($index)
    {
        if ($index < 0) {
            throw new OutOfRangeException('$index must be >= 0');
        }
        
        unset($this->bindings[$index]);
        
        return $this;
    }
    
    /**
     * Test for binding at position
     * 
     * @param int $index
     * @return boolean
     */
    public function hasBindingAt($index)
    {
        return array_key_exists($index, $this->bindings) && $this->bindings[$index] !== static::SKIP;
    }

    /**
     * 
     * @return mixed
     */
    public function call()
    {
        $arguments = [];
        $supplied  = func_get_args();
        $totalArgs = func_num_args() + count($this->bindings);
        
        // merge bindings and supplied arguments
        for ($i = 0; $i < $totalArgs; $i++) {
            array_push($arguments, $this->hasBindingAt($i) ? $this->bindings[$i] : array_shift($supplied));
        }
        
        return call_user_func_array($this->callable, $arguments);
    }
    
    /**
     * @see call
     * 
     * @param array $arguments
     * @return mixed
     */
    public function apply(array $arguments)
    {
        return call_user_func_array([$this, 'call'], $arguments);
    }
    
}
