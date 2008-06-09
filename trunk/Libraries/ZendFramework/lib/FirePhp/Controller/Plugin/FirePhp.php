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
 * @category   FirePhp
 * @package    FirePhp_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */


/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** FirePhp_Db_Profiler_FirePhp */
require_once 'FirePhp/Db/Profiler/FirePhp.php';


/**
 * Writes DB events as log messages to the Firebug Console via FirePHP.
 * 
 * @category   FirePhp
 * @package    FirePhp_Controller
 * @subpackage Plugin
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
class FirePhp_Controller_Plugin_FirePhp extends Zend_Controller_Plugin_Abstract
{
    
    /**
     * Holds a reference to the profile used for DB queries.
     * 
     * @var FirePhp_Db_Profiler_FirePhp
     */
    private $profiler = null;


    /**
     * Write a message to FirePHP if the extension is installed.
     *
     * @param  array  $event  event data
     * @return void
     */
    public function setProfiler(FirePhp_Db_Profiler_FirePhp $profiler)
    {
      $this->profiler = $profiler;
    }


    /**
     * Flush all FirePHP data after the controller has dispatched the request.
     *
     * @param  Zend_Controller_Request_Abstract  $request  The controller request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
      if($this->profiler!=null)
      {
        $this->profiler->flush();
      }
    }

}
