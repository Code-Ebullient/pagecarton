<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Application_Category_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Application_Category_Exception 
 */
 
require_once 'Application/Category/Exception.php';


/**
 * @category   PageCarton CMS
 * @package    Application_Category_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Application_Category_Abstract extends Ayoola_Abstract_Table
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
	protected $_identifierKeys = array( 'category_name',  );
	
    /**
     * 
     * @var string
     */
	protected $_idColumn = 'category_name';  
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Application_Category';
	
	
    /**
     * creates the form
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue, $legend = null, Array $values = null ) 
    {
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$fieldset = new Ayoola_Form_Element;
		$form->submitValue = $submitValue ;
		$form->oneFieldSetAtATime = true; 
		$fieldset->addElement( array( 'name' => 'category_label', 'placeholder' => 'Give this category a name', 'type' => 'InputText', 'value' => @$values['category_label'] ) );
/* 		if( ! $values && @$_REQUEST['category_name'] )
		{
			$fieldset->addElement( array( 'name' => 'category_name', 'placeholder' => 'Give this category a name', 'type' => 'InputText', 'value' => @$values['category_name'] ? : $_REQUEST['category_name'] ) );
			$form->requiredElements = $form->requiredElements + array( 'category_name' => $_REQUEST['category_name'] );
		}
 */		$articleSettings = Application_Article_Settings::getSettings( 'Articles' );
		
//var_export( $articleSettings )
		
		//	Cover photo
/* 		$link = '/ayoola/thirdparty/Filemanager/index.php?field_name=' . ( $fieldset->hashElementName ? Ayoola_Form::hashElementName( 'cover_photo' ) : 'cover_photo' );
	//	var_export( $link );
		$fieldset->addElement( array( 'name' => 'cover_photo', 'label' => '<input type=\'button\' value=\'Select Photo\' />', 'placeholder' => 'Cover Photo for this category', 'onClick' => 'ayoola.spotLight.showLinkInIFrame( \'' . $link . '\' );', 'type' => 'InputText', 'value' => @$values['cover_photo'] ) );
 */		
	//	$link = '/ayoola/thirdparty/Filemanager/index.php?field_name=' . ( $fieldset->hashElementName ? Ayoola_Form::hashElementName( 'cover_photo' ) : 'cover_photo' );
		$fieldName = ( $fieldset->hashElementName ? Ayoola_Form::hashElementName( 'cover_photo' ) : 'cover_photo' );
	//	var_export( $link );
		$fieldset->addElement( array( 'name' => 'cover_photo', 'label' => '', 'placeholder' => 'Cover Photo for this category', 'type' => 'Hidden', 'value' => @$values['cover_photo'] ) );
		$width = @$articleSettings['category_photo_width'] ? : '900';
		$height = @$articleSettings['category_photo_height'] ? : '300';
		$fieldset->addElement( array( 'name' => 'x', 'type' => 'Html' ), array( 'html' => Ayoola_Doc_Upload_Link::viewInLine( array( 'image_preview' => ( @$values['cover_photo'] ? : null ), 'field_name' => $fieldName, 'preview_text' => $width . 'x' . $height, 'width' => $width, 'height' => $height, 'crop' => true, 'field_name_value' => 'url' ) ) ) );
	//	$fieldset->addElement( array( 'name' => 'cover_photo_base64', 'label' => 'Cover Picture', 'data-allow_base64' => true, 'type' => 'Document', 'value' => @$values['document_url_base64'] ) );
		

		$fieldset->addElement( array( 'name' => 'category_description', 'placeholder' => 'Describe this category in a few words...', 'type' => 'TextArea', 'value' => @$values['category_description'] ) );
		
		$options =  array( 
							'parent' => 'Make this category a sub-category of existing categories',
							'child' => 'Add some categories as a child of this category',
							'link' => 'Manually enter a URL that this category will link to',  
							);
		$fieldset->addElement( array( 'name' => 'category_options', 'type' => 'Checkbox', 'value' => @$values['category_options'] ), $options );
		
		$fieldset->addFilters( array( 'trim' => null ) );
		$fieldset->addRequirement( 'category_label', array( 'WordCount' => array( 2, 100 ) ) );
		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );
		
		$addCategoryLink = self::hasPriviledge() ? ( '<a class="goodnews boxednews" rel="spotlight;" title="Add new Category" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Category_List/">+</a>' ) : null; 
/* 		if( ( is_array( Ayoola_Form::getGlobalValue( 'category_options' ) ) && in_array( 'parent', Ayoola_Form::getGlobalValue( 'category_options' ) ) ) || @$values['parent_category_name'] ) 
		{
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addLegend( 'Make this category a sub-category of an existing category. ' . $addCategoryLink );
			$options = new Application_Category;
			$options = $options->select();
			require_once 'Ayoola/Filter/SelectListArray.php';
			$filter = new Ayoola_Filter_SelectListArray( 'category_name', 'category_label');
			$options = array( '' => 'None' ) + $filter->filter( $options );
			$fieldset->addElement( array( 'name' => 'parent_category_name', 'label' => 'Parent Category', 'type' => 'Select', 'value' => @$values['parent_category_name'] ), $options );
			$fieldset->addRequirement( 'parent_category_name', array( 'InArray' => array_keys( $options )  ) ); 
			$form->addFieldset( $fieldset );
		}
 */		if( ( is_array( Ayoola_Form::getGlobalValue( 'category_options' ) ) && in_array( 'parent', Ayoola_Form::getGlobalValue( 'category_options' ) ) ) )
		{
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addLegend( 'Add some categories as a parent of this category. ' . $addCategoryLink );
			$options = new Application_Category;
			$options = $options->select();
			require_once 'Ayoola/Filter/SelectListArray.php';
			$filter = new Ayoola_Filter_SelectListArray( 'category_name', 'category_label');
			$options = $filter->filter( $options );
			$fieldset->addElement( array( 'name' => 'parent_category', 'label' => 'Select Parent Categories', 'type' => 'Checkbox', 'value' => @$values['parent_category'] ), $options );
			$fieldset->addRequirement( 'parent_category', array( 'InArray' => array_keys( $options )  ) ); 
			$form->addFieldset( $fieldset );
		}
		if( ( is_array( Ayoola_Form::getGlobalValue( 'category_options' ) ) && in_array( 'child', Ayoola_Form::getGlobalValue( 'category_options' ) ) ) || @$values['child_category_name'] )
		{
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addLegend( 'Add some categories as a child of this category. ' . $addCategoryLink );
			$options = new Application_Category;
			$options = $options->select();
			require_once 'Ayoola/Filter/SelectListArray.php';
			$filter = new Ayoola_Filter_SelectListArray( 'category_name', 'category_label');
			$options = $filter->filter( $options );
	//		$fieldset->addElement( array( 'name' => 'child_category_name', 'label' => 'Child Category', 'type' => 'SelectMultiple', 'value' => @$values['child_category_name'] ), $options );
			$fieldset->addElement( array( 'name' => 'child_category_name', 'label' => 'Select Child Categories', 'type' => 'Checkbox', 'value' => @$values['child_category_name'] ), $options );
			$fieldset->addRequirement( 'child_category_name', array( 'InArray' => array_keys( $options )  ) ); 
			$form->addFieldset( $fieldset );
		}

		if( is_array( Ayoola_Form::getGlobalValue( 'category_options' ) ) && in_array( 'link', Ayoola_Form::getGlobalValue( 'category_options' ) ) )
		{
			$fieldset = new Ayoola_Form_Element;
			$fieldset->addLegend( 'Manually enter a URL that this category will link to' );
			$fieldset->addElement( array( 'name' => 'category_url', 'placeholder' => 'e.g. http://' . Ayoola_Page::getDefaultDomain() . '/page/', 'type' => 'InputText', 'value' => @$values['category_url'] ) );
			$form->addFieldset( $fieldset );
		}

		
		$this->setForm( $form );
    } 
	// END OF CLASS
}
