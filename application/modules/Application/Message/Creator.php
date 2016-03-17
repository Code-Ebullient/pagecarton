<?php
/**
 * AyStyle Developer Tool
 *
 * LICENSE
 *
 * @Message   Ayoola
 * @package    Application_Message_Creator
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 * @version    $Id: Creator.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_Message_Abstract
 */
 
require_once 'Application/Message/Abstract.php';


/**
 * @Message   Ayoola
 * @package    Application_Message_Creator
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 */

class Application_Message_Creator extends Application_Message_Abstract
{
	
    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = 0;
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		try
		{ 
			$this->createForm( 'Send', 'Send a private message' );
			$this->setViewContent( $this->getForm()->view(), true );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
		//	var_export( $values );
			
			
			@$values['from'] = $values['from'] ? : Ayoola_Application::getUserInfo( 'username' );
			@$values['to'] = $values['to'] ? : Ayoola_Application::$GLOBAL['username'];
			
			//	There must be a valid sender
			
			if( ! $senderInfo = Ayoola_Access::getAccessInformation( $values['from'] ) )
			{
				return false;
			//	throw new Application_Message_Exception( 'UNABLE TO POST AN UPDATE BECAUSE USER IS INVALID.' );
			}
			if( ! $receiverInfo = Ayoola_Access::getAccessInformation( $values['to'] ) )
			{
				return false;
			//	throw new Application_Message_Exception( 'UNABLE TO POST AN UPDATE BECAUSE USER IS INVALID.' );
			}
			@$values['timestamp'] = $values['timestamp'] ? : time();
			@$values['reference'] = $values['reference'] ? ( (array) $values['reference'] ) : array();
			$values['reference']['from'] = $values['from'];
			$values['reference']['to'] = $values['to'];
			if( $values['from'] == $values['to'] )
			{
			//	return false;
				throw new Application_Message_Exception( 'PRIVATE MESSAGE CANNOT BE SENT TO ONESELF.' );
			}
			if( ! $this->insertDb( $values ) ){ return $this->setViewContent( $this->getForm()->view(), true ); }
			
			//	Send a message to the receiver
		//	$table = new Application_User_NotificationMessage();
		//	$emailInfo = $table->selectOne( null, array( 'subject' => 'Private Message Received' ) ); 
			$emailInfo = array
			(
								'subject' => 'Private Message Received', 
								'body' => '
Dear ' . $receiverInfo['firstname'] . ',
You have just received a new private message from ' . $senderInfo['display_name'] . '. Click the following link to view the message: 

***LINK***
http://' . Ayoola_Page::getDefaultDomain() . '/' . $senderInfo['username'] . '/message
									
								', 
			
			); 
			
			$values = array( 
							//	'firstname' => $receiverInfo['firstname'], 
								'domainName' => Ayoola_Page::getDefaultDomain(), 
							);
			
			$emailInfo = self::replacePlaceholders( $emailInfo, $values + $receiverInfo );
		//	var_export( $emailInfo );
			$emailInfo['to'] = $receiverInfo['email'];
			$emailInfo['from'] = '' . ( Application_Settings_CompanyInfo::getSettings( 'CompanyInformation', 'company_name' ) ? : Ayoola_Page::getDefaultDomain() ) . '<no-reply@' . Ayoola_Page::getDefaultDomain() . '>';
			@self::sendMail( $emailInfo );
			
			$this->setViewContent( '<p class="boxednews goodnews">Private message has been sent successfully.</p>', true );
		}
		catch( Application_Message_Exception $e ){ return false; }
   } 
	// END OF CLASS
}
