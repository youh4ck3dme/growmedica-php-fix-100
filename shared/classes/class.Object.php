<?

interface iUkazkovyInterface 
{
	public function getVarA();
	public function setVarA($value);

	public function getVarB();
	public function setVarB($value = NULL);	

	public function getHigher();
}

class UkazkovaTrieda implements iUkazkovyInterface 
{
	private $varA;
	private $varB;

	/**
	*	@name Konstruktor
	*	@desc Konstruktor, ktory priradi hodnoty pri vytvoreni objektu
	*
	*	@acces public
	*	@param int $varA
	*/

	public function __construct($variableA)
	{
		NumericRequiredException::try_value($variableA);

		$this->varA = $this->fuckinazer($variableA);
		$this->varB = $this->fuckinazer(rand(1,10));
	}

	/**
	*	@name getVarA()
	*	@desc Vrati hodnotu varA
	*
	*	@acces public
	*	@return int
	*/

	public function getVarA()
	{
		return $this->varA;
	}

	/**
	*	@name setVarA()
	*	@desc Nastavi hodnotu varA
	*
	*	@acces public
	*	@param int $value
	*/

	public function setVarA($value)
	{
		NumericRequiredException::try_value($value);

		$this->varA = $value;
	}	

	/**
	*	@name getVarB()
	*	@desc Vrati hodnotu varB
	*
	*	@acces public
	*	@return int
	*/

	public function getVarB()
	{
		return $this->varB;
	}

	/**
	*	@name setVarB()
	*	@desc Nastavi hodnotu varB
	*
	*	@acces public
	*	@param int $value
	*/

	public function setVarB($value = NULL)
	{
		if($value)
		{
			$this->varB = $value;
		}
		else
		{
			$this->varB = rand(1,10);
		}
	}		

	/**
	*	@name getHigher()
	*	@desc Vrati bud varA alebo varB, podla toho co je vacsie
	*
	*	@acces public
	*	@return int
	*/

	public function getHigher()
	{
		return (($this->varA >= $this->varB) ? $this->varA : $this->varB);
	}

	/**
	*	@name fuckinazer()
	*	@desc Ak mu padne karta, pohraje sa s cislom a zmeni jeho hodnotu. Ale ma zmysel len pri konstruktore alebo pri vlozeni hodnoty, preto je private... Vonku ju nepotrebujeme
	*
	*	@acces private
	*	@param int $value
	*	@return int
	*/

	private function fuckinazer($value)
	{
		$foo = rand(3,5);

		if($foo == 4)
		{
			return $value * $foo;
		}
		else
		{
			return $value;
		}
	}

	/**
	*	@name isMondayToday()
	*	@desc Zisti ci je pondelok... Nema nic spolocne s touto triedou, ale moze sa hodit, tak preto je static.
	*
	*	@acces static
	*	@return boolean
	*/

	static function isMondayToday()
	{
		if(date('D') == 'Mon')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}

/***

$obj = new UkazkovaTrieda(5);
echo $obj->getVarA();
echo '<br>';
echo $obj->getVarB();
echo '<br>';
echo $obj->getHigher();
echo '<br>';
var_dump(UkazkovaTrieda::isMondayToday());
echo '<br>';
var_dump($obj->isMondayToday());

// ODKOMENTOVANIE TYCHTO TU SKONCI CHYBOU ALEBO VYNIMKOU... DO TOHO
//echo $obj->fuckinazer(5);
//echo $obj->setVarA('dsfds');

**/