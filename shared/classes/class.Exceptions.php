<?

class ArrayRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje pole!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(!is_array($value))
	 		{
	 			throw new ArrayRequiredException();
	 		}			
		} 
		catch (ArrayRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	}
}

/************************************************************************************************************************************************************/

class ObjectRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje objekt!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(!is_object($value))
	 		{
	 			throw new ObjectRequiredException();
	 		}			
		} 
		catch (ObjectRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	}
}

/************************************************************************************************************************************************************/

class NumericRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje ciselnu hodnotu!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(!is_numeric($value))
	 		{
	 			throw new NumericRequiredException();
	 		}			
		} 
		catch (NumericRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	}
}

/************************************************************************************************************************************************************/

class IntegerRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje ciselnu hodnotu!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(!is_integer($value))
	 		{
	 			throw new IntegerRequiredException();
	 		}			
		} 
		catch (IntegerRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	}
}

/************************************************************************************************************************************************************/

class BooleanRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje boolean hodnotu!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(!is_bool($value))
	 		{
	 			throw new BooleanRequiredException();
	 		}			
		} 
		catch (BooleanRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	}
}

/************************************************************************************************************************************************************/

class NotZeroRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje ciselnu hodnotu, ktora bude vacsia ako 0!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(!is_numeric($value) || (is_numeric($value) && $value < 1))
	 		{
	 			throw new NotZeroRequiredException();
	 		}			
		} 
		catch (NotZeroRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	}
 }

/************************************************************************************************************************************************************/

class NotEmptyRequiredException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		if (!$message) 
		{
			throw new $this('<b>'.get_class($this).':</b> Funkcia vyzaduje neprazdnu hodnotu!<br/><pre>'.$this->getTraceAsString().'</pre>');
		}
		
		parent::__construct($message, $code);
	}

 	static function try_value($value)
 	{
		try 
		{
	 		if(empty($value) OR is_null($value))
	 		{
	 			throw new NotEmptyRequiredException();
	 		}			
		} 
		catch (NotEmptyRequiredException $e) 
		{
            echo $e->getMessage();
            return false;			
		}
 	} 	
}
