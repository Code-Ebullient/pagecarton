<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Extension_Import_Status
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Status.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Ayoola_Extension_Import_Abstract
 */
 
require_once 'Application/Subscription/Abstract.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Extension_Import_Status
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Extension_Import_Status extends Ayoola_Extension_Import_Abstract
{	
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()  
    {
		try{ $this->setIdentifier(); }
		catch( Ayoola_Extension_Import_Exception $e ){ return false; }
		if( ! $data = self::getIdentifierData() ){ return false; }
		
//		var_export( $data );
		$currentStatus = true;
		if( $this->getParameter( 'switch' ) === 'off' )
		{
			//	Try to switch this off whether its previously on/off
			$data['status'] = '1';
		}
		switch( strtolower( strval( $data['status'] ) ) )
		{
			case 'enabled':
			case '1':
				// we currently are on
				$currentStatus = true;
				
				//	Switch off				
				$data['status'] = 'Disabled';
				$this->createConfirmationForm( 'Disable Extension...', 'Disable "' . $data['extension_title'] . '"', $data );
			break;
			default:
				// we currently are off
				$currentStatus = false;
				
				//	Switch on
				$this->createConfirmationForm( 'Enable Extension...', 'Enable "' . $data['extension_title'] . '"', $data );
				$data['status'] = 'Enabled';
			break;
		}
		$this->setViewContent( $this->getForm()->view(), true );
		if( ! $values = $this->getForm()->getValues() )
		{ 
			if( $this->getParameter( 'switch' ) !== 'off' )
			{
				return false; 
			}
		}
		$this->setViewContent( '<p class="boxednews normalnews"></p>', true );
		$fromDir = ( @constant( 'EXTENSIONS_PATH' ) ? Ayoola_Application::getDomainSettings( EXTENSIONS_PATH ) : ( APPLICATION_DIR . DS . 'extensions' ) ) . DS . $data['extension_name'] . DS . 'application';
		$toDir = Ayoola_Application::getDomainSettings( APPLICATION_PATH );
		if( @$data['modules'] )
		{
			$directory =   '/modules';
			foreach( $data['modules'] as $key => $each )
			{						
				$from = $fromDir . $directory . $each;
				$to = $toDir . $directory . $each;
				self::changeStatus( $currentStatus, $from , $to );
			}
		}
		if( @$data['databases'] )
		{
			$directory =  '/databases';
			foreach( $data['databases'] as $each )
			{
				$from = $fromDir . $directory . $each;
				$to = $toDir . $directory . $each;
				$to = dirname( $to ) . '/__/' . array_shift( explode( '.', basename( $each ) ) ) . '/extensions/' . $data['extension_name'] . '.xml';
				self::changeStatus( $currentStatus, $from , $to );
			}
		}
//		var_export( $data['documents'] );
		if( @$data['documents'] )
		{
			$directory =  '/documents';
			foreach( $data['documents'] as $each )
			{
				$from = $fromDir . $directory . $each;
				$to = $toDir . $directory . $each;
				self::changeStatus( $currentStatus, $from , $to );
			}
		}
		if( @$data['plugins'] )
		{
			$directory =  '/plugins/';
			foreach( $data['plugins'] as $each )
			{
				$from = $fromDir . $directory . $each;
				$to = $toDir . $directory . $each;
				self::changeStatus( $currentStatus, $from , $to );
			}
		}
		if( @$data['pages'] )
		{
			$directory =  '/';
			foreach( $data['pages'] as $uri )
			{
				if( $pagePaths = Ayoola_Page::getPagePaths( $uri ) )
				{
					foreach( $pagePaths as $each )
					{
						$from = $fromDir . $directory . $each;
						$to = $toDir . $directory . $each;
						self::changeStatus( $currentStatus, $from , $to );
					}
				}
			}
		}
		if( @$data['templates'] )
		{
			$directory =  '/documents/layout/';
			foreach( $data['templates'] as $each )
			{
				$from = $fromDir . $directory . $each;
				$to = $toDir . $directory . $each;
				self::changeStatus( $currentStatus, $from , $to );
			}
		}
		unset( $data['extension_name'] );
		unset( $data['extension_id'] );
 		if( ! $this->updateDb( $data ) )
		{ 
			$this->setViewContent( '<p class="badnews">Error: could not save extension.</p>.' ); 
			return false;
		}
		$this->setViewContent( '<p class="boxednews normalnews">Extension status "' . $data['status'] . '" successfully.</p>' );
	//	$this->setViewContent( $this->getForm()->view() );
  
	} 
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function changeStatus( $currentStatus, $from, $to )  
    {
//		echo '' . $from . '<br>';
	//	echo  '' . $to . '<br>';
		$file = str_ireplace( Ayoola_Application::getDomainSettings( APPLICATION_PATH ), '', $to );
		switch( $currentStatus )
		{
			case true:
				if( ! is_link( $to ) )
				{
					$this->setViewParameter( '' . 'ERROR 1: "' . $file . '" not enabled before.' );
					continue;
				}				
				elseif( $from !== readlink( $to ) )
				{
					$this->setViewParameter( '' . 'ERROR 2: "' . $file . '" is in use by another extension.' );
					continue;
				}				
				unlink( $to );
				Ayoola_Doc::removeDirectory( basename( $to ) );
			break;
			case false:
				if( ! file_exists( $from ) )
				{
					$this->setViewParameter( '' . 'ERROR 3: "' . $file . '" not found in extension files.' );
					continue;
				}					
				elseif( file_exists( $to ) )
				{
					$this->setViewParameter( '' . 'ERROR 4: "' . $file . '" has a conflicting file on the server.' );
					continue;
				}					
				//	create this dir if it isnt there before
				Ayoola_Doc::createDirectory( dirname( $to ) );
				symlink( $from , $to );
			break;
		}
	}
	// END OF CLASS
}
