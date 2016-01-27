<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Abstract_Viewable
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php 4.26.2012 10.08am ayoola $
 */

/**
 * @see Ayoola_Exception 
 * @see Ayoola_Object_Interface_Viewable 
 */
 
require_once 'Ayoola/Exception.php';
require_once 'Ayoola/Abstract/Viewable.php';

/**
 * @category   PageCarton CMS
 * @package    Ayoola_Abstract_Viewable
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Ayoola_Abstract_Viewable implements Ayoola_Object_Interface_Viewable
{

    /**
     * Data storage
     *
     * @var Ayoola_Storage
     */
	protected $_objectStorage;

    /**
     * Useful for lists
     *
     * @var Ayoola_Paginator
     */
	protected $_list;
	
    /**
     * Content for the view method
     *
     * @var string XML Document
     */
	protected $_viewContent;
	
    /**
     * Markup template to sent from layout template for this view
     *
     * @var string
     */
	protected $_markupTemplate;
	
    /**
     * Whether to wrap _viewContent in a tag
     *
     * @var boolean
     */
	public $wrapViewContent = true;
	
    /**
     * Whether to hash the form elements name as an antibot mechanism
     *
     * @var boolean
     */
	public $hashFormElementName = true;
	
    /**
     * 
     */
	protected $_viewParameter;
	
	
    /**
     * Integrated view, option and other parameters.
     * 
     * var array
     */
	protected $_parameter = array();
	
    /**
     * Values to use to replace placeholders in the markup templates
     * 
     * var array
     */
	protected $_objectTemplateValues = array();
	
    /**
     * Use to count the instance
     * 
     * @var int
     */
	protected static $_counter = 0;
	
    /**
     * For editable div in layout editor
     * 
     * @var string
     */
	protected static $_editableTitle;
	
    /**
     * For editable div in layout editor
     * 
     * @var string
     */
	protected static $_editableHideTitle = "Hide";

    /**
     * The Options Available as a Viewable Object
     * This property makes it possible to use this same class
     * To serve all document available on the site
     * 
     * @var array
     */
	protected $_classOptions;

    /**
     * The option value selected
     * 
     * @var mixed
     */
	protected $_viewOption;
	
    /**	Object Name
     *
     * @var string
     */
	public $objectName;
	
    /**	Set to true if the init method has been run
     *
     * @var boolean
     */
	public $initiated = false;
	
    /**	
     *
     * @var boolean
     */
	public static $openViewParametersByDefault = true;
	
    /** The tag of the element used in preparing the view content.	
     *
     * @var string
     */
	protected static $_viewContentElementContainer = 'span';
	
 
    /**
     * My User Agent Name
     * 
     * @var string
     */
	public static $userAgent = 'Mozilla/5.0 ( compatible; ayoolabot/0.1; +http://ayoo.la/bot/ )';
 
	/**
     * constructor
     * 
     */
	public function __construct( $parameter = null )
    {
	//	self::v( $parameter );
		if( ! $parameter )
		{
			if( Ayoola_Application::isXmlHttpRequest() || Ayoola_Application::isClassPlayer() ){ return null; }
		}
		if( is_array( $parameter ) ){ $this->setParameter( $parameter ); }
		$this->initOnce();
		static::$_counter++;
    } 
 	
    /**
     * 
     * 
     */
	protected function initOnce()
	{ 
/*
		if( ! is_null( $this->getParameter( 'access_level_whitelist' ) ) )
		{
			//	compatibility
			if( $this->getParameter( 'access_level_whitelist' ) != Ayoola_Application::getUserInfo( 'access_level' ) )
			{
		//		var_export( $this->getParameter( 'access_level_whitelist' ) );
		//		var_export( Ayoola_Application::getUserInfo( 'access_level' ) );
		//		$this->initiated = true;
				return false;
			}
		}
 */		if( ! $this->initiated && ! $this->getParameter( 'no_init' ) ) //	compatibility
		{
			$this->initiated = true;
			if( $this->init() )
			{
/* 				//	find out if we are running embeded objects in stages.
				$class = new Ayoola_Object_Embed();
				if( $class->getStorage( 'current' ) === get_class( $this ) )
				{
					//	We are done with the current object. Step up
					
				
				}
 */			}
		}
	} 
 	
    /**
     * default the class initialization process
     * 
     */
	protected function init(){ } 
	
    /**
     * shares the profile
     * 
     */
	public static function getShareLinks( $fullUrl )    
    {
		return '
				<!-- I got these buttons from simplesharebuttons.com -->
				<style type="text/css">
					.share-buttons img {
					width: 35px;
					padding: 5px;
					border: 0;
					box-shadow: 0;
					display: inline;
					}
				</style>
				<div class="share-buttons" >
					Share on | 
					<!-- Facebook -->
					<a href="http://www.facebook.com/sharer.php?u=' . $fullUrl . '" target="_blank" title="Share on Facebook"><img src="/social-media-icons/facebook.png" alt="Facebook" /></a>
					 
					<!-- Twitter -->
					<a href="http://twitter.com/share?url=' . $fullUrl . '&text=I think you might like this...&hashtags=" target="_blank" title="Share on Twitter"><img src="/social-media-icons/twitter.png" alt="Twitter" /></a>
					   
					<!-- Google+ -->
					<a href="https://plus.google.com/share?url=' . $fullUrl . '" target="_blank" title="Share on Google+"><img src="/social-media-icons/google-plus.png" alt="Google" /></a>
					 
					<!-- LinkedIn -->
					<a href="http://www.linkedin.com/shareArticle?mini=true&url=' . $fullUrl . '" target="_blank" title="Share on LinkedIn"><img src="/social-media-icons/linkedin.png" alt="LinkedIn" /></a>
					 
					<!-- Email -->
					<a href="mailto:?Subject=Check out this link...&Body=I%20saw%20this%20and%20thought%20of%20you!%20 ' . $fullUrl . '" title="Share via E-mail"><img src="/social-media-icons/email.png" alt="Email" /></a>
				</div>
		';
	}
	
    /**
     * Sets the list
     * 
     * @param Ayoola_Paginator
     */
	public function setList( Ayoola_Paginator $list = null )
    {
		if( is_null( $list ) ){ $list = $this->createList(); }
		$this->_list = $list;
    } 
	
    /**
     * Returns the storage object
     * 
     * @param string Unique ID for Namespace
     * @return Ayoola_Storage
     */
	public function getObjectStorage( $storageInfo = null )
    {
		$id = null;
		$device = null;
		if( is_string( $storageInfo ) )
		{
			$id = $storageInfo;
		}
		elseif( is_array( $storageInfo ) )
		{
			$id = $storageInfo['id'];
			$device = @$storageInfo['device'];
			$timeOut = @$storageInfo['time_out'];  
		}
		
		if( @$this->_objectStorage[$id] ){ return $this->_objectStorage[$id]; }
		$this->_objectStorage[$id] = new Ayoola_Storage();
		$this->_objectStorage[$id]->storageNamespace = $this->getObjectName() . '-' . $id; 
		$this->_objectStorage[$id]->timeOut = @$timeOut;   
		$device ? $this->_objectStorage[$id]->setDevice( $device ) : null; 
		return $this->_objectStorage[$id];
    }
	
    /**
     * Sends email
     * 
     */
	public static function sendMail( array $mailInfo )
    {
//		var_export( $mailInfo );
//		var_export( empty( $mailInfo['body'] ) );
		if( empty( $mailInfo['body'] ) ){ throw new Ayoola_Abstract_Exception( 'E-mail cannot be sent without a body' ); }
		if( empty( $mailInfo['to'] ) ){ throw new Ayoola_Abstract_Exception( 'E-mail destination was not specified' ); }
		if( empty( $mailInfo['from'] ) )
		{ 
		//	$mailInfo['from'] = 'no-reply@' . Ayoola_Page::getDefaultDomain(); 
			$mailInfo['from'] = '' . ( Application_Settings_CompanyInfo::getSettings( 'CompanyInformation', 'company_name' ) ? : Ayoola_Page::getDefaultDomain() ) . ' <no-reply@' . Ayoola_Page::getDefaultDomain() . '>' . "\r\n";
		}
		if( empty( $mailInfo['subject'] ) ){ $mailInfo['subject'] = 'Account Notice'; }
		$header = 'From: ' . $mailInfo['from'] . "";
		if( ! empty( $mailInfo['bcc'] ) )
		{ 
			$header .= "bcc: {$mailInfo['bcc']}\r\n";
		//	var_export( $header );
		}
		if( ! empty( $mailInfo['html'] ) )
		{ 
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		}
		$sent = mail( $mailInfo['to'], $mailInfo['subject'], $mailInfo['body'], $header );
	//	exit( var_export( $mailInfo ) );
	//	if( ! $sent ){ throw new Ayoola_Abstract_Exception( 'Error encountered while sending e-mail' ); }
		return true;
    } 
  	
    /** 
     * Fetches a remote link
     *
     * @param string Link to fetch
     * @param array Settings
     */
    public static function fetchLink( $link, array $settings = null )
    {	
		if( ! function_exists( 'curl_init' ) )
		{
			trigger_error( __METHOD__ . ' WORKS BETTER WHEN CURL IS ENABLED. PLEASE ENABLE CURL ON YOUR SERVER.' );
			return file_get_contents( $link );
		}
		$request = curl_init( $link );
//		curl_setopt( $request, CURLOPT_HEADER, true );
		curl_setopt( $request, CURLOPT_URL, $link );
		curl_setopt( $request, CURLOPT_USERAGENT, @$settings['user_agent'] ? : self::$userAgent );
		curl_setopt( $request, CURLOPT_AUTOREFERER, true );
		curl_setopt( $request, CURLOPT_REFERER, @$settings['referer'] ? : $link );
		curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $request, CURLOPT_FOLLOWLOCATION, @$settings['follow_redirect'] === false ? false : true ); //	By default, we follow redirect
		curl_setopt( $request, CURLOPT_CONNECTTIMEOUT, @$settings['connect_time_out'] ? : 10 );	//	Max of 1 Secs on a single request
		curl_setopt( $request, CURLOPT_TIMEOUT, @$settings['time_out'] ? : 10 );	//	Max of 1 Secs on a single request
		if( @$settings['post_fields'] )
		{
			curl_setopt( $request, CURLOPT_POST, true );
			curl_setopt( $request, CURLOPT_POSTFIELDS, $settings['post_fields'] );
		}
		if( is_array( @$settings['http_header'] ) )
		{
			curl_setopt( $request, CURLOPT_HTTPHEADER, $settings['http_header'] );
		}
		$response = curl_exec( $request );
		$responseOptions = curl_getinfo( $request );

			// close cURL resource, and free up system resources
		curl_close( $request );
	//	var_export( htmlentities( $response ) );
		
 		//	var_export( $responseOptions );
	//	exit( var_export( $responseOptions ) );
		//	var_export( $settings['post_fields'] );
 	//	if( ! $response || $responseOptions['http_code'] != 200 ){ return false; }
 		if( $responseOptions['http_code'] != 200 ){ return false; }
		if( @$settings['return_as_array'] == true )
		{ 
			$response = array( 'response' => $response, 'options' => $responseOptions );
		}
 		//	var_export( $response );
		return $response;
    } 
	
    /**
     * Returns the list
     * 
     * @param void
     * @return Ayoola_Paginator
     */
	public function getList()
    {
		if( is_null( $this->_list ) ){ $this->setList(); }
		return $this->_list->view(); 
    } 
	
    /**
     * Check if I have privilege to access a resource
     * 
     * param array Allowed Access Levels
     * return boolean
     */
	public static function hasPriviledge( $allowedLevels = null, array $options = null )   
	{
//		var_export( Ayoola_Application::getUserInfo( 'access_level' ) );
		//var_export( intval( Ayoola_Application::getUserInfo( 'access_level' ) ) );
		if( is_array( $allowedLevels ) )
		{
			$allowedLevels = array_map( 'intval', $allowedLevels );
		}
		elseif( $allowedLevels === 0 || $allowedLevels === '0'  )
		{
			$allowedLevels = array( 0 );
		}
		else
		{
			$allowedLevels = array();
		}
//		var_export( in_array( 0, $allowedLevels ) );
		$myLevel = intval( Ayoola_Application::getUserInfo( 'access_level' ) );
		//	var_export( $allowedLevels );
	//		var_export( $myLevel );
	//	var_export( ( in_array( 0, $allowedLevels ) && ! @$options['strict'] ) );
	//	var_export( in_array( $myLevel, $allowedLevels ) );
	//	var_export( $myLevel === 99 );
		if( $myLevel === 99 // Super user
	//	|| ( in_array( 98, $allowedLevels ) &&  ) //	Profile owner means he is authorized
		|| ( in_array( 0, $allowedLevels ) && ! @$options['strict'] ) //	Public means everyone is welcome except if its strict
		|| in_array( $myLevel, $allowedLevels ) //	We are explicitly allowed
		|| ( $_SERVER['REMOTE_ADDR' ] === '127.0.0.1' ) //	Localhost
		)
		{ 
			//	We are either a super user, or has a listed allowed user or the resource is public
			return true;
		}
	//	else		if
		//	MyLevel now has capabilities of inheriting from  other levels
		$authLevel = new Ayoola_Access_AuthLevel;
		$authLevel = $authLevel->selectOne( null, array( 'auth_level' => $myLevel ) );
		require_once 'Ayoola/Filter/SelectListArray.php';
	//	if( $myLevel == 5 )
		{
		//	var_export( $authLevel );
		//	exit();
		}
		$authLevel['parent_access_level'] = @$authLevel['parent_access_level'] ? : array();
	//	var_export( $authLevel );
		foreach( $authLevel['parent_access_level'] as $each )
		{
			if( $each < 10 )
			{
			//	if( $myLevel == 5 )
				{
				//	var_export( $each );
				//	exit();
				}
				if( in_array( $each, $allowedLevels ) )
				{
					return true;
				}
			}
		}
	//	if( $myLevel == 5 )
		{
		//	var_export( $authLevel );
	//		exit();
		}
		$access = new Ayoola_Access();
		$userInfo = $access->getUserInfo();
		@$userInfo['profiles'] = is_array( $userInfo['profiles'] ) ? $userInfo['profiles'] : array();
		
		//	$previous = Ayoola_Page::setPreviousUrl( '' );
	//		var_export( $userInfo );    
		  
		if( in_array( 98, $allowedLevels ) && ! empty( Ayoola_Application::$GLOBAL['profile_url'] ) && is_array( $userInfo['profiles'] ) && in_array( Ayoola_Application::$GLOBAL['profile_url'], $userInfo['profiles'] ) ) //	profile owner
		{
			return true;
		}
	
		
		//	No way jose
		return false;
	}
	
    /**
     * Used by administrators to inspect variables for debugging purposes.
     * 
     */
	public static function v( $variable )
    {
		if( self::hasPriviledge() )
		{ 
			var_export( $variable );
		}
	}

    /**
	 * Returns text for the "interior" of the Layout Editor
	 * The default is to display view and option parameters.
	 * 		
     * @param array Object Info
     * @return string HTML
     */
    public static function getHTMLForLayoutEditor( $object )
	{
		$html = null;
		@$object['view'] = $object['view'] ? : $object['view_parameters'];
		@$object['option'] = $object['option'] ? : $object['view_option'];
//		$html .= "<span data-parameter_name='view' >{$object['view']}</span>";
		
		//	Implementing Object Options
		//	So that each objects can be used for so many purposes.
		//	E.g. One Class will be used for any object
	//	var_export( $object );
		if( method_exists( $object['class_name'], 'getClassOptions' ) )
		{
			$options = $object['class_name'];
			$options = new $options( array( 'no_init' => true ) ); 
	//		$options = array();
			$options = (array) $options->getClassOptions();
			$html .= '<select data-parameter_name="option">';
			foreach( $options as $key => $value )
			{ 
				$html .=  '<option value="' . $key . '"';  
			//	var_export( $object['view'] );
				if( $object['option'] == $key ){ $html .= ' selected = selected '; }
				$html .=  '>' . $value . '</option>';  
			}
			$html .= '</select>';
		}	
		return $html;
	}

    /**
     * Produce the mark-up for each viewable object
     *
     * @param array viewableObject Information
     * @return string Mark-Up to Display Viewable Objects
     */
    protected static function getViewableObjectRepresentation( array $object )
    {
		
		$html = null;
		$object['object_unique_id'] = @$object['object_unique_id'] ? : ( md5( $object['object_name'] ) . rand( 100, 1000 ) );
		$advancedName = 'advanced_parameters_' . $object['object_unique_id'] . '';
		$html .= "<div class='DragBox' id='" . $object['object_unique_id'] . "' title='Move this object by dragging it around - " . $object['view_parameters'] . "' data-object_name='{$object['object_name']}' >";
		
		//	title bar
		$html .= '<div style="" title="' . $object['view_parameters'] . '" class="title_bar" data-parameter_name="parent">'; 
		
		
		//	Delete button
		$html .= '<span class="title_button close_button" style="" name="" href="javascript:;" class="" title="Delete this object" onclick="this.parentNode.parentNode.parentNode.removeChild( this.parentNode.parentNode );"> x </span>';
		
		//	Maximize
		$html .= '<a class="title_button" name="' . $advancedName . '" href="javascript:;" class="" title="Click to show or hide advanced settings" onclick="  var b = document.getElementsByName( this.name );for( var a = 0; a < b.length; a++ ){  b[a].style.display = ( b[a].style.display == \'none\' ) ? \'\' : \'none\'; this.style.display = \'\'; } "> &square; </a>'; 
		
		//	Minimize
		$html .= '<a class="title_button" name="' . $advancedName . '_interior" href="javascript:;" class="" title="Minimize or open the body of this object" onclick="  var b = document.getElementsByName( this.name );for( var a = 0; a < b.length; a++ ){  b[a].style.display = ( b[a].style.display == \'none\' ) ? \'\' : \'none\'; this.style.display = \'\'; } "> _ </a>'; 
		
		//	title
		$html .= '<span style="">' . $object['view_parameters'] . '</span>';
		$html .= '<div style="clear:both;"></div>';		
		
		$html .= '</div>';	//	 title bar  
		
		//	advanced options
		$html .= '<div style="border: #ccc 1px solid;padding:0.5em;padding:0 0.5em 0 0.5em;" title="" class="status_bar" data-parameter_name="parent">'; 
		$html .= '<div style="clear:both;display:none;" name="' . $advancedName . '"><strong>Inject some parameters to this object...</strong></div>';		

			$form = new Ayoola_Form( array( 'name' => $advancedName, 'data-parameter_name' => 'advanced_parameters', 'style' => 'display:none;' ) );
			$i = 0;
		//	$object['advanced_parameter_name'] = html_entity_decode( @$object['advanced_parameter_name'] );
	//		var_export( $object['advanced_parameter_name'] );
		//	var_export( parse_str( $object['advanced_parameter_name'] ) );
		//	var_export( @$object['advanced_parameter_name'] );
			parse_str( @$object['advanced_parameters'], $advanceParameters );
		//	var_export( $advanceParameters );
			do
			{
				
			//	$each = array_pop( $object['advanced_parameter_name'] );
				
				$fieldset = new Ayoola_Form_Element; 
				$fieldset->hashElementName = false;
				$fieldset->container = 'div';
			//	$fieldset->addLegend( 'Inject some parameters to this object...' );
			//	$fieldset->addLegend( ' ' );
			//	$form->submitValue = $submitValue ;
			//	$form->oneFieldSetAtATime = true;
				$form->wrapForm = false;
				$fieldset->addElement( array( 'name' => 'advanced_parameter_name[]', 'label' => '', 'placeholder' => 'Parameter Name', 'type' => 'InputText', 'value' => @$advanceParameters['advanced_parameter_name'][$i] ) );
				$fieldset->addElement( array( 'name' => 'advanced_parameter_value[]', 'label' => '', 'placeholder' => 'Parameter Value', 'type' => 'InputText', 'value' => @$advanceParameters['advanced_parameter_value'][$i] ) );
				$fieldset->allowDuplication = true;
				$fieldset->placeholderInPlaceOfLabel = true;
				$form->addFieldset( $fieldset );
				$i++;
			}
			while( ! empty( $advanceParameters['advanced_parameter_name'][$i] ) );
			$options = new Ayoola_Access_AuthLevel;
			$options = $options->select();
			require_once 'Ayoola/Filter/SelectListArray.php';
			$filter = new Ayoola_Filter_SelectListArray( 'auth_level', 'auth_name' );
			$options = $filter->filter( $options );
			$fieldset = new Ayoola_Form_Element; 
			$fieldset->hashElementName = false;
		//	$fieldset->addLegend( 'Select user groups that would be able to view this object...' );
			$fieldset->addElement( array( 'name' => 'xx', 'type' => 'Html' ), array( 'html' => '<p><strong>Which user groups can view this object...</strong></p>' ) );
			$fieldset->addElement( array( 'name' => 'object_access_level', 'id' => $object['object_unique_id'] . '_object_access_level', 'label' => ' ', 'placeholder' => '', 'type' => 'Checkbox', 'value' => @$advanceParameters['object_access_level'] ), $options );
			$fieldset->placeholderInPlaceOfLabel = true;
			$form->addFieldset( $fieldset );
			$html .= $form->view();

		$html .= '</div>';	//	advanced options
				
		//	Retrieving object "interior" from the object class
		
		//	Determine if its opening or closing inside the "object".
		$openOrNot = static::$openViewParametersByDefault ? '' : 'display:none;';
		$html .= '<div  title="' . $object['view_parameters'] . '" style="' . $openOrNot . ' padding-top:0.5em;padding-bottom:0.5em;cursor: default;" name="' . $advancedName . '_interior" class="" data-parameter_name="parent">'; //	interior parent
		$getHTMLForLayoutEditor = 'getHTMLForLayoutEditor';
		if( method_exists( $object['class_name'], $getHTMLForLayoutEditor ) )
		{
			$html .= $object['class_name']::$getHTMLForLayoutEditor( $object );
		}
		//	var_export( $object );
		if( @$object['call_to_action'] )
		{
			$html .= '<textarea name="' . $advancedName . '" placeholder="Enter HTML for a Call-To-Action" data-parameter_name="call_to_action" style="display:none;width:100%;" onclick="">' . @$object['call_to_action'] . '</textarea>';
		}
		if( @$object['markup_template_namespace'] )
		{
			$html .= '<input name="' . $advancedName . '" placeholder="Choose a namespace for HTML template" data-parameter_name="markup_template_namespace" style="display:none;width:100%;" onclick="" value="' . @$object['markup_template_namespace'] . '" />';
		}
		if( @$object['markup_template'] )
		{
			$html .= '<textarea name="' . $advancedName . '" placeholder="Enter HTML template to use" data-parameter_name="markup_template" style="display:none;width:100%;" onclick="">' . @$object['markup_template'] . '</textarea>';
		}
		if( static::$_editableTitle ) 
		{
		//	$html .= '<button href="javascript:;" title="' . static::$_editableTitle . '"  class="" onclick="ayoola.div.makeEditable( this.nextSibling ); this.nextSibling.style.display=\'block\';"> edit </button>';   
				//	var_export( $object );
			$html .= '<span data-parameter_name="editable" style="display:block;min-height:1em;" contentEditable=true onclick="" >' . ( @$object['editable'] ? : static::$_editableTitle ) . '</span>';
		//	$html .= '<button href="javascript:;" style="display:none;" class="" title="' . static::$_editableTitle . '" onclick="this.previousSibling.style.display=\'none\';this.style.display=\'none\';"> hide </button>';
		}
		
		$html .= '</div>';	//	 interior
		
		//	status bar
		$html .= '<div name="' . $advancedName . '_interior" style="' . $openOrNot . '" title="' . $object['view_parameters'] . '" class="status_bar">'; 
				
		//	Export
		$html .= '<a class="title_button" title="Import or export object" name="" href="javascript:;" onclick="var a = window.prompt( \'Copy to clipboard: Ctrl+C, Enter\', this.parentNode.parentNode.outerHTML ); if( a ){ this.parentNode.parentNode.outerHTML = a; }">&#8635;</a>'; 
				
		//	Help
		$html .= '<a class="title_button" title="Seek help on how to use this page editor" name="" href="http://pagecarton.com" onclick="this.target=\'_new\'">?</a>'; 
		$html .= method_exists( $object['class_name'], 'getStatusBarLinks' ) ? static::getStatusBarLinks( $object ) : null; 
		
		$html .= '<div style="clear:both;"></div>';
		$html .= '</div>';	//	 status bar
		
		

		$html .= "</div>";
		return $html;
    }

    /**
	 * Replacing setViewOption and setViewParameter with a universal method
	 * 		
     * @param array Parameters meant for this object
     */
    public function setParameter( array $parameters ) 
	{
		
		if( ! empty( $parameters['advanced_parameters'] ) )
		{ 
			parse_str( $parameters['advanced_parameters'], $advanceParameters );
			@$advanceParameters = array_combine( $advanceParameters['advanced_parameter_name'], @$advanceParameters['advanced_parameter_value'] ) ? : array();
		//	var_export( $advanceParameters );
			$parameters += $advanceParameters;
			unset( $parameters['advanced_parameters'] );
		}
		if( isset( $parameters['view'] ) ){ $this->setViewParameter( $parameters['view'] ); }
		if( isset( $parameters['editable'] ) ){ $this->setViewParameter( $parameters['editable'] ); }
		if( isset( $parameters['option'] ) ){ $this->setViewOption( $parameters['option'] ); }
		$this->_parameter = array_merge( $this->_parameter, $parameters );
	}

    /**
	 * Return $parameters
	 * 		
     * @param string If set, method returns value of $parameters[$key]
     * @return array $parameters
     */
    public function getParameter( $key = null )
	{
		if( is_null( $key ) )
		{
			return $this->_parameter;
		}
		if( array_key_exists( $key, $this->_parameter ) )
		{
			return $this->_parameter[$key];
		}
	//	throw new Ayoola_Exception( 'KEY IS NOT AVAILABLE IN PARAMETERS: ' . $key );
	}

    /**
	 * Just incoporating this - So that the layout can be more interative
	 * The layout editor will be able to pass a parameter to the viewable object				
     * @param mixed Parameter set from the layout editor
     * @return null
     */
    public function setViewParameter( $parameter )
	{
//	var_Export( __LINE__ );
		$this->_viewParameter = $parameter ;
		
		//	compatibility.
		$this->_parameter['view'] = $parameter;
	}
	
    public function setViewOption( $parameter )
	{
		$this->_viewOption = $parameter ;
		
	//	var_export( $parameter );
		
		//	compatibility.
		$this->_parameter['option'] = $parameter;
	}
	
    /**
     * Returns object_name will become the form name or id
     * 
     */
	 protected function getObjectName( $className = null )
	 {
		if( $this->objectName )
		{
			return $this->objectName;
		}
		$className = $className ? : get_class( $this );
		$objectName = new Ayoola_Object_Table_ViewableObject();
	//	var_export( $className );
		$objectName = $objectName->selectOne( null, array( 'class_name' => $className ) );
	//	var_export( $objectName );
		@$objectName = $objectName['object_name'] ? : $className;
	//	var_export( $objectName );
		$this->objectName = $objectName;
		return $objectName;
	 }
 
    /**
	 * Sets the _viewContent
	 *
     */
    public function setViewContent( $content = null, $refresh = false )
	{
	//	if( is_object( $content ) ) var_export( $content );
		if( is_object( $content ) ){ $content = $content->view(); }
		if( ! trim( $content ) )
		{
		//	var_export( $content );
			//	don't return empty tags
			return false;
		}
		if( null === $this->_viewContent || true === $refresh )
		{ 
			$this->_viewContent = new Ayoola_Xml();
			if( $this->wrapViewContent && ! $this->getParameter( 'no_view_content_wrap' ) )
			{
				$documentElement = $this->_viewContent->createElement( static::$_viewContentElementContainer );  
				$documentElement->setAttribute( 'data-object-name', $this->getObjectName() );
				$documentElement->setAttribute( 'name', $this->getObjectName() . '_container' );
				$this->_viewContent->appendChild( $documentElement );
				
				//	Use Named Anchor to reference this content
				$a = $this->_viewContent->createElement( 'span' );
				$a->setAttribute( 'name', $this->getObjectName() );
				$documentElement->appendChild( $a );
			}
		}
		$contentData = $this->_viewContent->createCDATASection( $content );
		if( $this->wrapViewContent && ! $this->getParameter( 'no_view_content_wrap' ) )
		{
			$contentTag = $this->_viewContent->createElement( static::$_viewContentElementContainer ); 
			$contentTag->appendChild( $contentData );
			$this->_viewContent->documentElement->appendChild( $contentTag );
		}
		else
		{
			$this->_viewContent->appendChild( $contentData );
		}
	//	$this->_viewContent->view(); exit();
	//	var_export( $content );
		
	}

    /**
	 * Gets the _viewContent
	 *
     */
    public function getViewContent()
	{
		if( null === $this->_viewContent ){ return; } 	//	don't return empty tags
		return $this->_viewContent->saveHTML();
	}

    /**
     * Returns the markup sent by template for the view method
     * 
     * @param void
     * @return string Mark-Up for the view template
     */
    public function getMarkupTemplate( array $options = null )
	{
		/* ALLOWING TEMPLATES TO INJECT MARKUP INTO VIEWABLE OBJECTS */
	//	self::v( get_class( $this ) );
		
		if( ! is_null( $this->_markupTemplate ) && ! $options['refresh'] )
		{
		//	self::v( $this->_markupTemplate );
			return $this->_markupTemplate;
		}
/* 	//	if( ! $this->getParameter( 'markup_template' ) )
		{
			//	Turn me to false so we dont have to come here again for the same request.
			$this->_markupTemplate = false;
			return $this->_markupTemplate;
		}
 */		$storageNamespace = 'markup_template_c' . $this->getParameter( 'markup_template_namespace' ) . '_' . Ayoola_Application::getUserInfo( 'access_level' );
		$markup = $this->getParameter( 'markup_template_prefix' );
		$markup .= $this->getParameter( 'markup_template' );
		$markup .= $this->getParameter( 'markup_template_suffix' );
		
		//	Site Wide Storage of this value
		$storage = $this->getObjectStorage( array( 'id' => $storageNamespace, 'device' => 'File', 'time_out' => 100, ) );
	//	$this->_markupTemplate = $this->_markupTemplate ? : $this->getObjectStorage( $storageNamespace )->retrieve();
	//	$this->_markupTemplate =  $this->getParameter( 'markup_template' ) ? $markup : $storage->retrieve();
		if( $this->getParameter( 'markup_template' ) )
		{
			$this->_markupTemplate = $markup;
		//	self::v( $this->getParameter( 'markup_template' ) );

			// prevent multiple disk writes
		//	$storage->store( $this->_markupTemplate );  
		//	$storage->retrieve() != $this->_markupTemplate && ! $this->getParameter( 'markup_template_no_cache' ) ? $storage->store( $this->_markupTemplate ) : null;
			$storage->retrieve() != $this->_markupTemplate && $this->getParameter( 'markup_template_cache' ) ? $storage->store( $this->_markupTemplate ) : null;
		}
		elseif( $storage->retrieve() && ( $this->getParameter( 'markup_template_namespace' ) || Ayoola_Application::getRuntimeSettings( 'real_url' ) == '/tools/classplayer' ) ) 
		{
			$this->_markupTemplate =  $storage->retrieve();
			null;
		}
		else
		{
			//	Turn me to false so we dont have to come here again for the same request.
			$this->_markupTemplate = false;
		}
	//	$storage->clear(  );  
		return $this->_markupTemplate;
	}
	
    /**
     * @param void
     * @return array
     */
    public function getObjectTemplateValues()
	{
		return $this->_objectTemplateValues ? : array();
	}
	
    /**
     * Returns html content that is useful for display. 
     * Depends on the situation and environment, it will return different content
     * @param void
     * @return string Mark-Up for the view template
     */
    public function view()
	{

		$this->_playMode = $this->getParameter( 'play_mode' ) ? : $this->_playMode;
		if( isset( $_SERVER['HTTP_AYOOLA_PLAY_MODE'], $_REQUEST['object_name'] ) && $_REQUEST['object_name'] == get_class( $this ) )
		{
			$this->_playMode = $_SERVER['HTTP_AYOOLA_PLAY_MODE'] ? : $this->_playMode;
		}
	//	var_export( $_POST );
/* 		if( @$_POST['a'] == 'test' )
		{
		//	var_export( $_SERVER );
		}
 */	
		switch( $this->_playMode )
		{
			case static::PLAY_MODE_MUTE:
				exit();
			break;
			case static::PLAY_MODE_JSON:
				error_reporting( E_ALL & ~E_STRICT & ~E_NOTICE & ~E_USER_NOTICE );
				ini_set( 'display_errors', "0" ); 
			//	var_export( $this->_objectData );
				header( 'Content-Type: application/json; charset=utf-8' );
				if( @$_POST['PAGECARTON_RESPONSE_WHITELIST'] )
				{
				
					//	Limit the values that is being sent
					$whitelist = @$_POST['PAGECARTON_RESPONSE_WHITELIST'];
					$whitelist = is_array( $whitelist ) ? $whitelist : array_map( 'trim', explode( ',', $whitelist ) );
					$whitelist = array_combine( $whitelist, $whitelist );
					$this->_objectData = array_intersect_key( $this->_objectData, $whitelist );
				}
				$dataToSend = json_encode( $this->_objectData );
				echo $dataToSend;
				
				//	Log early before we exit
				Ayoola_Application::log();
			//	if( ! self::hasPriviledge() )
				{
					exit();
				}
			break;
			case static::PLAY_MODE_JSONP:
				error_reporting( E_ALL & ~E_STRICT & ~E_NOTICE & ~E_USER_NOTICE );
				ini_set( 'display_errors', "0" ); 
			
				header( 'Content-Type: application/javascript;' );
				if( @$_POST['PAGECARTON_RESPONSE_WHITELIST'] )
				{
				
					//	Limit the values that is being sent
					$whitelist = @$_POST['PAGECARTON_RESPONSE_WHITELIST'];
					$whitelist = is_array( $whitelist ) ? $whitelist : array_map( 'trim', explode( ',', $whitelist ) );
					$whitelist = array_combine( $whitelist, $whitelist );
					$this->_objectData = array_intersect_key( $this->_objectData, $whitelist );
				}
				$dataToSend = json_encode( $this->_objectData );
				echo $dataToSend;
				//	Log early before we exit
				Ayoola_Application::log();
			//	if( ! self::hasPriviledge() )
				{
					exit();
				}
			break;
			case 'ENCRYPTION':
			
				header( "Content-Disposition: attachment;filename=encryption" );
				header( 'Content-Type: application/octet-stream' );
				//	Introduce timeout to prevent a replay attack.
			//	if( isset( $_POST['pagecarton_request_timezone'], $_POST['pagecarton_request_time'], $_POST['pagecarton_request_timeout'] ) )
				{
					$this->_objectData['pagecarton_response_timezone'] = date_default_timezone_get();
					$this->_objectData['pagecarton_response_time'] = time();
					$this->_objectData['pagecarton_response_timeout'] = 50;
				}
				if( @$_POST['pagecarton_response_whitelist'] ) 
				{
				
					//	Limit the values that is being sent
					$whitelist = @$_POST['pagecarton_response_whitelist'];
					$whitelist = is_array( $whitelist ) ? $whitelist : array_map( 'trim', explode( ',', $whitelist ) );
					$whitelist = array_combine( $whitelist, $whitelist );
					$this->_objectData = array_intersect_key( $this->_objectData, $whitelist );
				}
				
				$dataToSend = json_encode( $this->_objectData );
			//	var_export( $_SERVER['HTTP_PAGECARTON_RESPONSE_ENCRYPTION'] );
				$encrypted = OpenSSL::encrypt( $dataToSend, $_SERVER['HTTP_PAGECARTON_RESPONSE_ENCRYPTION'] );
			//	var_export( $encrypted );
			//	echo $dataToSend;
			//	echo base64_encode( $encrypted );
				echo $encrypted;
				//	Log early before we exit
				Ayoola_Application::log();
			//	if( ! self::hasPriviledge() )
				{
					exit();
				}
			break;
			case static::PLAY_MODE_PHP:
				$dataToSend = serialize( $this->_objectData );
				echo $dataToSend;
				//	Log early before we exit
				Ayoola_Application::log();
			//	if( ! self::hasPriviledge() )
				{
					exit();
				}
			break;
			case static::PLAY_MODE_HTML:
				$content = null;
				$content = $this->getViewContent();
				
				if( ! $template = $this->getMarkupTemplate() )      
				{
					return $content;
				}
		//		var_export( $template );
				if( $this->_form )
				{
					Application_Javascript::addCode
					(
						'
							ayoola.events.add
							(
								window,
								"load",
								function()
								{
									
									ayoola.xmlHttp.setAfterStateChangeCallback
									( 
										function()
										{ 
											var a = document.getElementById( "' . $this->getObjectName() . '_form_goodnews" );
											if( a )
											{
												//	workaround for a bug that makes content for the goodnews show the whole view content
												a.id = "";
												ayoola.spotLight.popUp( a.innerHTML ); 
											}
											
										} 
									);
								}
							);
						'
					);
					//	Lets insert form requirements in the artificial form fields
					$this->_objectTemplateValues = array_merge( $_REQUEST, $this->_objectTemplateValues );
				//	var_export( $this->getParameter() );
					$this->_objectTemplateValues['template_object_name'] = $this->getObjectName();
					
					//	internally count the instance
					$this->_objectTemplateValues['template_instance_count'] = static::$_counter;
					$this->_objectTemplateValues['template_form_requirements'] = $this->getForm()->getRequiredFieldset()->view();
					$this->_objectTemplateValues['template_form_badnews'] = null;
					$this->_objectTemplateValues['template_form_goodnews'] = null;
					if( $this->getForm()->getBadnews() )
					{
						$this->_objectTemplateValues['template_form_badnews'] .= '<ul>';
						foreach( $this->getForm()->getBadnews() as $message ) 
						{
							$this->_objectTemplateValues['template_form_badnews'] .= "<li class='badnews'>$message</li>\n";
						}
						$this->_objectTemplateValues['template_form_badnews'] .= '</ul>';  
					}
					elseif( $this->getForm()->getValues() ) 
					{
						//	used to disable forms for avoid multiple submissions after form completion
						$this->_objectTemplateValues['template_form_disable'] = 'disabled="disabled"';
						
						$this->_objectTemplateValues['template_form_goodnews'] = '<span id="' . $this->getObjectName() . '_form_goodnews"><span class="goodnews boxednews fullnews centerednews">' . $content . '</span></span>';

		/* 				$content = $this->getViewContent();
						return $content;
		 */			}
				}
		//		self::v( $template );
				//	Add the Ayoola_Application Global
				$this->_objectTemplateValues = array_merge( Ayoola_Application::$GLOBAL ? : array(), $this->_objectTemplateValues );
	
				//	allows me to add pagination on post listing with predefined suffix
				$template = $this->getParameter( 'markup_template_prepend' ) . $template;
				$template = $template . $this->getParameter( 'markup_template_append' );
		//		self::v( $template );  
		// 		self::v( $this->_objectTemplateValues );  
				$template = Ayoola_Abstract_Playable::replacePlaceholders( $template, $this->_objectTemplateValues + array( 'placeholder_prefix' => '{{{', 'placeholder_suffix' => '}}}', ) );
	//			self::v( $this->_objectTemplateValues );   
				
				return $template;
			break;
			default:
				
			break;
		}
	}
	// END OF CLASS
}
