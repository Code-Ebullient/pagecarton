<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @user   Ayoola
 * @package    Application_User_Email_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Application_User_Email_Exception 
 */
 
require_once 'Application/User/Email/Exception.php';


/**
 * @user   Ayoola
 * @package    Application_User_Email_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Application_User_Email_Abstract extends Ayoola_Abstract_Table
{
	
    /**
     * Whether class is playable or not
     *
     * @var boolean
     */
	protected static $_playable = true;
	
    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = 99;
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'email_id' );
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Application_User_Email_Email';
		
    /**
     * This method secures the application from injection of user_id by a standard user.
     *
     * @param 
     */
    public function getIdentifierUserId()
    {		
		$access = new Ayoola_Access();
		if( ! $userInfo = $access->getUserInfo() ){ return false; }
		if( ! $data = $this->getIdentifierData() ){ return false; }
		if( $userInfo['access_level'] < 4 && $userInfo['user_id'] != $data['user_id'] )
		{ 
			throw new Application_User_Email_Exception( 'ACCESS DENIED' ); 
		}
		return $data['user_id'];
    } 
	
    /**
     * This method secures the application from injection of user_id by a standard user.
     *
     * @param 
     */
    public function getIdentifierUserIdQuery()
    {		
		$access = new Ayoola_Access();
		if( ! $userInfo = $access->getUserInfo() ){ return false; }
	//	if( ! $data = $this->getIdentifierData() ){ return false; }
//	var_export( $access->getUserInfo() );
		if( $userInfo['access_level'] < 4 )
		{ 
			return $userInfo['user_id']; 
		}
		return null;
    } 

    /**
     * This method sets the DbTable to a value
     *
     * @param Ayoola_Dbase_Table_Interface
     */
    public function setDbTable( Ayoola_Dbase_Table_Interface $table = null )
    {		
		if( null === $table ){ $table = $this->getTableClass(); }
		
		//	Retrieve a dbInfo from the DbTable
		$dbInfo = new Application_Database();
		$dbInfo = $dbInfo->selectOne( null, array( 'database' => 'mail' ) );
	//	var_export( $dbInfo );
		$table = new $table( new Ayoola_Dbase( $dbInfo ) );
		$this->_dbTable = $table;
		return $table;
    } 
	
    /**
     * Sets _dbData
     * 
     */
	public function setDbData()
    {
		if( ! $provider = Application_Settings_Abstract::getSettings( 'E-mail', 'provider' ) )
		{
			$provider = 'ayoola';
		}
	//	var_export( $provider );
		switch( $provider )
		{
			case 'ayoola':
				$response = Application_User_Email_Api_List::send( array( 'user_id' => $this->getIdentifierUserIdQuery() ) );
			//	var_export( $this->getIdentifierUserIdQuery() );
			//	var_export( $response );
				if( is_array( $response['data'] ) )
				{
					$this->_dbData = $response['data'];
				}
			break;
			case 'self':
				$table = $this->getDbTable();
				$functionName = function_exists( 'posix_getuid' ) ? 'posix_getuid' : 'getmyuid';
				$this->_dbData = (array) $table->fetchSQLQuery( 'SELECT * FROM `email`, `domain`, `useraccount` WHERE email.domain_id = domain.domain_id AND useraccount.userid = domain.userid AND useraccount.userid = "' . $functionName() . '" ' );	
			break;
		
		}
	//	var_export();
    } 
	
    /**
     * Sets _identifierData
     * 
     */
	public function setIdentifierData( $identifier = null )
    {
		if( is_null( $identifier ) ){ $identifier = $this->getIdentifier(); }
		if( ! $provider = Application_Settings_Abstract::getSettings( 'E-mail', 'provider' ) )
		{
			$provider = 'ayoola';
		}
	//	var_export( $provider );
		switch( $provider )
		{
			case 'ayoola':
				$identifier['user_id'] = $this->getIdentifierUserIdQuery();
				$response = Application_User_Email_Api_List::send( $identifier );
			//	var_export( $identifier );
			//	var_export( $response );
				if( is_array( $response['data'] ) )
				{
					$this->_identifierData = array_pop( $response['data'] );
	//			var_export( $this->_dbData );
				}
			break;
			case 'self':
				$emailId = $identifier['email_id'];
				$table = $this->getDbTable();
				$userAccountInfo =  self::getUserAccountInfo();
				$this->_identifierData = (array) array_pop( $table->fetchSQLQuery( 'SELECT * FROM `email`, `domain`, `useraccount` WHERE email.domain_id = domain.domain_id AND useraccount.useraccount_id = domain.useraccount_id AND useraccount.userid = "' . $userAccountInfo['userid'] . '" AND email.email_id = "' . $emailId . '"', 1 ) );
			break;
		
		}
	//	$this->_identifierData = (array) $table->selectOne( null, $identifier );
    } 
	
    /**
     * 
     * 
     */
	protected static function getUserAccountInfo()
    {
		$userAccountInfo['userid'] =  array();
		$functionName = function_exists( 'posix_getuid' ) ? 'posix_getuid' : 'getmyuid';
		$userAccountInfo['userid'] =  $functionName();
		$processUserInfo =  function_exists( 'posix_getpwuid' ) ? posix_getpwuid( $userAccountInfo['userid'] ) : null;
		$userAccountInfo['username'] =  $processUserInfo['name'] ? : 'UNKWOWN';
		return $userAccountInfo;
    } 
		
    /**
     * creates the form for creating and editing
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue, $legend = null, Array $values = null )
    {
		//	Form to create a new page
        $form = new Ayoola_Form( 'name=>' . $this->getObjectName() );
		$form->oneFieldSetAtATime = true;
		$form->submitValue = 'Continue';
		if( ! $values )
		{

			//	Username
			$fieldset = new Ayoola_Form_Element;
			$fieldset->placeholderInPlaceOfLabel = true;
			$fieldset->addElement( array( 'name' => 'username', 'description' => '', 'style' => 'max-width:40%;display:inline;margin-left:0;', 'type' => 'InputText', 'value' => @$values['username'] ) );
			$fieldset->addRequirement( 'username', array( 'Username' => null ) );
			$fieldset->addLegend( 'Choose e-mail username' );
		//	$form->addFieldset( $fieldset );
			$fieldset->addElement( array( 'name' => 'x', 'type' => 'Html' ), array( 'html' => '@' ) );
			//	Domain
		//	$fieldset = new Ayoola_Form_Element;
			$table = new Application_Domain();
		//	$options = $table->select( null, array( 'sub_domain' => 0 ) );
			$options = $table->select();
			require_once 'Ayoola/Filter/SelectListArray.php';
			$filter = new Ayoola_Filter_SelectListArray( 'domain_name', 'domain_name');
			$options = $filter->filter( $options );
			$fieldset->addElement( array( 'name' => 'domain', 'type' => 'Select', 'style' => 'max-width:40%;display:inline;margin-left:0;', 'value' => @$values['domain'] ), $options );
	//		$fieldset->addRequirement( 'domain', array( 'ArrayKeys' => $options ) );
			$fieldset->addLegend( 'Create a new e-mail' );
			$form->addFieldset( $fieldset );
		}
		else
		{
			$fieldset = new Ayoola_Form_Element;
		
			//	editing options
			$options = array( 'password' => 'Edit Password', 'user' => 'Assign e-mail address to a new user.', );
			$fieldset->addElement( array( 'name' => 'editing_options', 'type' => 'Checkbox', 'value' => @$values['editing_options'] ), $options );
			$form->addFieldset( $fieldset );
		}
		$access = new Ayoola_Access();
		if( ! $userInfo = $access->getUserInfo() ){ null; } 
		if( ( $this->getGlobalValue( 'editing_options' ) && in_array( 'user', $this->getGlobalValue( 'editing_options' ) ) ) || ! $values ) 
		{
			//	commented out to allow it to work in the cloud. Think of another solution later.
		//	if( intval( $userInfo['access_level'] ) > 4 )
			{ 
/* 				//	Assign to user account
				$fieldset = new Ayoola_Form_Element;
				$class = new Application_User_List();
				$options = $class->getDbData();
				
				require_once 'Ayoola/Filter/SelectListArray.php';
		//		var_export( $options );
				$filter = new Ayoola_Filter_SelectListArray( 'username', 'username');
				$options = $filter->filter( $options );
		//		var_export( $options );
				unset
				$fieldset->addElement( array( 'name' => 'user_id', 'type' => 'Select', 'value' => @$values['user_id'] ), $options );
			//	$fieldset->addRequirement( 'user_id', array( 'ArrayKeys' => $options ) );
				$fieldset->addRequirement( 'user_id', array( 'Int' => null ) );
				$fieldset->addLegend( 'Assign E-mail to an Application User' );
				$form->addFieldset( $fieldset );
 */			}
		}
		if( ( $this->getGlobalValue( 'editing_options' ) && in_array( 'password', $this->getGlobalValue( 'editing_options' ) ) ) || ! $values )
		{ 
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addElement( array( 'name' => 'password', 'description' => 'Choose a password for the email', 'type' => 'InputPassword', 'value' => @$values['password'] ) );
			$fieldset->addRequirement( 'password', array( 'WordCount' => array( 5, 100 ) ) );
			$fieldset->addElement( array( 'name' => 'password2', 'description' => 'Confirm Password', 'type' => 'InputPassword', 'value' => @$values['password2'] ) );
			$fieldset->addFilters( array( 'trim' => null ) );
			$fieldset->addElement( array( 'name' => 'application_id', 'type' => 'Hidden' ) );
			$fieldset->addLegend( 'Choose a password' );
			$form->addFieldset( $fieldset );
		}
		$this->setForm( $form );
    } 
	// END OF CLASS
}
