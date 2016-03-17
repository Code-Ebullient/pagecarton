<?php
/**
 * AyStyle Developer Tool
 *
 * LICENSE
 *
 * @category   Ayoola
 * @package    Application_Log_View_Access
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 * @version    $Id: Access.php 10.3.2012 7.55am ayoola $
 */

/**
 * @see Application_Log_Abstract
 */
 
//require_once 'Application/Log/Abstract.php';


/**
 * @category   Ayoola
 * @package    Application_Log_View_Access
 * @copyright  Copyright (c) 2011-2010 Ayoola Online Inc. (http://www.www.pagecarton.com)
 * @license    http://developer.www.pagecarton.com/aystyle/license/
 */

class Application_Log_View_Access extends Application_Log_View_Abstract
{
	
    /**
     * Table where log goes to
     * 
     * @var string
     */
	protected static $_logTable = 'Application_Log_View_Access_Log';
		
		
    /**
     * Creates a log
     * 
     */
	public static function log()
	{
		
	//	var_export( self::getLogTable() );
		foreach( self::getLogTable()->getDataTypes() as $key => $value )
		{
			$log[$key] = @$_SERVER[strtoupper( $key )];
		}
	//	$log['total_run_time'] = microtime( true ) - $log['request_time'];
		$log['total_run_time'] = Ayoola_Application::getRuntimeSettings( 'total_runtime' );
	//	var_export( $log['total_run_time'] );  
	//	var_export( $log ); 
		$log['total_run_time'] = number_format( is_numeric( $log['total_run_time'] ) ? : 0, 2, '.', '' );
	//	var_export( $log['total_run_time'] );
		$log['ip'] = implode( ':', Ayoola_Application::getRuntimeSettings( 'user_ip' ) );
	//	$log['session_id'] = sha1( session_id() );
	//	$log['post'] = $_POST;
	//	$log['get'] = $_GET; 
	//	$log['request'] = $_REQUEST; 
	//	$log['request'] = $_GET + $_POST;  
		$log['request'] = $_POST; 
		
		//	NUMBER OF PAGES VIEWED IN THIS SESSION
		@$log['NPS'] = ++$_SESSION['NPS']; 
		$log['log_time'] = time();  
		
		unset( $log['request']['password'], $log['request']['password2'], $log['request'][Ayoola_Form::hashElementName( 'password' )], $log['request'][Ayoola_Form::hashElementName( 'password2' )], $log['request']['local_password'], $log['request'][Ayoola_Form::hashElementName( 'local_password' )] );
		$log['uri'] = Ayoola_Application::getPresentUri(); 
		switch( $log['uri'] )
		{
			case '/tools/classplayer':
				$log['uri'] = $_GET['object_name'];
			break;
			case '/object':
				$log['uri'] = $_GET['name'];
			break;
		}
		$access = new Ayoola_Access();
		$userInfo = $access->getUserInfo();
	//	var_export( $userInfo ); 
//		if( Ayoola_Application::getUserInfo( 'access_level' ) == 99 )
		{
		//	var_export( $userInfo ); 
		}
		$log['user_id'] = $userInfo['user_id'] ? : substr( md5( sha1( session_id() ) ), 0, 5 ); 
		try
		{
			self::getLogTable()->insert( $log );
		}
		catch( Ayoola_Dbase_Adapter_Exception $e ){ null; } // Encountered error when trying to log the process of clearing access log
    }
	// END OF CLASS
}
