<?

/************************************
*	POUZITIE:
*		
*	$obj_paginator = new Paginator;

	$obj_paginator->set_items_per_page(24);									//	pocet zobrazenych poloziek na 1 stranke
	$obj_paginator->set_items_count(480);									//	pocet poloziek v databaze
	
	$obj_paginator->set_params_base(Menu::getHyperLinkById($navigateId));	//	base... to ani netreba menit
	$obj_paginator->set_params($navigateArrayUrlWithoutBase);				//	ani toto nie je treba menit... maximalne k tomu pripojit dalsie parametre, ak treba

	echo $obj_paginator->get_paginator();									//	vypise paginator

	$obj_paginator->debug();												//	vypise zadane a vypocitane nastavenia paginatora

	...

	a v query, ktore taha samotne polozky sa nastavi LIMIT $obj_paginator->get_page_start(), 24;
*/	


interface iPaginator {

	public function set_items_per_page($value);
	public function set_items_count($value);
	
	public function set_params_base($value);
	public function set_params($value);

	public function get_page_start();
	public function get_page_end();

	public function get_paginator();

	public function debug();
}

/****************************************************************************/

class Paginator implements iPaginator {

	private $_items_per_page;
	private $_items_count;
	private $_pages;

	private $_params_base;
	private $_params;
	private $_params_count;

	private $_actual_page;
	private $_page_start;
	private $_page_end;

	/****************************************************************************/
	/****************************************************************************/
	/****************************************************************************/

	public function __construct()
	{
		$this->_items_per_page = 0;
		$this->_items_count = 0;
		$this->_pages = 1;

		$this->_page_start = 0;
	}

	/****************************************************************************/

	public function set_items_per_page($value)
	{
		$this->_items_per_page = (int)$value;
	}

	/****************************************************************************/

	public function set_items_count($value)
	{
		$this->_items_count = (int)$value;
	}

	/****************************************************************************/

	public function set_params_base($value)
	{
		$this->_params_base = $value;
	}

	/****************************************************************************/

	public function set_params($value)
	{
		$this->_params = $value;

		$this->set_params_count();
	}

	/****************************************************************************/

	public function get_page_start()
	{
		$this->set_page_start();

		return $this->_page_start;
	}

	/****************************************************************************/

	public function get_page_end()
	{
		$this->set_page_end();

		return $this->_page_end;
	}

	/****************************************************************************/

	public function get_paginator()
	{
		$this->set_pages();
		$this->set_actual_page();

		//$this->_pages = 10;

		if($this->_params[$this->_params_count-2] == 'strana' && is_numeric($this->_params[$this->_params_count-1]))
		{
			// odkazy na predoslu a prvu stranku
			if(intval($this->_actual_page) == 2)
			{
				echo '<a href="'.$this->_params_base.'/"><<</a> ';
				echo '<a href="'.$this->_params_base.'/"> 1</a> ';
			}
			else
			{
				echo '<a href="'.$this->_params_base.'/'.$this->_params[$this->_params_count-2].'/'.($this->_params[$this->_params_count-1] - 1).'"><<</a> ';
				echo '<a href="'.$this->_params_base.'/"> 1</a> ';
			}

			/*************************/

			// zvysne tri odkazy
			if($this->_actual_page > 3)
			{
				echo '... ';
			}

			if($this->_actual_page - 1 > 1)
			{
				echo '<a href="'.$this->_params_base.'/'.$this->_params[$this->_params_count-2].'/'.($this->_actual_page - 1).'">'.($this->_actual_page - 1).'</a> ';
			}

			/*************************/

			echo '<span style="padding:0px 1px;"><b>'.$this->_actual_page.'</b></span> ';

			/*************************/

			if($this->_actual_page + 1 < $this->_pages)
			{
				echo '<a href="'.$this->_params_base.'/'.$this->_params[$this->_params_count-2].'/'.($this->_actual_page + 1).'">'.($this->_actual_page + 1).'</a> ';
			}

			if($this->_actual_page < ($this->_pages - 2))
			{
				echo '... ';
			}

			/*************************/

			// odkazy na nasledujucu a poslednu stranku
			if($this->_pages > $this->_actual_page)
			{
				echo '<a href="'.$this->_params_base.'/'.$this->_params[$this->_params_count-2].'/'.$this->_pages.'"> '.$this->_pages.'</a> ';
				echo '<a href="'.$this->_params_base.'/'.$this->_params[$this->_params_count-2].'/'.($this->_params[$this->_params_count-1] + 1).'">>></a>';
			}
		}
		else
		{
			if($this->_pages > 1)
			{
				echo '<span style="padding:0px 1px;"><b>1</b></span> ';

				for($i=2;$i<5;++$i)
				{
					if($i < $this->_pages)
					{
						echo '<a href="'.$this->_params_base.'/strana/'.$i.'"><span style="padding:0px 3px;">'.$i.'</span></a> ';
						$last_i = $i;
					}
				}

				if($last_i < $this->_pages)
				{
					if($this->_pages - $last_i > 1)
					{
						echo '... <a href="'.$this->_params_base.'/strana/'.$this->_pages.'"><span style="padding:0px 3px;">'.$this->_pages.'</span></a> ';
					}
					else
					{
						echo '<a href="'.$this->_params_base.'/strana/'.$this->_pages.'"><span style="padding:0px 3px;">'.$this->_pages.'</span></a> ';	
					}
				}

				echo '<a href="'.$this->_params_base.'/strana/2">>></a>';
			}
		}

	}

	/****************************************************************************/

	public function debug()
	{
		echo '[_items_per_page]: '.$this->_items_per_page.'<br/>';
		echo '[_items_count]: '.$this->_items_count.'<br/>';
		echo '[_pages]: '.$this->_pages.'<br/>';

		echo '[_params_base]: '.$this->_params_base.'<br/>';;

		echo '[_params]: ';
		if(is_array($this->_params))
		{
			foreach($this->_params as $i => $v)
			{
				echo '['.$i.'] => '.$v.'; ';
			}
		}
		echo '<br/>';

		echo '[_params_count]: '.$this->_params_count.'<br/>';

		echo '[_actual_page]: '.$this->_actual_page.'<br/>';
		echo '[_page_start]: '.$this->_page_start.'<br/>';
		echo '[_page_end]: '.$this->_page_end.'<br/>';
	}

	/****************************************************************************/
	/****************************************************************************/
	/****************************************************************************/

	private function set_pages()
	{
		if($this->_items_count > 0 && $this->_items_per_page > 0)
		{
			$this->_pages = ceil($this->_items_count / $this->_items_per_page);
		}
		else
		{
			$this->_pages = 1;
		}
	}

	/****************************************************************************/

	private function set_page_start()
	{
		$this->set_actual_page();

		$this->_page_start = ((($this->_actual_page * $this->_items_per_page) - 1) - ($this->_items_per_page - 1));
	}

	/****************************************************************************/

	private function set_page_end()
	{
		$this->set_actual_page();

		$this->_page_end = ((($this->_actual_page * $this->_items_per_page) - 1));
	}

	/****************************************************************************/

	private function set_params_count()
	{
		$this->_params_count = count($this->_params);
	}

	/****************************************************************************/

	private function set_actual_page()
	{
		if($this->_params[$this->_params_count-2] == 'strana')
		{
			$this->_actual_page = $this->_params[$this->_params_count-1];
		}
		else
		{
			$this->_actual_page = 1;
		}
	}

}