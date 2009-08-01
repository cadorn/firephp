<?php

class FirePHP_Rep_PHP_Error extends FirePHP_Rep
{

    protected $_errorNames = array(
        1     => 'E_ERROR',
        2     => 'E_WARNING',
        4     => 'E_PARSE',
        8     => 'E_NOTICE',
        16    => 'E_CORE_ERROR',
        32    => 'E_CORE_WARNING',
        64    => 'E_COMPILE_ERROR',
        128   => 'E_COMPILE_WARNING',
        256   => 'E_USER_ERROR',
        512   => 'E_USER_WARNING',
        1024  => 'E_USER_NOTICE',
        2048  => 'E_STRICT',
        4096  => 'E_RECOVERABLE_ERROR',
        8192  => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
        30719 => 'E_ALL');

    protected $_colorizer = null;


    /*
        $message = array();
        $message['errno'] = $errno;
        $message['errstr'] = $errstr;
        $message['errfile'] = $errfile;
        $message['errline'] = $errline;
        $message['errcontext'] = $errcontext;
        $message['backtrace'] = debug_backtrace();
     */
    public function toString()
    {
        $this->_colorizer = new Zend_Tool_Framework_Client_Console_ResponseDecorator_Colorizer();

        $string = array();
        
        $string[] = array(array('LINE','bgRed'));
        
        $string[] = array(array($this->_message['errstr'],array('hiWhite','bgRed')),
                          array('SPACER', 'bgRed'),
                          array(' | ' . $this->_error2string($this->_message['errno']),'bgRed'));

        $string[] = array(array('LINE','bgRed'));

        $insertCodeOffset = null;
        if(isset($this->_message['backtrace'])) {

            $table = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT, '');
            $index = 1;
            for( $i=0 ; $i<sizeof($this->_message['backtrace']) ; $i++ ) {

                $frame = $this->_message['backtrace'][$i];
                $row = array();

                if($i==0) {
                    
                    // We are dealing with an exception
                    if(is_string($this->_message['errno'])) {

                        $table->addRow(array('   | ' . $this->_normalizeFilePath($this->_message['errfile']),
                                             '@ ' . $this->_message['errline']));

                    } else
                    // We are dealing with an error
                    if($frame['class']=='FirePHP_Error_Handler') {

                        $i++;

                        if(!isset($frame['file']) && !isset($frame['line'])) {
                            $frame = $this->_message['backtrace'][$i];
                        }
                        $row[] = '   | ' . $this->_normalizeFilePath($frame['file']);
                        $row[] = '@ ' . $frame['line'];
                    } else {
                        throw new Exception('Could not determine where to insert code!');
                    }
                }
                
                if(!$row) {
                    if(!isset($frame['file'])) {
                        // This is a call_user_func*() call frame
                        if(sizeof($this->_message['backtrace'])>=$i+1) {
                            $frameNext = $this->_message['backtrace'][$i+1];
                            $row[] = '   | ' . $this->_normalizeFilePath($frameNext['file']);
                            $row[] = '@ ' . $frameNext['line'];
                            $row[] = '- call_user_func*(\''.$frame['class'].'\',\''.$frame['function'].'\')';
                            $i++;
                        }
                    } else {
                        $row[] = '   | ' . $this->_normalizeFilePath($frame['file']);
                        $row[] = '@ ' . $frame['line'];
                        $row[] = '- ' . ((isset($frame['type']))?
                                               $frame['class'] . $frame['type'] . $frame['function']:
                                               $frame['function']) . '('.$this->_renderArgs($frame['args']).')';
                    }
                }
                $table->addRow($row);
                $index++;
            }
            
            $insertCodeOffset = sizeof($string)+1;
            
            foreach( explode("\n", $table->getTable()) as $line ) {
                if($line = rtrim($line)) {
                    $string[] = rtrim($line);
                }
            }
        }

        if(isset($this->_message['errfile']) && isset($this->_message['errline'])) {
            $code = array();
            $code[] = '    |';

            $start = $this->_message['errline'] - 5;
            if($start<0) {
                $start = 0;
            }
            // TODO: Make number of lines configurable
            $end = $start + 10;
            $lines = file($this->_message['errfile']);
            if($end>=sizeof($lines)) {
                $end = sizeof($lines)-1;
            }
            for( $i=$start ; $i <= $end ; $i++ ) {
                $errorLine = ($i+1==$this->_message['errline']);
                $lines[$i] = str_replace("\t", '    ', rtrim($lines[$i]));
                $code[] = str_pad($i+1,4,' ',STR_PAD_LEFT). (($errorLine)?'|>':'| ') . $lines[$i];
                
                // See if we have annotated arguments on previous lines and insert them
                if($errorLine &&
                   ($vars = FirePHP_Annotator::getVariables())) {
                   
                    for( $j=sizeof($code)-1 ; $j>=sizeof($code)-4 ; $j-- ) {
                        if(($pos=strpos($code[$j], 'FirePHP_Annotator::setVariables('))!==false) {
                            
                            $varLines = array();
                            $varLines[] = '';
                            foreach( $vars as $name => $value ) {
                                $varLines[] = '    :' . str_repeat(' ',$pos-1) . $name . ' = ' . $this->_renderVar($value, 50);
                            }
                            $varLines[] = '';
                            
                            array_splice($code, $j+1, 0, $varLines);
                            break;
                        }
                    }
                    
                }
            }
            $code[] = '    |';

            if($insertCodeOffset!==null) {
                array_splice($string, $insertCodeOffset, 0, $code);
            } else {
                array_splice($string, sizeof($string), 0, $code);
            }
        }

        $string[] = array(array('LINE','bgRed'));
        $string[] = array('RIGHT', array('Generated by FirePHP: http://www.firephp.org/','cyan'));
        
        $this->_markupOutput($string);

        return implode("\n", $string);
    }
    
    
    public function shouldDisplay()
    {
        if(isset($this->_message['backtrace']) &&
           substr($this->_message['backtrace'][0]['file'],-16,16)=='/Zend/Loader.php' &&
            (
              preg_match_all('/^include\([^)]*\): failed to open stream: No such file or directory$/si', $this->_message['errstr'], $m) ||
              preg_match_all('/^include\(\): Failed opening \'[^\']*\' for inclusion \([^)]*\)$/si', $this->_message['errstr'], $m)
            )
          ) {

            return false;
        }
        
        return true;
    }
    
    
    protected function _markupOutput(&$string)
    {
        $lines = array();
        $maxLength = 0;
        for( $i=0 ; $i<sizeof($string) ; $i++ ) {
            $maxLength = max($maxLength, $this->_lineLength($string[$i]));
        }
        
        $border = array('|', 'bgRed');
        
        for( $i=0 ; $i<sizeof($string) ; $i++ ) {

            if(is_array($string[$i])) {
                
                if(sizeof($string[$i])==1 &&
                   (extract($this->_normalizeSegment($string[$i][0]))) &&
                   $part=='LINE') {
                       
                    if($i==0 || $i+1==sizeof($string)) {
                        $string[$i] = $this->_markupLine(array(array(str_repeat('-', $maxLength+4), $color)));
                    } else {
                        $string[$i] = $this->_markupLine(array($border, $this->_markupLine(array(array(str_repeat('-', $maxLength+2), $color))) , $border));
                    }
                } else
                if(sizeof($string[$i])==2 && $string[$i][0]=='RIGHT') {
                    $string[$i] = $this->_markupLine(array($border, ' ', str_repeat(' ', $maxLength-$this->_lineLength($string[$i])) .
                                                     $this->_markupLine(array($string[$i][1])), ' ' , $border));
                } else {
                    
                    for( $j=0 ; $j<sizeof($string[$i]) ; $j++ ) {
                        $part = $string[$i][$j];
                        if((is_string($part) && $part=='SPACER') ||
                           (is_array($part) && $part[0]=='SPACER')) {

                            $before = array_slice($string[$i], 0, $j);
                            $after = array_slice($string[$i], $j+1);
       
                            extract($this->_normalizeSegment($part));
                            
                            $padLength = $maxLength - $this->_lineLength($before) - $this->_lineLength($after);
                            if($padLength<0) {
                                $padLength = 0;
                            }
        
                            $string[$i] = $this->_markupLine(array($border, $this->_markupLine(array(array(' ',$color))), $this->_markupLine($before) .
                                          $this->_markupLine(array(array(str_repeat(' ', $padLength), $color))) .
                                          $this->_markupLine($after) , $this->_markupLine(array(array(' ',$color))), $border));
                        }
                    }
                }
            } else {
                $string[$i] = $this->_markupLine(array($border,' ', str_pad($string[$i], $maxLength, ' ', STR_PAD_RIGHT), ' ' , $border));
            }
        }
    }
    

    protected function _markupLine($line)
    {
        if(is_string($line)) {
            return $line;
        } else
        if(is_array($line)) {
            for( $i=0 ; $i<sizeof($line) ; $i++ ) {
                
                extract($this->_normalizeSegment($line[$i]));
                
                if($color!==null) {
                    $line[$i] = $this->_colorizer->decorate($part, $color);
                }
            }
            return join($line);
        }
    }
        
    protected function _normalizeSegment($segment) {
        if(is_string($segment)) {
            return array('part'=>$segment, 'color'=> null);
        } else {
            return array('part'=>$segment[0], 'color'=> $segment[1]);
        }
    }

    protected function _lineLength($line)
    {
        if(is_string($line)) {
            return strlen($line);
        } else
        if(is_array($line)) {
            $length = 0;
            foreach( $line as $part ) {
                if(is_string($part)) {
                    if(!in_array($part, array('LINE', 'RIGHT', 'SPACER'))) {
                        $length += strlen($part);
                    }
                } else
                if(is_array($part)) {
                    if(!in_array($part[0], array('LINE', 'RIGHT', 'SPACER'))) {
                        $length += strlen($part[0]);
                    }
                }
            }
            return $length;
        }
    }
    
    protected function _renderArgs($args)
    {
        if(!$args) {
            return '';
        }
        $string = array();
        foreach( $args as $arg ) {
            $string[] = $this->_renderVar($arg);
        }
        return implode(',', $string);
    }
    
    protected function _renderVar($var, $length=20)
    {
        if(is_null($var)) {
            return 'NULL';
        } else
        if(is_bool($var)) {
            return ($var)?'TRUE':'FALL';
        } else
        if(is_int($var) || is_float($var) || is_double($var)) {
            return $this->_trimString((string)$var, $length);
        } else
        if(is_object($var)) {
            return $this->_trimString(get_class($var), $length);
        } else
        if(is_array($var)) {
            return $this->_trimString(serialize($var), $length);
        } else
        if(is_resource($var)) {
            return $this->_trimString($var);
        } else
        if(is_string($var)) {
            return '\'' . $this->_trimString($var, $length) . '\'';
        } else {
            return '\'' . $this->_trimString($var, $length) . '\'';
        }
    }
    
    protected function _trimString($string, $length=20)
    {
        if(strlen($string)<=$length+3) {
            return $string;
        }
        return substr($string, 0, $length) . '...';
    }
    
    
    protected function _normalizeFilePath($path)
    {
        foreach( explode(PATH_SEPARATOR, get_include_path()) as $include_path ) {
            
            // For now we shorten paths based on the include paths
            // TODO: Provide an option not to shorten paths
            if( substr($path, 0, strlen($include_path)) == $include_path ) {
                return '..' . substr($path, strlen($include_path));
            }
        }
        return $path;
    }


    protected function _error2string($errorLevels)
    {
        return implode(' | ', $this->_error2array($errorLevels));
    }

    protected function _error2array($errorLevels)
    {
        $names = array();
        
        if(is_string($errorLevels)) {

            $names[] = $errorLevels;

        } else {
            
            if( ( $errorLevels & E_ALL ) == E_ALL)
            {
                $levels[] = 'E_ALL';
                $errorLevels &= ~E_ALL;
            }
            
            foreach( $this->_errorNames as $level => $name) {
                if( ($errorLevels & $level) == $level ) {
                    $names[] = $name;
                }
            }
        }
        
        return $names;
    }
}
