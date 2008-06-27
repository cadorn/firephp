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

/** Zend_Debug_MethodHandler_Interface */
require_once 'Zend/Debug/MethodHandler/Interface.php';

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
     * List of Zend_Debug_MethodHandler_Interface
     * @var array
     */
    protected static $_handlers = array();

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
     * Register a handler for a specific method
     *
     * @param  string $method The method that should trigger this handler
     * @param  Zend_Debug_MethodHandler_Interface $handler Handler object to register
     * @return boolean Returns TRUE if handler was added for method, FALSE if already added.
     */
    public static function registerMethodHandler($method, Zend_Debug_MethodHandler_Interface $handler)
    {
        if (isset(self::$_handlers[$method]) && in_array($handler,self::$_handlers[$method])) {
            return false;
        }
        
        self::$_handlers[$method][] = $handler;
        return true;
    }

    /**
     * Unregister a handler for a specific method
     *
     * @param  string $method The method that should trigger this handler
     * @param  Zend_Debug_MethodHandler_Interface $handler Handler object to register
     * @return boolean Returns TRUE if handler was added for method, FALSE if already added.
     */
    public static function unregisterMethodHandler($method, Zend_Debug_MethodHandler_Interface $handler)
    {
        if (isset(self::$_handlers[$method]) && in_array($handler,self::$_handlers[$method])) {
            unset(self::$_handlers[$method][array_search($handler,self::$_handlers[$method])]);
            if (sizeof(self::$_handlers[$method])==0) {
                unset(self::$_handlers[$method]);
            }
            return true;
        }
        return false;
    }

    /**
     * Dispatch a method call to all registered handlers.
     * 
     * @param string $method The target method.
     * @param array $arguments An array of arguments to be passed to the handler
     * @return boolean Returns TRUE if method call was dispatched, FALSE if no handlers were found.
     */
    protected static function _dispatchToMethodHandlers($method, $arguments)
    {
        if (!isset(self::$_handlers[$method])) {
            return false;
        }
        foreach (self::$_handlers[$method] as $handler) {
            $handler->handleDebugMethod($method, $arguments);
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
     * @return boolean Returns TRUE if message was delivered to handlers, FALSE otherwise.
     */
    public static function trace($var, $label=null, $type=null)
    {
        $args = func_get_args();
        return self::_dispatchToMethodHandlers('trace', $args);
    }
}
