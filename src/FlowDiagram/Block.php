<?php
namespace FlowDiagram;

use Observer\Observer;
use Observer\Observable;

/**
 * Represents a block in a flow diagram.
 * Multiple blocks are coupled via observer pattern so that an
 * actual (event) flow is acomplished between blocks.
 * This block holds a callable operation that acts on the input to produce an output.
 */
class Block extends Observer
{

    /**
     *
     * @var unknown
     */
    private $_name;

    /**
     * The input to the block
     *
     * @var mixed
     */
    private $_input;

    /**
     * The output to the block
     *
     * @var mixed
     */
    private $_output;

    /**
     * The operation function
     *
     * @var callable
     */
    private $_operation;

    /**
     * internals are Observable
     *
     * @todo needs better naming
     * @var Observable
     */
    private $_internals;

    /**
     * Holds all current blocks by unique name
     *
     * @var Block[]
     */
    private static $blocks = array();

    /**
     * Factory method
     *
     * @param string $name
     * @return Block
     */
    public static function create($name)
    {
        if (! is_string($name)) {
            throw new \Exception('Supplied name must be a string');
        }
        
        if (! isset(self::$blocks[$name])) {
            self::$blocks[$name] = new Block($name);
        }
        
        return self::$blocks[$name];
    }

    /**
     * Constructs and sets identity function as operator
     *
     * @param string $name
     */
    private function __construct($name)
    {
        $this->_name = $name;
        $this->_input = null;
        $this->_output = null;
        
        // by default an identity closure is set
        $this->_operation = function ()
        {
            return func_get_args();
        };
        
        $this->_internals = new Observable();
    }

    /**
     * Returns the name of the block
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * This block will be the predecessor of the given block.
     *
     * @param Block $block
     * @return null
     */
    public function predecessorTo(Block $block)
    {
        if ($this === $block) {
            return;
        }
        
        $this->setSuccessorBlock($block);
        $block->setPredecessorBlock($this);
    }

    /**
     * This block will be the successor of the given block.
     *
     * @param Block $block
     * @return null
     */
    public function successorTo(Block $block)
    {
        if ($this === $block) {
            return;
        }
        
        $this->setPredecessorBlock($block);
        $block->setSuccessorBlock($this);
    }

    /**
     * Set the block that follows after this block
     *
     * @param Block $block
     *            Block that is the successor to this block
     */
    public function setSuccessorBlock($block)
    {
        if ($block instanceof Block) {
            // attach the successor block to the output_changed event
            // because we will notify the successor block when this output is changed
            $this->_internals->attach('output_changed', $block);
            
            // attach the predecessor block to the remove event
            $this->_internals->attach('remove', $block);
        }
    }

    /**
     * Set the block that goes before this block
     *
     * @param Block $block
     *            Block that is the predecessor to this block
     */
    public function setPredecessorBlock($block)
    {
        if ($block instanceof Block) {
            // attach the predecessor block to the request_input event
            // because we will request input from this block
            $this->_internals->attach('request_input', $block);
            
            // attach the predecessor block to the remove event
            $this->_internals->attach('remove', $block);
        }
    }

    /**
     * Optionally sets the input.
     * Always returns it.
     * Internals notifies chained blocks of a request_input event.
     *
     * @return \FlowDiagram\mixed
     */
    public function input()
    {
        if (func_num_args() > 0) {
            $this->_input = func_get_args();
        }
        
        $this->_internals->notify('request_input');
        
        return $this->_input;
    }

    /**
     *
     * @param callable $callable
     */
    public function setOperation(callable $callable)
    {
        $this->_operation = $callable;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Observer\Observer::update()
     */
    public function update($event, $state)
    {
        if ($event == 'output_changed') {
            if (is_null($this->_input)) {
                $this->_input = array();
            } elseif (! is_array($this->_input)) {
                $this->_input = array(
                    $this->_input
                );
            }
            
            $this->_input = array_merge($this->_input, $state);
        } elseif ($event == 'request_input') {
            $this->input();
            $this->output();
        } elseif ($event == 'remove') {
            $this->_internals->detach('output_changed', $state);
            $this->_internals->detach('request_input', $state);
            $this->_internals->detach('remove', $state);
        }
    }

    /**
     * Operate on the input with the given callable operation.
     *
     * @param mixed $input
     * @return mixed
     */
    public function operate($input)
    {
        $output = call_user_func_array($this->_operation, $input);
        if (! is_array($output)) {
            $output = array(
                $output
            );
        }
        return $output;
    }

    /**
     * Returns the output.
     * If there is none, operate on the input to produce it.
     * Internals notifies chained blocks that the output has changed.
     *
     * @throws \Exception
     * @return \FlowDiagram\mixed
     */
    public function output()
    {
        // if the output is not retrieved yet
        if (is_null($this->_output)) {
            
            // get the input
            $input = $this->input();
            
            if (is_null($input)) {
                // if there is no input
                throw new \Exception(sprintf('Can not resolve an input for block %s', $this));
            } else {
                // there is input, operate on it and set as output
                $this->_output = $this->operate($input);
                $this->_internals->setState($input)->notify('output_changed');
            }
        }
        
        return $this->_output;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_name;
    }

    /**
     * Notifies chained blocks of the removal of this block.
     * Unsets static reference.
     * Please manually call unset on the variable.
     */
    public function remove()
    {
        $this->_internals->setState($this)->notify('remove');
        $this->_internals->clear();
        
        unset(self::$blocks[$this->_name]);
    }
}