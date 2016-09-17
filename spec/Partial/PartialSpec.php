<?php

namespace spec\Partial;

use Partial\Exceptions\PartialException;
use Partial\Partial;
use PhpSpec\ObjectBehavior;
use SebastianBergmann\RecursionContext\Exception;

class PartialSpec extends ObjectBehavior
{
    
    function it_is_callable()
    {
        $this->beConstructedWith('sprintf');
        
        if ( !is_callable($this)) {
            throw new Exception('Not callable');
        }
    }
    
    function it_throws_exception_on_construction_with_non_callable()
    {
        $this->beConstructedWith('Not a callable');
        
        $this
            ->shouldThrow(PartialException::class)
            ->duringInstantiation();
    }
    
    function it_binds_first_argument()
    {
        $this->beConstructedWith('sprintf');
        $this->bind('Hello, %s');
        
        $this->call('world')->shouldBe('Hello, world');
    }
    
    function it_binds_argument_at_index()
    {
        $this->beConstructedWith('sprintf');
        $this->bindAt(1, 'world');
        
        $this->call('Hello, %s')->shouldBe('Hello, world');
    }
    
    function it_binds_multiple_arguments()
    {
        $this->beConstructedWith('sprintf');
        $this->bind('%s, %s, %s, %s', 1, 2);
        
        $this->call(3, 4)->shouldBe('1, 2, 3, 4');
    }
    
    function it_allows_skipping_bindings()
    {
        $this->beConstructedWith('sprintf');
        $this->bind('Hello, %s, %s!', Partial::SKIP, 'again');
        
        $this->call('world')->shouldBe('Hello, world, again!');
    }
    
    function it_implements_invoke()
    {
        $this->beConstructedWith('sprintf');
        $this->bind('%s, %s, %s, %s', 1, 2);
        
        $self   = $this->getWrappedObject();
        $result = $self(3, 4);
        
        if ($result !== '1, 2, 3, 4') {
            throw new \Exception("Result mismatch: expected [1, 2, 3, 4] but got [{$result}]");
        }
    }
}
