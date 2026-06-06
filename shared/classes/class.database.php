<?php

	class Database
	{

		/*
		*	Funkcia na vypis hodnot pri debugovani
		*
		*	@param string 	$query 	-	sql query, ktore sa ma vypisat
		*	@param bool     $row    -	pole s result, ktore sa ma vypisat
		*/

		public function debug($query, $row)
		{
			echo $query;
			echo '<br/>';
			echo '<pre>';
			print_r($row);
			echo '</pre>';
		}

		/*
		*	Funkcia vracia objekt s datami
		*
		*	@param string 	$query 	-	sql query, ktore sa ma vykonat
		*	@param bool     $debug  -	zapnutie debugovacej funkcie - vypise query a print_r
		*
		*	@return object 			-	objekt s datami
		*/

		public function getRows($query, $debug = FALSE)
		{
			if ( $result = mysql_query($query) )
			{
				if ( mysql_num_rows($result) > 0 )
				{
					while($row = mysql_fetch_object($result))
					{
						$output[] = $row;
					}

					echo ($debug === true) ? Database::debug($query, $row) : '';
					return $output;
				}
				else
				{
					echo ($debug === true) ? $query.'<br/>NOTICE: Vysledok je prazdny' : '';
					return FALSE;
				}
			}
			else
			{
				echo ($debug === true) ? $query.'<br/>ERROR '.mysql_errno().': '.mysql_error() : '';
				return FALSE;
			}
		}

		/*
		*	Funkcia vracia OPTION pre SELECT
		*
		*	param string 	$query 	-	sql query, ktore sa ma vykonat
		*	param string 	$key 	-	nazov stlpca, ktory sa ma pouzit ako obsah atributu value; zadava sa ako obycajny textovy retazec v uvodzovkach
		*	param string 	$value 	-	nazov stlpca, ktory sa ma pouzit ako nazov/oznacenie optionov; zadava sa ako obycajny textovy retazec v uvodzovkach
		*
		*	return string 			-	retazec s optionmi
		*
		*	Je mozne vkladat aj kombinovane nazvy, ak sa definuju v query
		*/

		public function getOption($query, $key, $value, $selected='')
		{
			$output = '';

			if ( $result = mysql_query($query) )
			{
				if ( mysql_num_rows($result) > 0 )
				{
					while ( $row = mysql_fetch_object($result) )
					{
						$output .= '<option value="'.$row->$key.'" '.(($selected == $row->$value) ? 'selected="selected"' : '').'>'.$row->$value.'</option>'.PHP_EOL;
					}
					echo ($debug === true) ? Database::debug($query, $row) : '';
					return $output;
				}
				else
				{
					echo ($debug === true) ? $query.'<br/>NOTICE: Vysledok je prazdny' : '';
					return FALSE;
				}
			}
			else
			{
				echo ($debug === true) ? $query.'<br/>ERROR '.mysql_errno().': '.mysql_error() : '';
				return FALSE;
			}
		}

		/*
		*	Funkcia na vkladanie udajov do databazy
		*
		*	@param string
		*	@param array
		*	@return true or false		
		*/

		public function insertRow($table, $data, $debug = FALSE)
		{
			$query = 'INSERT INTO '.$table.' ('.implode(', ',array_keys($data)).') VALUES ('.implode(', ',array_values($data)).')';
			if( mysql_query($query) )
			{
				echo ($debug === true) ? Database::debug($query, $row) : '';
				return TRUE;
			}
			else
			{
				echo ($debug === true) ? $query.'<br/>ERROR '.mysql_errno().': '.mysql_error() : '';
				return FALSE;
			}
		}		

		/*
		*	Funkcia na updatovanie udajov
		*
		*	@param string
		*	@param array
		*	@param string
		*	@return true or false		
		*/

		public function updateRows($table, $data, $where, $debug = FALSE)
		{
			$set = '';
			foreach ( $data as $key=>$value )
			{
				$set .= $key.' = '.$value.', ';
			}
			$set = substr($set, 0, -2);

			$query = 'UPDATE '.$table.' SET '.$set.' WHERE '.$where;
			if( mysql_query($query) )
			{
				echo ($debug === true) ? Database::debug($query, $row) : '';		
				return TRUE;
			}
			else
			{
				echo ($debug === true) ? $query.'<br/>ERROR '.mysql_errno().': '.mysql_error() : '';
				return FALSE;
			}
		}

		/*
		*	Funkcia na vymazanie udajov
		*
		*	@param string
		*	@param string
		*	@return true or false
		*/

		public function deleteRows($table, $where, $debug = FALSE)
		{
			$query = 'DELETE FROM '.$table.' WHERE '.$where;
			if( mysql_query($query) )
			{
				echo ($debug === true) ? Database::debug($query, $row) : '';					
				return TRUE;
			}
			else
			{
				echo ($debug === true) ? $query.'<br/>ERROR '.mysql_errno().': '.mysql_error() : '';
				return FALSE;
			}		
		}		
	}