<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Application_Slideshow
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Slideshow.php 4.17.2012 11.53 ayoola $
 */

/**
 * @see Ayoola_Dbase_Table_Abstract_Xml
 */
 
require_once 'Ayoola/Dbase/Table/Abstract/Xml.php';


/**
 * @category   PageCarton CMS
 * @package    Application_Slideshow
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Slideshow extends Ayoola_Dbase_Table_Abstract_Xml
{

    /**
     * The Version of the present table (SVN COMPATIBLE)
     *
     * @param int
     */
    protected $_tableVersion = '0.09'; 
 
	protected $_dataTypes = array
	( 
		'slideshow_name' => 'INPUTTEXT, UNIQUE',
		'slideshow_type' => 'INPUTTEXT',
		'category_name' => 'INPUTTEXT',
		'slideshow_title' => 'INPUTTEXT',
		'slideshow_description' => 'TEXTAREA',
		'image_limit' => 'INT',
		'sample_image' => 'INPUTTEXT',
		'width' => 'INPUTTEXT',
		'height' => 'INPUTTEXT',
		'timeout' => 'INPUTTEXT',
		'slideshow_images' => 'INPUTTEXT',
		'slideshow_data' => 'ARRAY',
		'slideshow_options' => 'ARRAY',
		'slideshow_image' => 'JSON',
		'image_link' => 'JSON',
		'image_title' => 'JSON',
		'image_description' => 'JSON',
	);
	// END OF CLASS
}
