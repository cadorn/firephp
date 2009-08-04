<?php

class FirePHP_Rep_PHP_Variable extends FirePHP_Rep
{

    protected $_colorizer = null;



    public function toString()
    {
        $this->_colorizer = new Zend_Tool_Framework_Client_Console_ResponseDecorator_Colorizer();

        $string = array();
        
        $encoder = new FirePHP_Encoder();
        $encoder->setOrigin($this->getData());
        
        $graph = $encoder->encode();
	    $string[] = $this->_renderOrigin($graph);

        return implode("\n", $string);
    }



    
    protected function _renderOrigin(&$graph)
    {
        return $this->_render($graph, $graph['origin']);
    }
    
    protected function _render(&$graph, $var)
    {
        static $_instanceStack = array();

        $string = array();
        
        switch($var['type']) {
            
            case 'null':
                $string[] = array(array('NULL','blue'));
            	break;
            case 'boolean':
                $string[] = array(array(($var['value'])?'TRUE':'FALSE','blue'));
            	break;
            case 'integer':
            case 'float':
            case 'double':
                $string[] = array(array((string)$var['value'],'green'));
            	break;
            case 'object':
            	
                if(in_array($var['instance'], $_instanceStack)) {

                    $string[] = array(array('** Recursion ('.$graph['instances'][$var['instance']]['class'].') **', 'magenta'));
                    
                } else {
                    array_push($_instanceStack, $var['instance']);
                        	
                    $instance = $graph['instances'][$var['instance']];
                        	
                    $string[] = array(array($instance['class'] . ' (','cyan'));
                    
                    if(isset($instance['members'])) {
                        foreach( $instance['members'] as $member ) {
                            
                            $key = $this->_render($graph, array('type'=>'string','value'=>$member['name']));
                            
                            if(isset($member['filtered'])) {
                                
                                $string[] = array('  ', $key, ' = ', array('** Excluded by Filter **', 'magenta'));
                                
                            } else {
                                
                                if(isset($member['error'])) {
                                    
                                    $string[] = array('  ', $key, ' = ', array('** ' . $member['error'] . ' **', 'magenta'));
                                    
                                } else {
                                    $result = explode("\n", $this->_render($graph, $member['value']));
                        
                                    $string[] = array('  ', $key, ' = ', $result[0]);
                                    
                                    if(sizeof($result)>1) {
                                        for( $i=1 ; $i<sizeof($result) ; $i++ ) {
                                            $string[] = array('  ', $result[$i]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $string[] = array(array(')', 'cyan'));
                    
                    array_pop($_instanceStack);
                }
            	break;
            case 'map':
                $string[] = 'array(';
                
                foreach( $var[$var['type']] as $value ) {
                    
                    $result = explode("\n", $this->_render($graph, $value[1]));
    
                    $string[] = array('  [',$this->_render($graph, $value[0]),'] => ', $result[0]);
                    
                    if(sizeof($result)>1) {
                        for( $i=1 ; $i<sizeof($result) ; $i++ ) {
                            $string[] = array('  ',$result[$i]);
                        }
                    }
                }
                
                $string[] = ')';
            	break;
            case 'list':
                $string[] = 'array(';
                
                for( $i=0 ; $i<sizeof($var[$var['type']]) ; $i++ ) {
                    
                    $result = explode("\n", $this->_render($graph, $var[$var['type']][$i]));
    
                    $string[] = array('  [',$this->_render($graph, array('type'=>'integer', 'value'=>$i)),'] => ', $result[0]);
                    
                    if(sizeof($result)>1) {
                        for( $i=1 ; $i<sizeof($result) ; $i++ ) {
                            $string[] = array('  ',$result[$i]);
                        }
                    }
                }
                
                $string[] = ')';
            	break;
            case 'resource':
                $string[] = array(array('** '.(string)$var['value'].' **', 'magenta'));
            	break;
            case 'string':
            case 'unknown':
                $string[] = array(array('\'' . $var['value'] . '\'','red'));
            	break;
        }
        
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
