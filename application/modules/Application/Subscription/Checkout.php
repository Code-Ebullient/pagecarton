<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Application_Subscription_Checkout
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Checkout.php 5.7.2012 11.53 ayoola $
 */

/**
 * @see Application_Subscription_Abstract
 */
 
require_once 'Application/Subscription/Abstract.php';


/**
 * @category   PageCarton CMS
 * @package    Application_Subscription_Checkout
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Subscription_Checkout extends Application_Subscription_Abstract
{
    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = 0;
	
    /**
     * Unique Order Number
     *
     * @var string
     */
	protected static $_orderNumber;
	
    /**
     * Unique Order Number
     *
     * @var string
     */
	protected static $checkoutStages = array( 0 => 'Payment Failed', 1 => 'Checkout Attempted', 2 => 'Payment Disputed', 99 => 'Payment Successful' );
	
    /**
     * Default Database Table
     *
     * @var string
     */
	protected $_tableClass = 'Application_Subscription_Checkout_CheckoutOption';
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
	//	$response = self::fetchLink( 'http://pmcsms.com/' );
	//	var_export( $response );
		try
		{
		if( ! $cart = self::getStorage()->retrieve() )
		{ 
			return $this->setViewContent( '<span class="boxednews centerednews badnews">You have no item in your shopping cart.</span>', true );
		}
	//	var_export( self::getOrderNumber() );
	//	var_export( $cart );
		//	Record in the orders table
		$this->setViewContent( $this->getForm()->view() );
		if( ! $values = $this->getForm()->getValues() ){ return false; }

		//	Dont save plaintext password
		unset( $values['password'] );
		unset( $values['password2'] );
		
		//	Put the checkout info in the cart
		$cart = self::getStorage()->retrieve();
		$cart['checkout_info'] = $values;
		self::getStorage()->store( $cart );
		
		//	Notify Admin
		$mailInfo = array();
		$mailInfo['subject'] = 'Checkout Attempted.';
		$mailInfo['html'] = true; 
		$mailInfo['body'] = '   
						<html>
						<body>
						Someone just attempted to checkout. Here is the cart content<br>
						' . Application_Subscription_Cart::viewInLine() . '<br>
						Subscription options are available on: http://' . Ayoola_Page::getDefaultDomain() . '/ayoola/subscription/.<br>
						</body></html>       
		
		
		';
		try
		{
		//	var_export( $newCart );
			$mailInfo['to'] = Application_Settings_CompanyInfo::getSettings( 'CompanyInformation', 'email' );
			@self::sendMail( $mailInfo );
	//		Ayoola_Application_Notification::mail( $mailInfo );
		}
		catch( Ayoola_Exception $e ){ null; }
		
	//	var_export( $orderInfo );
		$api = self::getApi( $values['checkoutoption_name'] );
		$this->setViewContent( $api::viewInLine(), true );
	//	if( ! $values = $this->getForm()->getValues() )
		{ 
		//	$this->setViewContent( $this->getForm()->view() );
		}

		}
		catch( Exception $e )
		{
			$this->getForm()->setBadnews( $e->getMessage() ); 
			$this->setViewContent( $this->getForm()->view(), true );
		}
    } 
	
    /**
     * Plays the API that is selected
     * 
     */
	public static function getApi( $checkoutOptionName )
    {
		//if( ! $values = $this->getForm()->getValues() ){ return false; }
		$table = new Application_Subscription_Checkout_CheckoutOption();
		$data = $table->selectOne( null, array( 'checkoutoption_name' => $checkoutOptionName ) );
	//	var_export( $data );
		$className = __CLASS__ . '_' . $data['checkoutoption_name'];
		require_once 'Ayoola/Loader.php';
		if( ! Ayoola_Loader::loadClass( $className ) )
		{ 
			throw new Application_Subscription_Exception( 'INVALID CHECKOUT API' ); 
		}
		
		return $className;
    } 
	
    /**
     * Returns the current order number
     * 
     */
	public static function getOrderNumber( $orderApi = null )
    {
		$storage = new Ayoola_Storage();
		$storage->storageNamespace = __CLASS__ . 'orderInfo';
		if( ! $orderApi )
		{ 
			$storage->clear(); 
			return;
		}
		if( is_null( self::$_orderNumber ) )
		{
			//	Store order number to avoid multiple table insert
			$orderInfo = $storage->retrieve();
			$cart = self::getStorage()->retrieve();
	//		var_export( $cart );
	//		var_export( $orderApi );
			if( ! $orderInfo || ( $orderInfo['cart_id'] != md5( serialize( $cart ) ) || ( $orderInfo['order_api'] != $orderApi ) ) )
			{
				$table = new Application_Subscription_Checkout_Order();
				$insertInfo = $table->insert( array( 'order' => serialize( $cart ), 'currency' => $cart['settings']['currency_abbreviation'], 'order_api' => $orderApi, 'username' => Ayoola_Application::getUserInfo( 'username' ), 'order_status' => self::$checkoutStages[1] ) );
				$orderNumber = $insertInfo['insert_id'];
				$orderInfo = array();
				$orderInfo['cart_id'] = md5( serialize( $cart ) );
				$orderInfo['order_number'] = $orderNumber;
				$orderInfo['order_api'] = $orderApi;
				
				$storage->store( $orderInfo );
			}
			self::$_orderNumber =  $orderInfo['order_number'];
		}
		
		return self::$_orderNumber;
    } 
	
    /**
     * Checks if the checkout api supports our currency,
     * 
     */
	public static function isValidCurrency( $currency = null )
    {		
		if( is_null( $currency ) )
		{
			if( ! $values = self::getStorage()->retrieve() ){ return; }
			$currency = $values['settings']['currency_abbreviation'];
		}
		if
		( 
			( ( stripos( static::$_currency['whitelist'], $currency ) !== false ) 
				|| ( stripos( static::$_currency['whitelist'], 'ALL' ) !== false )
			)
			||
			( ( stripos( static::$_currency['blacklist'], $currency ) === false ) 
				&& ( stripos( static::$_currency['blacklist'], 'ALL' ) === false ) 
			)
		) 
		{
			
			return true;
		}
		
		//	$this->setViewContent( $this->getForm()->view() );
		return false;
    } 
	
    /**
     * Creates the form for checkout
     * 
     */
	public function createForm()
    {
		$form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
		
		if( $cart = self::getStorage()->retrieve() )
		{ 
			
			//	Look for checkout requirements
			$requirements = array();
			foreach( $cart['cart'] as $name => $value )
			{
 				$value['checkout_requirements'] = is_string( $value['checkout_requirements'] ) ? array_map( 'trim', explode( ',', $value['checkout_requirements'] ) ) : $value['checkout_requirements'];
				$value['checkout_requirements'] = is_array( $value['checkout_requirements'] ) ? $value['checkout_requirements'] : array();
				$requirements += @$value['checkout_requirements'];
 			}
			if( $requirements )
			{
				if( ! $this->getParameter( 'all_form_elements_at_once' ) )
				{
					$form->oneFieldSetAtATime = true;
					$form->submitValue = 'Continue checkout...';
				}
				$form->submitValue = 'Continue checkout...';
			}
			else
			{
				$form->submitValue = 'Continue checkout...';
			}

		//	var_export( $requirements );
			self::setFormRequirements( $form, $requirements );
		}
		
		$fieldset = new Ayoola_Form_Element();		
		
		$options = new Application_Subscription_Checkout_CheckoutOption();
		$options = $options->select();
		$allowedOptions = Application_Settings_Abstract::getSettings( 'Payments', 'allowed_payment_options' ) ? : array();
		foreach( $options as $key => $each )
		{
			$api = 'Application_Subscription_Checkout_' . $each['checkoutoption_name'];
		//	$options[$key]['checkoutoption_logo'] = $each['checkoutoption_logo'] . htmlspecialchars( '<br />' );
			$options[$key]['checkoutoption_logo'] = $each['checkoutoption_logo']; 
			if( ! $api::isValidCurrency() ){ unset( $options[$key] ); }
			if( ! in_array( $each['checkoutoption_name'], $allowedOptions ) ){ unset( $options[$key] ); }
		//	var_export( $api::isValidCurrency() );
		}
	//	var_export( $options );
		require_once 'Ayoola/Filter/SelectListArray.php';
		$filter = new Ayoola_Filter_SelectListArray( 'checkoutoption_name', 'checkoutoption_logo');
		$options = $filter->filter( $options );
												
		$editLink = self::hasPriviledge() ? ( '<a class="badnews" rel="spotlight;" title="Change organization contact information" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Settings_Editor/settingsname_name/Payments/">(edit payment informaton)</a>' ) : null; 

		$fieldset->addElement( array( 'name' => 'checkoutoption_name', 'label' => 'We accept the following methods of payment for your order. Please select the most convenient payment option for you. ' , 'type' => 'Radio', 'value' => @$values['checkoutoption_name'] ), $options );
// 		$fieldset->addRequirement( 'checkoutoption_name', array( 'InArray' => array_keys( $options ) ) );
	//	$fieldset->addRequirements( array( 'NotEmpty' => null  ) );

//		$fieldset->addElement( array( 'name' => 'api-checkout', 'value' => 'Checkout', 'type' => 'Submit' ) );
		$fieldset->addLegend( 'Please select your preferred payment method ' . $editLink );
		$form->addFieldset( $fieldset );
		$this->setForm( $form );
    }
	// END OF CLASS
}
