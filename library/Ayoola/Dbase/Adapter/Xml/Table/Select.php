<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Dbase_Adapter_Xml_Table_Select
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Select.php 4.6.12 6.33 ayoola $
 */

/**
 * @see Ayoola_Dbase_Adapter_Xml_Table_Abstract
 */
 
require_once 'Ayoola/Dbase/Adapter/Xml/Table/Abstract.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Dbase_Adapter_Xml_Table_Select
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Dbase_Adapter_Xml_Table_Select extends Ayoola_Dbase_Adapter_Xml_Table_Abstract
{
	
    /**
     * Switch to true to rearrange the result array
     *
     * @var boolean
     */
    public $selectResultKeyReArrange = false;
	
    /**
     * Switch to true to use a namespace for rowIds of parent table
     *
     * @var boolean
     */
    protected $_useParentNamespace = false;
	
    /**
     * Selects record into from db table
     *
     * @param array Fields of Data to select from the table
     * @param array Filter with field values
     */
    public function init( Array $fieldsToFetch = null, Array $where = null, Array $options = null )
    {
		/**
		 * If the accessibility NOT PRIVATE, we need to load all files
		 * The reason for the protected scope is to be able to select prIvate files
		 */
		$result = null;
		$result = $this->getCache( func_get_args() );
 	//	var_export( count( $result ) );
			//	if( $this->getTableName() == 'menu' )
				{
			//		$result = null;
				}
/* 			if( @$where['menu_name'] )
			{
			//	var_export( $where );
				$result = null;
			}
 */	//	var_export( $where );
	//	var_export( func_get_args() );
	//	if( ! is_array( $where[$key] ) )
		if( is_array( $result ) && empty( $options['disable_cache'] ) && $this->cache ){ return $result; }
	//	$this->_myFilename = @$options['filename'] ? : $this->_myFilename;
	//	exit;
	//	var_export( $this->_useCacheResult );
	//	var_export( func_get_args() );
	//	var_export( $this->getMyFilename() );
		$rows = array();
		if( ! empty( $options['filename'] ) )
		{
		//	var_export( $this->getMyFilename() );
			$this->setXml();
			$this->getXml()->load( $options['filename'] );
			$rows = $this->selectResultKeyReArrange == true ? array_merge( $rows, $this->doSelect( $fieldsToFetch, $where, $options ) ) : $rows + $this->doSelect( $fieldsToFetch, $where, $options );
		}
		elseif( $this->getAccessibility() == self::SCOPE_PRIVATE || $this->getAccessibility() == self::SCOPE_PUBLIC )
		{
		//	$rows = $this->doSelect( $fieldsToFetch, $where, $options ); 
			$files =  array_unique( array( $this->getFilenameAccordingToScope() => $this->getFilenameAccordingToScope() ) + $this->getSupplementaryFilenames() );
		//	$files =  array_unique( array( $this->getMyFilename() => $this->getMyFilename() ) + $this->getSupplementaryFilenames() );
		//	$files = array_unique( $this->getSupplementaryFilenames() );
/* 			if( Ayoola_Application::getUserInfo( 'access_level' ) == 99 )
			{
				var_export( count( $files ) );
				var_export( '<br />' );
				var_export( $files );
				var_export( '<br />' );
			}
 */			foreach( $files as $filename )
			{
				if( ! is_file( $filename ) ){ continue; }
			//	var_export( $this->getMyFilename() );
				$this->setXml();
				$this->getXml()->load( $filename );
				$rows = $this->selectResultKeyReArrange == true ? array_merge( $rows, $this->doSelect( $fieldsToFetch, $where, $options ) ) : $rows + $this->doSelect( $fieldsToFetch, $where, $options );
	//			var_export( count( $rows ) );
	//			var_export( '<br />' );
		//		return $rows;
			}
 	//	var_export( count( $rows ) );
	//		var_export( $rows );

		}
		else
		{
	//		var_export( $this->getMyFilename() );
			$rows = array();
			$files = array_unique( $this->getGlobalFilenames() );
	//		var_export( $files );
/* 				if( @$where['menu_name'] )
				{
				//	var_export( $where );
			//		var_export( $files );
				}
 */			foreach( $files as $filename )
			{
				if( ! is_file( $filename ) ){ continue; }
				$this->setXml();
				$this->getXml()->load( $filename );
			//	var_export( $this->getMyFilename() );
				$this->_useParentNamespace = ( $filename == $this->getMyFilename() ) ? false : true;
				$rows = $this->selectResultKeyReArrange == true ? array_merge( $rows, $this->doSelect( $fieldsToFetch, $where, $options ) ) : $rows + $this->doSelect( $fieldsToFetch, $where, $options );
			//	$rows = $rows + $this->doSelect( $fieldsToFetch, $where );
			//	var_export( $filename );
			}
		}
	//	exit();
 	//	var_export( count( $rows ) );
	//			var_export( $rows );
		if( empty( $options['disable_cache'] ) ){ $this->setCache( $rows ); }
		return $rows;
    }
	
    /**
     * Does the work
     *
     * @param void
     */
    public function doSelect( Array $fieldsToFetch = null, Array $where = null, Array $options = null )
    {
		//	Calculate the total fields on the table, extended
		$allFields = $this->query( 'FIELDLIST' );
	//	var_export( $allFields );
		if( ! is_null( $where ) )
		{ 
			foreach( $where as $key => $value )
			{  
		//		var_export( $key );
				if( ! in_array( $key, $allFields ) ){ throw new Ayoola_Dbase_Adapter_Xml_Table_Exception( "{$key} is not in field list" ); }
			}
		}
		if( is_null( $fieldsToFetch ) ){ $fieldsToFetch = $allFields; }
		else
		{
			foreach( $fieldsToFetch as $eachfield )
			{
				if( ! in_array( $eachfield, $allFields ) ){ throw new Ayoola_Dbase_Adapter_Xml_Table_Exception( "{$eachfield} is not in field list" ); 	}
			}
		}
		$rows = array();
	//	var_export( $this->getRecords()->childNodes->length );
		foreach( $this->getRecords()->childNodes as $eachRecord )
		{
			if( $eachRecord instanceof DOMText ){ continue; }
			$fields = array();		
			$rowId = self::getRecordRowId( $eachRecord );
			foreach( $eachRecord->childNodes as $field )
			{
				$key = self::getFieldKey( $field );
				if( ! in_array( $key, $fieldsToFetch ) ){ continue; }
				foreach( $field->childNodes as $value )
				{ 
					if( $value instanceof DOMCDATASection )
					{ 
						$fields[$key] = is_string( $value->data ) ? htmlspecialchars_decode( $value->data ) : $value->data;
						break; 
					} 
				}
				//		var_export( $fields[$key] );
				$fields[$key] = self::filterDataType( $fields[$key], $this->getTableDataTypes( $key ) );
				if( ! empty( $where ) )
				{ 
					if( array_key_exists( $key, $where ) )
					{
						if( ! is_array( $fields[$key] ) )
						{
							if( ! is_array( $where[$key] ) && $where[$key] != $fields[$key] )
							{ 
								continue 2; 
							}
							elseif( is_array( $where[$key] ) && ! in_array( $fields[$key], $where[$key] ) )
							{
							//	var_export( $fields[$key] );
								continue 2; 
							}
						}
 						else
						{
							//	An array is matched if a single member is present.
							if( ! in_array( $where[$key], $fields[$key] ) )
							{ 
								continue 2; 
							}
						//	var_export( $where[$key] );
						//	var_export( $fields[$key] );
						//	continue 2; 
						}
 					}
				}
				
				//	Retrieve values from the foreign keys
			//	$temp = array();
				foreach( $this->getForeignKeys() as $foreignTable => $foreignKey )
				{
					if( $key != $foreignKey ){ continue; }
					$foreignWhere = array( $foreignKey => $fields[$foreignKey] );
					if( empty( $temp[serialize( $foreignWhere )] ) )
					{ 
						$temp[serialize( $foreignWhere )] = self::selectForeign( $foreignTable, $foreignWhere );
					}
					$foreignData = $temp[serialize( $foreignWhere )];
					if( ! empty( $where ) )
					{ 
						foreach( $foreignData as $foreignDataKey => $foreignDataValue )
						{
							if( array_key_exists( $foreignDataKey, $where ) )
							{
								if( ! is_array( $where[$foreignDataKey] ) && $where[$foreignDataKey] != $foreignData[$foreignDataKey] )
								{ 
									continue 4; 
								}
								elseif( is_array( $where[$foreignDataKey] ) && ! in_array( $foreignData[$foreignDataKey], $where[$foreignDataKey] ) )
								{
								//	var_export( $fields[$key] );
									continue 2; 
								}
							}
						}
					}
					$fields = array_merge( $foreignData, $fields );
				}

			}
	//		$rowId = $this->_useParentNamespace ? 'parent_' . $rowId : $rowId;
			$rows[$rowId] = $fields;
		}
	//	var_export( $rows );
	
		// cache result
		if( empty( $options['disable_cache'] ) && $this->cache ){ $this->setCache( $rows ); }
		return $rows;
	}
	
    /**
     * Select from foreign tables
     *
     * @param string The table of the foreign
     * @param array Filter with field values
     */
    public static function selectForeign( $table, Array $foreignWhere )
    {
		return self::getForeignTable( $table )->selectOne( null, $foreignWhere );
    } 
		
    /**
     * sets the result from the last cache update
     *
     */
    public function setCache( $result )
    {
		$file = $this->getCacheFilename();
	//	var_export( $file );
		Ayoola_Doc::createDirectory( dirname( $file ) );
		return @file_put_contents( $file, serialize( $result ) );
    } 
		
    /**
     * sets the result from the last cache update
     *
     */
    public function getCache()
    {
		$cacheFile = $this->getCacheFilename( func_get_args() );
		$cacheTime = @filemtime( $cacheFile );
		foreach( $this->getGlobalFilenames() as $tableFile )
		{
		//	var_export( $tableFile . '<br />' );
			if( $cacheTime <= @filemtime( $tableFile ) ){ @unlink( $cacheFile ); }
		}
	//	var_export( filemtime( $cacheFile ) . '<br />' );
	//	var_export( filemtime( $tableFile ) . '<br />' );
	//	var_export( filemtime( $tableFile ) );
	//	var_export( $cacheFile );
		return @unserialize( file_get_contents( $cacheFile ) );
    } 
	// END OF CLASS
}
