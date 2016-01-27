<?php

// takes a multidimensional array from fetchall
// Converts it to an array useful for Select Form Element

class Ayoola_Filter_SelectListArray implements Ayoola_Filter_Interface
{

	protected $_value;
	
	protected $_label;
	
	
    public function filter( $data )
	{
	//	var_export( $data );
		
		$data = _Array( $data );
		$filteredValue = array();
		if( ! is_array( $data ) )
		{
			return $filteredValue;
		}
		foreach( $data as $values )
		{
			if( ! $values ){ continue; }
			if( array_key_exists( $this->_value, $values ) && array_key_exists( $this->_label, $values )  )
			{
				$filteredValue[$values[$this->_value]] = $values[$this->_label];
			}
		}
		asort( $filteredValue );
		return $filteredValue;
	}
 
    public function __construct( $value, $label )
	{
		$this->_value = (string) $value;
		$this->_label = (string) $label;
		
	}
 
 
}
