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

    const SKIP = 'fooooooobarrrrrrrr';
    
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
            if ( ! isset($this->bindings[$i]) || $this->bindings[$i] === static::SKIP) {
                array_push($arguments, array_shift($supplied));
            }
            else {
                array_push($arguments, $this->bindings[$i]);
            }
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
