<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Filter_Time
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Time.php 10.13.2011 1:55PM ayoola $
 */

/**
 * @see Ayoola_
 */
 
require_once 'Ayoola/Filter/Interface.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Filter_Time
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Filter_Time implements Ayoola_Filter_Interface
{
    /**
     * The time setting
     *
     * @var string
     */
	protected $_timeSettings = 'relative';
	
    /**
     * The time setting
     *
     * @var int
     */
	public $precision = 1;
	
    /**
     * Prefix to use for the filtered value
     *
     * @var string
     */
	public $prefix = 'ago';
	
	
    /**
     *
     * @param string
     * @return 
     */
    public function __construct( $timeSettings = null )
    {
        return $this->setTimeSettings( $timeSettings );
    } 

    /**
     * This method does the main filtering biz.
     *
     * @param int UNIX time()
     * @return string 
     */
    public function filter( $value )
    {
	//	var_export( $value );
		$timeDifference = time() - $value;
		$time = self::splitSeconds( $timeDifference, $this->precision );
		return $time . ' ' . $this->prefix;
    } 

    /**
     * "Splits" seconds into its respective seconds, minutes, hours, days, weeks, months, years
     *
     * @param int Seconds to evaluate
     * @return array
     */
    public static function splitSeconds( $seconds, $precision = 1 )
    {
	//	var_export( $value );
	//	var_export( $timeDifference );
   //	switch( $this->getTimeSettings() )
		{
	//		case null:
	//		default:
			$timeSegments = array( 'secs', 'mins', 'hrs', 'days', 'wks', 'months', 'yrs' );
			krsort( $timeSegments );
			$time = null;
			$counter = 0;
			foreach( $timeSegments as $each )
			{
				list( $noOfSegment, $remSec ) = self::getTimeSegment( $seconds, $each );
				$seconds = $remSec;
				if( $remSec === 0 ){ break; }
				if( $noOfSegment == 0 ){ continue; }
				if( $noOfSegment == 1 ){ $each = rtrim( $each, 's' ); }
				
				$time .= $noOfSegment . ' ' . $each . ' ';
				$counter++;
				if( $counter >= $precision ){ break; }
			}
		}
		return trim( $time );
    } 

    /**
     * Sets the _timeSettings to a value
     *
     * @param string
     * @return mixed
     */
    public function setTimeSettings( $Time = null )
    {
		return $this->_timeSettings = $Time;
    } 
	
    /**
     * Retrieves the _timeSettings
     *
     * @param void
     * @return string
     */
    public function getTimeSettings( )
    {
        return $this->_timeSettings;
		
    } 
	
    /**
     * Calculate the minutes Ago
     *
     * @param void
     * @return string
     */
    public static function getTimeSegment( $secondsAgo, $mode )
    {
        switch( $mode )
		{
			case 'secs':
			$seconds = 1;
			break;
			
			case 'mins':
			$seconds = 60;
			break;
			
			case 'hrs':
			$seconds = 3600;
			break;
			
			case 'days':
			$seconds = 86400;
			break;
			
			case 'wks':
			$seconds = 604800;
			break;
			
			case 'months':
			$seconds = 2419200;
			break;
			
			case 'yrs':
			$seconds = 29030400;
			break;
			
			default:
			$seconds = 1;
			break;
		}
		return array( floor( $secondsAgo / $seconds ), fmod( $secondsAgo, $seconds ) );
    } 
	
	
    /**
     * This method
     *
     * @param 
     * @return 
     */
    public function autofil( $parameter )
    {
		if( ! empty( $parameter[0] ) )
        return setTimeSettings( $parameter[0] );
		
    } 
	
	
	// END OF CLASS
}
