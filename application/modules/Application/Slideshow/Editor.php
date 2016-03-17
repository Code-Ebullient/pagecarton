<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @Slideshow   Ayoola
 * @package    Application_Slideshow_Editor
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Editor.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Application_Slideshow_Abstract
 */
 
require_once 'Application/Slideshow/Abstract.php';


/**
 * @Slideshow   Ayoola
 * @package    Application_Slideshow_Editor
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Slideshow_Editor extends Application_Slideshow_Abstract
{
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		try
		{ 
			if( ! $data = self::getIdentifierData() ){ return false; }
			$this->createForm( 'Edit', 'Editing ' . $data['slideshow_name'], $data );
			$this->setViewContent( $this->getForm()->view(), true );
			
			if( ! $values = $this->getForm()->getValues() ){ return false; }
			if( ! $this->updateDb() )
			{ 
				return false;
			}
			$this->setViewContent( '<div class="boxednews goodnews" style="clear:both;">Slideshow settings saved successfully. </div>', true ); 
		//	$values['slideshow_type'] = $values['slideshow_type'] ? : 'upload';
			switch( $values['slideshow_type'] )
			{
				case 'post':
					$this->setViewContent( '<a href="/post/create?article_type=photo&category=' .  $values['category_name'] . '" class="boxednews  pc-bg-color">Add new post</a>' ); 
				break;
			//	case 'upload':
				default:
					$this->setViewContent( '<a href="/tools/classplayer/get/object_name/Application_Slideshow_Manage/?slideshow_name=' .  ( @$values['slideshow_name'] ? : $data['slideshow_name'] ) . '" class="boxednews pc-bg-color">Add photos</a>' ); 
				break;
			}
		}
		catch( Application_Slideshow_Exception $e ){ return false; }
    } 
	// END OF CLASS
}
