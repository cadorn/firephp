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
 * @package    Zend_Debug
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Debug_Plugin_Interface */
require_once 'Zend/Debug/Plugin/Interface.php';

/**
 * Concrete class for generating debug dumps related to the output source.
 *
 * @category   Zend
 * @package    Zend_Debug
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Debug
{

    /**
     * @var string
     */
    protected static $_sapi = null;

    /**
     * List of Zend_Debug_Plugin_Interface
     * @var array
     */
    protected static $_plugins = array();

    /**
     * Get the current value of the debug output environment.
     * This defaults to the value of PHP_SAPI.
     *
     * @return string;
     */
    public static function getSapi()
    {
        if (self::$_sapi === null) {
            self::$_sapi = PHP_SAPI;
        }
        return self::$_sapi;
    }

    /**
     * Set the debug ouput environment.
     * Setting a value of null causes Zend_Debug to use PHP_SAPI.
     *
     * @param string $sapi
     * @return void;
     */
    public static function setSapi($sapi)
    {
        self::$_sapi = $sapi;
    }

    /**
     * Register a plugin for a specific method
     *
     * @param  Zend_Debug_Plugin_Interface $plugin Plugin object to register
     * @param  string $method The method that should trigger this plugin
     * @return boolean Returns TRUE if plugin was added for method, FALSE if already added.
     */
    public static function registerPlugin(Zend_Debug_Plugin_Interface $plugin, $method)
    {
        if (isset(self::$_plugins[$method]) && in_array($plugin,self::$_plugins[$method])) {
            return false;
        }
        
        self::$_plugins[$method][] = $plugin;
        return true;
    }

    /**
     * Unregister a plugin for a specific method
     *
     * @param  Zend_Debug_Plugin_Interface $plugin Plugin object to unregister
     * @param  string $method The method that should trigger this plugin
     * @return boolean Returns TRUE if plugin was removed, FALSE otherwise.
     */
    public function unregisterPlugin(Zend_Debug_Plugin_Interface $plugin, $method)
    {
        if (isset(self::$_plugins[$method]) && in_array($plugin,self::$_plugins[$method])) {
            unset(self::$_plugins[$method][array_search($plugin,self::$_plugins[$method])]);
            if (sizeof(self::$_plugins[$method])==0) {
                unset(self::$_plugins[$method]);
            }
            return true;
        }
        return false;
    }

    /**
     * Dispatch a method call to all registered plugins.
     * 
     * @param string $method The target method.
     * @param array $arguments An array of arguments to be passed to the plugin
     * @return boolean Returns TRUE if method call was dispatched, FALSE if no plugins were found.
     */
    protected static function _dispatch($method, $arguments)
    {
        if (!isset(self::$_plugins[$method])) {
            return false;
        }
        foreach (self::$_plugins[$method] as $plugin) {
            $plugin->handleDebugCall($method, $arguments);
        }
        return true;
    }

    /**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var   The variable to dump.
     * @param  string $label OPTIONAL Label to prepend to output.
     * @param  bool   $echo  OPTIONAL Echo output if true.
     * @return string
     */
    private static function dump($var, $label=null, $echo=true)
    {
        // format the label
        $label = ($label===null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (self::getSapi() == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES)
                    . '</pre>';
        }

        if ($echo) {
            echo($output);
        }
        return $output;
    }
    
    /**
     * Debug helper function.
     * 
     * @return boolean Returns TRUE if message was delivered to plugins, FALSE otherwise.
     */
    public static function trace($var, $label=null, $type=null)
    {
        $args = func_get_args();
        return self::_dispatch('trace', $args);
    }
}
