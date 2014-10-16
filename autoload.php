<?php

function __autoload($classname)
{
	require_once 'src/' . str_replace('\\', '/', $classname) . '.php';
}