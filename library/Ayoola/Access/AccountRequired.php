<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Access_AccountRequired
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: AccountRequired.php 3.6.2012 8.36am ayoola $
 */

/**
 * @see Ayoola_Access_Abstract
 */
 
require_once 'Ayoola/Access/Abstract.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Access_AccountRequired
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Access_AccountRequired extends Ayoola_Abstract_Table
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
	protected static $_accessLevel = 0;
		
    /**
     * Whether to show remember me option
     *
     * @var string
     */
	public static $showRememberMe = false;
		
    /**
     * 
     * @var array
     */
	protected static $_modes = array( 'Ayoola_Access_Login' => 'Yes? Login to an existing account', 'Application_User_Creator' => 'No? Create a new account for free', );
		
    /**
     * 
     * @var array
     */
	protected static $_modeInUse = 'Application_User_Creator';
	
    /**
     * This method performs the class' essense.
     *
     * @param void
     * @return boolean
     */
    public function init()
    {
		$this->createForm( 'Add', 'Add a new product or service.' );
		$this->setViewContent( $this->getForm()->view(), true );
		
		//	Try to login with the form
		if( ! $values = $this->getForm()->getValues() ){ return false; }
		$mode = $this->getGlobalValue( 'mode' ) ? : $this->getObjectStorage()->retrieve();
		if( $mode )
		{
		//	var_export( $mode );
			if( ! Ayoola_Loader::loadClass( $mode ) ) 
			{
			//	throw new Ayoola_Object_Exception( 'INVALID CLASS: ' . $mode );
			}
			$class = new $mode( array( 'signin' => true, 'no_redirect' => true, 'fake_values' => $values ) );
		//	var_export( $values );
			$class->fakeValues = $values;
			$class->initOnce();
	//		if( ! method_exists( $class, 'createForm' ) ){ continue; }
		//	$this->getObjectStorage()->store( $mode );
		}
		$this->setViewContent( Ayoola_Access_Bar::viewInLine(), true );
		
		
	//	$this->setViewContent(  );
	//	$this->setViewContent( $this->getForm()->view() );
    } 
	
    /**
     * Creates the form 
     *
     */
    public function createForm()
    {
		require_once 'Ayoola/Form.php'; 
		$form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$form->submitValue = 'Continue' ;
		$form->oneFieldSetAtATime = true;
		
		//	Check if there is a logged in user
		$auth = new Ayoola_Access();
	//	$auth->isLoggedIn();
		if( ! $auth->isLoggedIn() )
		{ 
			$fieldset = new Ayoola_Form_Element();
			$fieldset->id = __CLASS__;
			$fieldset->placeholderInPlaceOfLabel = true;
			$fieldset->useDivTagForElement = false;
			$fieldset->addElement( array( 'name' => 'mode', 'label' => 'Have you created an account on ' . Ayoola_Page::getDefaultDomain() . ' before?', 'type' => 'Radio', 'value' => @$values['mode'] ), self::$_modes ); 
			$fieldset->addRequirement( 'mode', array( 'ArrayKeys' => self::$_modes ) );
			$form->addFieldset( $fieldset );
			$mode = $this->getGlobalValue( 'mode' ) ? : $this->getObjectStorage()->retrieve();
		//	var_export( $mode );
			if( $mode )
			{
		//	var_export( $mode );
				if( ! Ayoola_Loader::loadClass( $mode ) )
				{
					throw new Ayoola_Object_Exception( 'INVALID CLASS: ' . $mode );
				}
				$class = new $mode();
				if( ! method_exists( $class, 'createForm' ) ){ continue; }
				$fieldsets = $class->getForm()->getFieldsets();
				foreach( $fieldsets as $fieldset )
				{
					$fieldset->appendElement = false;
					$fieldset->getLegend() ? : $fieldset->addLegend( self::$_modes[$mode] );
			//		$fieldset->addElement( array( 'type' => 'html', 'name' => 'e' ), array( 'html' => '<div class="goodnews">' . self::$_requirementOptions[$each]['goodnews'] . '</div>' ) );
					$form->addFieldset( $fieldset );
				}
				$this->getObjectStorage()->store( $mode );
			}
		}
		$this->setForm( $form );
    } 
	// END OF CLASS
}
