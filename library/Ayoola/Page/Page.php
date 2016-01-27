<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Page_Page
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Page.php date time ayoola $
 */

/**
 * @see Ayoola_Dbase_Table_Abstract_Xml
 */
 
require_once 'Ayoola/Dbase/Table/Abstract/Xml.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Page_Page
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Page_Page extends Ayoola_Dbase_Table_Abstract_Xml
{

    /**
     * The Version of the present table (SVN COMPATIBLE)
     *
     * @param int
     */
    protected $_tableVersion = '0.05';  

	
	protected $_dataTypes = array
	( 
	//	'name' => 'INPUTTEXT,UNIQUE',
		'url' => 'INPUTTEXT,UNIQUE', 
		'redirect_url' => 'INPUTTEXT', 
		'title' => 'INPUTTEXT',
		'description' => 'TEXTAREA', 'keywords' => 'TEXTAREA',
		'layout_name' => 'INPUTTEXT, FOREIGN_KEYS = Ayoola_Page_PageLayout',
		'auth_level' => 'ARRAY', 
		'enabled' => 'INT',
		'page_options' => 'ARRAY',
		'creation_date' => 'INT', 'modified_date' => 'INT',
	);
	// END OF CLASS
}
