<?php
/**
 * AyStyle Developer Tool
 *
 * LICENSE
 *
 * @category   Ayoola
 * @package    Application_Profile_List
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 * @version    $Id: List.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_Profile_Abstract
 */
 
require_once 'Application/Profile/Abstract.php';


/**
 * @category   Ayoola
 * @package    Application_Profile_List
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 */

class Application_Profile_List extends Application_Profile_Abstract
{
		
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
//		$this->setViewContent( '<h3>OPTIONS:</h3>' );		
	//	$this->setViewContent( '<a title="Compose an profile..." rel="shadowbox;changeElementId=' . $this->getObjectName() . /'" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Profile_Creator/">+</a>' );
		$this->setViewContent( $this->getList() );
    } 
	
    /**
     * creates the list of the available subscription packages on the application
     * 
     */
	public function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'List of Profiles on this Application';
		$list->setData( $this->getDbData() );
		$list->setListOptions( array( 'Creator' => '<a title="Compose an profile..." rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Profile_Creator/">+</a>' ) );
		$this->setIdColumn( 'profile_url' );
		$list->setKey( 'profile_url' );
		$list->setNoRecordMessage( 'You have not writen any profile yet. <a rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Profile_Creator/">Write!</a>' );
		$list->createList(  
			array(
				'profile_url' => '<a rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Profile_Editor/?' . $this->getIdColumn() . '=%KEY%">%FIELD%</a>', 
				'o' => '<a title="Preview profile." rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Profile_View/?' . $this->getIdColumn() . '=%KEY%">0</a>', 
				'X' => '<a title="Delete" rel="shadowbox;height=300px;width=300px;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Profile_Delete/?' . $this->getIdColumn() . '=%KEY%">X</a>', 
			)
		);
		//var_export( $list );
		return $list;
    } 
	// END OF CLASS
}
