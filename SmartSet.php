<?php
namespace D2K;

Class SmartSet
	implements \Iterator, \Countable
{
const UNIQUE = 1;
const SET = 2;

private $set;
private $rMap;

public function __construct($objectSet)
{
	$this->set = $objectSet;
}

// Accessors
public function __call($prop, $value)
{
	if(!$this->rMap[$prop])
	{
		$this->rMap[$prop] = $this->buildRMap($prop);
	}
	if($this->rMap[$prop]['data'][$value])
	{
		if($this->rMap[$prop]['type'] === self::SET)
		{
			$results = [];
			foreach($this->rMap[$prop]['data'][$value] as $key)
			{
				$results[] = $this->set[$key];
			}
			return new SmartSet($results);
		}
		else
		{
			return $this->set[$this->rMap[$prop]['data'][$value][0]];
		}
	}
	return false;
}
public function index($prop, $forceSubArray = false)
{
	$results = [];
	if(!$this->rMap[$prop])
	{
		$this->rMap[$prop] = $this->buildRMap($prop);
	}
	if($forceSubArray || $this->rMap[$prop]['type'] === self::SET)
	{
		foreach($this->rMap[$prop]['data'] as $propValue => $keys)
		{
			foreach($keys as $key)
			{
				$results[$propValue][] = $this->set[$key];
			}
		}
	}
	else
	{
		foreach($this->rMap[$prop]['data'] as $propValue => $keys)
		{
			$results[$propValue] = $this->set[$keys[0]];
		}
	}
	return $results;
}
public function property($prop)
{
	$out = [];
	foreach($this->set as $key => $item)
	{
		$out[$key] = $item->$prop;
	}
	return $out;
}

public function array()
{
	return $this->set;
}

// Modifiers
public function map($callable)
{
	foreach($this->set as $key => $item)
	{
		$this->set[$key] = $callable($key, $item);
	}
	return $this;
}
public function sort($cmp, $byKey = false)
{
	if($byKey)
	{
		uksort($this->set, $cmp);
	}
	else
	{
		uasort($this->set, $cmp);
	}
	return $this;
}

// Evolvers
public function callMethod($method, $parameters = [])
{
	$out = [];
	foreach($this->set as $key => $item)
	{
		$out[$key] = $item->$method(...$parameters);
	}
	return $out;
}
public function each($callable)
{
	$out = [];
	foreach($this->set as $key => $item)
	{
		$out[$key] = $callable($key, $item);
	}
	return $out;
}

private function buildRMap($prop)
{
	$rMap = [];
	$rMap['type'] = self::UNIQUE;
	foreach($this->set as $key => $row)
	{
		if(isset($rMap['data'][$row->$prop]))
		{
			$rMap['type'] = self::SET;
		}
		$rMap['data'][$row->$prop][] = $key;
	}
	return $rMap;
}



/* Iterator Implementation */
public function rewind() { return reset($this->set); }
public function current() { return current($this->set); }
public function key() { return key($this->set); }
public function next() { return next($this->set); }
public function valid()
{
	 $key = key($this->set);
	 return ($key !== NULL && $key !== FALSE);
}
/* Countable Implementation */
public function count() { return count($this->set); }
}
