<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @user   Ayoola
 * @package    Application_User_List
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_User_Abstract
 */  
 
require_once 'Application/User/Abstract.php';


/**
 * @user   Ayoola
 * @package    Application_User_List
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_User_List extends Application_User_Abstract
{
		
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		$this->setViewContent( $this->getList(), true );
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
		$list->listTitle = 'List of Users of this Application';

		if( ! $database = Application_Settings_Abstract::getSettings( 'UserAccount', 'default-database' ) )
		{
			$database = 'cloud';
		}
		switch( $database )
		{
			case 'cloud':

			break;
			case 'relational':

			break;
			case 'file':
				$list->rowDataColumn = 'user_information';
			break;
		
		}
		$list->showSearchBox = true;
		$data = $this->getDbData();
	//	rsort( $data );
		$list->setData( $data ); 
		$list->setListOptions
		( 
			array( 
				//		'Creator' => '<a rel="spotlight;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_User_Creator/" title="Add a new user">+</a>',
						'Settings' => '<a rel="spotlight;" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Settings_Editor/settingsname_name/UserAccount/" title="User Account settings">User Account Settings</a>'
						) 
		);
	//	var_export( base64_encode( hash( 'sha512', 'tymyjope' ) ) );
	//	$this->setIdColumn( 'user_name' );
		$list->setKey( $this->getIdColumn() );
	//	$list->setKey( 'email' );
		$list->setNoRecordMessage( 'There are no user accounts on this application' );
		$list->createList(  
			array(
				'firstname' => null, 
				'lastname' => null, 
				'email' => null, 
				'username' => '<a rel="spotlight;" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_User_Editor/?' . $this->getIdColumn() . '=%KEY%">%FIELD%</a>',
				'  ' => '<a title="Manually update wallet balance" class="normalnews boxednews" rel="spotlight;" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Wallet_Editor/?' . $this->getIdColumn() . '=%KEY%">$</a>',
				'   ' => '<a title="Sign in as this user" class="normalnews boxednews" rel="spotlight;" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_User_Impersonate/?' . $this->getIdColumn() . '=%KEY%">-</a>',
				
				' ' => '<a  class="badnews boxednews" title="Delete" rel="spotlight;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_User_Delete/?' . $this->getIdColumn() . '=%KEY%">X</a>', 
			)
		);
		//var_export( $list );
		return $list;
    } 
	// END OF CLASS
}
