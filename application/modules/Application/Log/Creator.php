<?php
/**
 * AyStyle Developer Tool
 *
 * LICENSE
 *
 * @category   Ayoola
 * @package    Application_Log_Creator
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 * @version    $Id: Creator.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_Log_Abstract
 */
 
require_once 'Application/Log/Abstract.php';


/**
 * @category   Ayoola
 * @package    Application_Log_Creator
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 */

class Application_Log_Creator extends Application_Log_Abstract
{
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
	//	var_export( __LINE__ );
		$this->createForm( 'Create', 'Create a new Log Package' );
		$this->setViewContent( $this->getForm()->view(), true );
		if( $this->insertDb() ){ $this->setViewContent( 'Log viewer created successfully', true ); }
    } 
	
    /**
     * Inserts the Data into Storage
     * 
     * @return bool
     */
	protected function insertDb()
    {
		if( ! $values = $this->getForm()->getValues() ){ return false; }
		//var_export( $values );
		try{ $this->getDbTable()->insert( $values ); }
		catch( Ayoola_Dbase_Adapter_Xml_Table_Exception $e )
		{
			//$class = get_class( $this->getDbTable()->getDatabase()->getAdapter() );
			if( $e->getMessage() == Ayoola_Dbase_Adapter_Xml_Table_Abstract::ERROR_INSERT_AMBIGUOUS )
			{
				$this->getForm()->setBadnews( 'Name already exist, please choose a different name' );
				$this->setViewContent( $this->getForm()->view(), true );
			}
			
			return false;
		}
		return true;
    } 
	// END OF CLASS
}
