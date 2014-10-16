<?php

namespace FlowDiagram;

class Block
{
	private $_name;

	/**
	 * The previous block if any
	 * @var [type]
	 */
	private $_previous_block;

	/**
	 * The next block if any
	 * @var Block
	 */
	private $_next_block;

	/**
	 * [$_input description]
	 * @var mixed
	 */
	private $_input;

	/**
	 * [$_output description]
	 * @var mixed
	 */
	private $_output;

	private $_operation;

	private static $block_names = array();

	/**
	 * Factory method
	 * 
	 * @param  string $name
	 * @return Block       
	 */
	public static function create($name)
	{
		if (!is_string($name)) {
			throw new \Exception('Supplied name must be a string');
		}

		if (!isset(self::$block_names[$name])) {
			self::$block_names[$name] = new Block($name);
		}

		return self::$block_names[$name];
	}

	private function __construct($name)
	{
		$this->_name = $name;
		$this->_next_block = null;
		$this->_input = null;
		$this->_output = null;

		// by default an identity closure is set
		$this->_operation = function ($id) { return $id; };
	}

	public function getName()
	{
		return $this->_name;
	}

	/**
	 * This block will be the predecessor of the given block.
	 * 
	 * @param  Block  $block [description]
	 * @return [type]        [description]
	 */
	public function predecessorTo(Block $block)
	{
		$this->setSuccessorBlock($block);
		$block->setPredecessorBlock($this);
	}

	/**
	 * This block will be the successor of the given block.
	 * @param  Block  $block [description]
	 * @return [type]        [description]
	 */
	public function successorTo(Block $block)
	{
		$this->setPredecessorBlock($block);
		$block->setSuccessorBlock($this);
	}

	public function setSuccessorBlock(Block $block)
	{
		$this->_next_block = $block;
	}

	public function setPredecessorBlock(Block $block)
	{
		$this->_previous_block = $block;
	}

	public function input()
	{
		if (func_num_args() == 1) {
			$this->_input = func_get_arg(0); // single value
		} elseif (func_num_args() > 1) {
			$this->_input = func_get_args(); // array of values
		}

		// if the input is still null, try to get it from previous block
		if (is_null($this->_input) && !is_null($this->_previous_block)) {
			$this->_input = $this->_previous_block->output();
		} // else there must be an input already

		return $this->_input;
	}

	public function setOperation(callable $callable)
	{
		$this->_operation = $callable;
	}

	public function operate($input)
	{
		return call_user_func($this->_operation, $input);
	}

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
			}
		}

		return $this->_output;
	}

	public function __toString()
	{
		return $this->_name;
	}

	public function __destroy()
	{
		unset(self::$block_names[$this->_name]);
	}
}