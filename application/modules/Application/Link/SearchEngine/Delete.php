<?php
/**
 * AyStyle Developer Tool
 *
 * LICENSE
 *
 * @category   Ayoola
 * @package    Application_Link_SearchEngine_Delete
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 * @version    $Id: Delete.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Application_Link_SearchEngine_Abstract
 */
 
require_once 'Application/Link/Abstract.php';


/**
 * @category   Ayoola
 * @package    Application_Link_SearchEngine_Delete
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 */

class Application_Link_SearchEngine_Delete extends Application_Link_SearchEngine_Abstract
{
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		try
		{ 
			if( ! $data = self::getIdentifierData() ){ return false; }
			$this->createConfirmationForm( 'Delete ' . $data['searchengine_name'],  'Delete search engine Information' );
			$this->setViewContent( $this->getForm()->view(), true );
			if( $this->deleteDb( false ) ){ $this->setViewContent( 'Search engine deleted successfully', true ); }
		}
		catch( Application_Link_SearchEngine_Exception $e ){ return false; }
    } 
	// END OF CLASS
}
