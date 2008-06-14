<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Db_Profiler */
require_once 'Zend/Db/Profiler.php';

/** Zend_Debug */
require_once 'Zend/Debug.php';

/** Zend_Debug_FirePhp_Plugin_Interface */
require_once 'Zend/Debug/FirePhp/Plugin/Interface.php';

/**
 * Writes DB events as log messages to the Firebug Console via FirePHP.
 * 
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FirePhp_Db_Profiler_FirePhp extends Zend_Db_Profiler implements Zend_Debug_FirePhp_Plugin_Interface
{
  
    /**
     * Constructor
     *
     * Add ourselves as a plugin to Zend_Debug_FirePhp
     *
     * @return void
     */
    public function __construct()
    {
        Zend_Debug_FirePhp::getInstance()->addPlugin($this);    
    }

    /**
     * Flush all profiling data to FirePHP.
     * 
     * @param Zend_Debug_FirePhp $firephp The FirePHP instance
     * @return void
     */
    public function flush(Zend_Debug_FirePhp $firephp)
    {
        $table = array();
        
        $table[] = array('Time', 'Event', 'Params');
        
        $total_time = 0;
        
        foreach ($this->getQueryProfiles() as $query) {
          
            $params = $query->getQueryParams();
            if($params) {
                $params = '\''.implode('\', \'',array_values($params)).'\'';
            } else {
                $params = '';
            }
            
            $table[] = array(round($query->getElapsedSecs(),5),
                             $query->getQuery(),
                             $params);
            $total_time += $query->getElapsedSecs();
        }
        
        if(sizeof($table)>1) {
            $firephp->fire($table,
                           (sizeof($table)-1).' DB events in '.round($total_time,5).' seconds',
                           Zend_Debug_FirePhp::TABLE);
        }
    }
}
