<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-11-7
 * Time: 下午5:00
 */

namespace Jenner\Zebra\Crontab;


/**
 *  @author:  Jan Konieczny <jkonieczny@gmail.com>
 *  @copyright: Copyright (C) 2009, Jan Konieczny
 *
 *  This is a simple script to parse crontab syntax to get the execution time
 *
 *  Eg.:   $timestamp = Crontab::parse('12 * * * 1-5');
 *
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Provides basic cron syntax parsing functionality
 *
 * @author:  Jan Konieczny <jkonieczny@gmail.com>
 * @copyright: Copyright (C) 2009, Jan Konieczny
 */
class CrontabParse {
    /**
     *  Finds next execution time(stamp) parsin crontab syntax,
     *  after given starting timestamp (or current time if ommited)
     *
     *  @param string $_cron_string:
     *
     *      0     1    2    3    4
     *      *     *    *    *    *
     *      -     -    -    -    -
     *      |     |    |    |    |
     *      |     |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *      |     |    |    +------- month (1 - 12)
     *      |     |    +--------- day of month (1 - 31)
     *      |     +----------- hour (0 - 23)
     *      +------------- min (0 - 59)
     *  @param int $_after_timestamp timestamp [default=current timestamp]
     *  @return int unix timestamp - next execution time will be greater
     *              than given timestamp (defaults to the current timestamp)
     *  @throws InvalidArgumentException
     */
    public static function parse($_cron_string,$_after_timestamp=null)
    {
        if(!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',trim($_cron_string))){
            throw new \InvalidArgumentException("Invalid cron string: ".$_cron_string);
        }
        if($_after_timestamp && !is_numeric($_after_timestamp)){
            throw new \InvalidArgumentException("\$_after_timestamp must be a valid unix timestamp ($_after_timestamp given)");
        }
        $cron   = preg_split("/[\s]+/i",trim($_cron_string));
        $start  = empty($_after_timestamp)?time():$_after_timestamp;
        $date   = array(    'minutes'   =>self::_parseCronNumbers($cron[0],0,59),
            'hours'     =>self::_parseCronNumbers($cron[1],0,23),
            'dom'       =>self::_parseCronNumbers($cron[2],1,31),
            'month'     =>self::_parseCronNumbers($cron[3],1,12),
            'dow'       =>self::_parseCronNumbers($cron[4],0,6),
        );
        // limited to time()+366 - no need to check more than 1year ahead
        for($i=0;$i<=60*60*24*366;$i+=60){
            if( in_array(intval(date('j',$start+$i)),$date['dom']) &&
                in_array(intval(date('n',$start+$i)),$date['month']) &&
                in_array(intval(date('w',$start+$i)),$date['dow']) &&
                in_array(intval(date('G',$start+$i)),$date['hours']) &&
                in_array(intval(date('i',$start+$i)),$date['minutes'])
            ){
                return $start+$i;
            }
        }
        return null;
    }
    /**
     * get a single cron style notation and parse it into numeric value
     *
     * @param string $s cron string element
     * @param int $min minimum possible value
     * @param int $max maximum possible value
     * @return int parsed number
     */
    protected static function _parseCronNumbers($s,$min,$max)
    {
        $result = array();
        $v = explode(',',$s);
        foreach($v as $vv){
            $vvv  = explode('/',$vv);
            $step = empty($vvv[1])?1:$vvv[1];
            $vvvv = explode('-',$vvv[0]);
            $_min = count($vvvv)==2?$vvvv[0]:($vvv[0]=='*'?$min:$vvv[0]);
            $_max = count($vvvv)==2?$vvvv[1]:($vvv[0]=='*'?$max:$vvv[0]);
            for($i=$_min;$i<=$_max;$i+=$step){
                $result[$i]=intval($i);
            }
        }
        ksort($result);
        return $result;
    }
}