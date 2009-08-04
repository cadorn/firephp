<?php

class FirePHP_Rep_PHP_LabeledVariable extends FirePHP_Rep_PHP_Variable
{

    protected $_label = null;
    
    
    public function setData($data)
    {
        parent::setData($data['variable']);
        
        $this->_label = $data['label'];
    }


    public function toString()
    {
        $this->_colorizer = new Zend_Tool_Framework_Client_Console_ResponseDecorator_Colorizer();

        $string = array();
        
        $string[] = array(array($this->_label, 'hiYellow'), ' : ', parent::toString());
        

        for( $i=0 ; $i<sizeof($string) ; $i++ ) {
            $string[$i] = $this->_markupLine($string[$i]);
        }

        return implode("\n", $string);
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
}
