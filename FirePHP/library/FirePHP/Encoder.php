<?php

class FirePHP_Encoder
{
    const UNDEFINED = '_U_N_D_E_F_I_N_E_D_';
    
    protected $options = array('maxObjectDepth' => 10,
                               'maxArrayDepth' => 20,
                               'useNativeJsonEncode' => true,
                               'includeLineNumbers' => true);    
    
    
    
    /**
     * @firephp Filter = On
     */
    protected $_origin = self::UNDEFINED;
    
    
    /**
     * @firephp Filter = On
     */
    protected $_instances = array();
        
    
    
    
    public function setOrigin($variable)
    {
        $this->_origin = $variable;
    }
    
    
    public function encode()
    {
        $graph = array();
        
        if($this->_origin!==self::UNDEFINED) {
            $graph['origin'] = $this->_encodeVariable($this->_origin);
        }
        
        if($this->_instances) {
            foreach( $this->_instances as $key => $value ) {
                $graph['instances'][$key] = $value[1];
            }
        }
        
        return $graph;
    }


    protected function _encodeVariable($Variable, $ObjectDepth = 1, $ArrayDepth = 1)
    {
        if(is_null($Variable)) {
            return array('type'=>'null');
        } else
        if(is_bool($Variable)) {
            return array('type'=>'boolean', 'value'=> ($Variable)?'1':'0');
        } else
        if(is_int($Variable)) {
            return array('type'=>'integer', 'value'=> $Variable);
        } else
        if(is_float($Variable)) {
            return array('type'=>'float', 'value'=> $Variable);
        } else
        if(is_double($Variable)) {
            return array('type'=>'double', 'value'=> $Variable);
        } else
        if(is_object($Variable)) {
            
            return array('type'=>'object', 'instance'=> $this->_encodeInstance($Variable));
            
        } else
        if(is_array($Variable)) {
            
            // Check if we have an indexed array (list) or an associative array (map)
            $i = 0;
            foreach( array_keys($Variable) as $k ) {
                if($k!=$i++) {
                    $i = -1;
                    break;
                }
            }
            if($i==-1) {
                return array('type'=>'map', 'map'=> $this->_encodeAssociativeArray($Variable, $ObjectDepth, $ArrayDepth));
            } else {
                return array('type'=>'list', 'list'=> $this->_encodeArray($Variable, $ObjectDepth, $ArrayDepth));
            }
        } else
        if(is_resource($Variable)) {
            return array('type'=>'resource', 'value'=> $Variable);
        } else
        if(is_string($Variable)) {
            if(is_utf8($Variable)) {
              return array('type'=>'string', 'value'=> $Variable);
            } else {
              return array('type'=>'string', 'value'=> utf8_encode($Variable));
            }
        } else {
            return array('type'=>'unknown', 'value'=> $Variable);
        }        
    }
    
    protected function _getInstanceID($Object)
    {
        foreach( $this->_instances as $key => $instance ) {
            if($instance[0]===$Object) {
                return $key;
            }
        }
        return null;
    }
    
    protected function _encodeInstance($Object, $ObjectDepth = 1, $ArrayDepth = 1)
    {
        $id = $this->_getInstanceID($Object);
        if($id!==null) {
            return $id;
        }
        
        $id = sizeof($this->_instances);
        $this->_instances[$id] = array($Object);
        $this->_instances[$id][1] = $this->_encodeObject($Object, $ObjectDepth, $ArrayDepth);
        
        return $id;
    }    
    
    protected function _encodeAssociativeArray($Variable, $ObjectDepth = 1, $ArrayDepth = 1)
    {
        if ($ArrayDepth > $this->options['maxArrayDepth']) {
          return '** Max Array Depth ('.$this->options['maxArrayDepth'].') **';
        }
      
        foreach ($Variable as $key => $val) {
          
          // Encoding the $GLOBALS PHP array causes an infinite loop
          // if the recursion is not reset here as it contains
          // a reference to itself. This is the only way I have come up
          // with to stop infinite recursion in this case.
          if($key=='GLOBALS'
             && is_array($val)
             && array_key_exists('GLOBALS',$val)) {
            $val['GLOBALS'] = '** Recursion (GLOBALS) **';
          }
          
          $return[] = array($this->_encodeVariable($key), $this->_encodeVariable($val, 1, $ArrayDepth + 1));
        }
        return $return;    
    }
    
    protected function _encodeArray($Variable, $ObjectDepth = 1, $ArrayDepth = 1)
    {
        if ($ArrayDepth > $this->options['maxArrayDepth']) {
          return '** Max Array Depth ('.$this->options['maxArrayDepth'].') **';
        }
        $items = array();
        foreach ($Variable as $val) {
          $items[] = $this->_encodeVariable($val, 1, $ArrayDepth + 1);
        }
        return $items;     
    }
    
    
    protected function _encodeObject($Object, $ObjectDepth = 1, $ArrayDepth = 1)
    {
        $return = array();

        if ($ObjectDepth > $this->options['maxObjectDepth']) {
          return '** Max Object Depth ('.$this->options['maxObjectDepth'].') **';
        }
                
        $return['class'] = $class = get_class($Object);
        
        $classAnnotations = $this->_getClassAnnotations($class);

        $properties = $this->_getClassProperties($class);

        $reflectionClass = new ReflectionClass($class);  
        
        $return['file'] = $reflectionClass->getFileName();
            
        $members = (array)$Object;

        foreach( $properties as $name => $property ) {
            
          $info = array();
          $info['name'] = $name;
          
          $raw_name = $name;
          if($property->isStatic()) {
            $info['static'] = 1;
          }
          if($property->isPublic()) {
            $info['visibility'] = 'public';
          } else
          if($property->isPrivate()) {
            $info['visibility'] = 'private';
            $raw_name = "\0".$class."\0".$raw_name;
          } else
          if($property->isProtected()) {
            $info['visibility'] = 'protected';
            $raw_name = "\0".'*'."\0".$raw_name;
          }

          if(isset($classAnnotations['$'.$name])
             && isset($classAnnotations['$'.$name]['Filter'])
             && $classAnnotations['$'.$name]['Filter']=='On') {
                   
              $info['filtered'] = 'annotation';
          } else
          if(isset($this->objectFilters[$class])
             && is_array($this->objectFilters[$class])
             && in_array($name,$this->objectFilters[$class])) {
                   
              $info['filtered'] = 'filters';
          } else {

            if(array_key_exists($raw_name,$members)
               && !$property->isStatic()) {

                $info['value'] = $this->_encodeVariable($members[$raw_name], $ObjectDepth + 1, 1);
            
            } else {
              if(method_exists($property,'setAccessible')) {
                $property->setAccessible(true);
              }
              try {
                $info['value'] = $this->_encodeVariable($property->getValue($Object), $ObjectDepth + 1, 1);
              } catch(ReflectionException $e) {
                $info['error'] = 'Need PHP 5.3 to get value';
              }
            }
          }
          
          $return['members'][] = $info;
        }
        
        // Include all members that are not defined in the class
        // but exist in the object
        foreach( $members as $name => $value ) {
          
          if ($name{0} == "\0") {
            $parts = explode("\0", $name);
            $name = $parts[2];
          }
          
          if(!isset($properties[$name])) {
            
            $info = array();
            $info['undeclared'] = 1;
            $info['name'] = $name;

            if(isset($classAnnotations['$'.$name])
               && isset($classAnnotations['$'.$name]['Filter'])
               && $classAnnotations['$'.$name]['Filter']=='On') {
                       
                $info['filtered'] = 'annotation';
            } else
            if(isset($this->objectFilters[$class])
               && is_array($this->objectFilters[$class])
               && in_array($name,$this->objectFilters[$class])) {
                       
                $info['filtered'] = 'filters';
            } else {
                $info['value'] = $this->_encodeVariable($value, $ObjectDepth + 1, 1);
            }

            $return['members'][] = $info;    
          }
        }
        
        return $return;
    }
    
    
    protected function _getClassProperties($class)
    {
        $reflectionClass = new ReflectionClass($class);  
                
        $properties = array();

        // Get parent properties first
        if($parent = $reflectionClass->getParentClass()) {
            $properties = $this->_getClassProperties($parent->getName());
        }
        
        foreach( $reflectionClass->getProperties() as $property) {
          $properties[$property->getName()] = $property;
        }
        
        return $properties;
    }
    
    protected function _getClassAnnotations($class)
    {
        $annotations = array();
        
        // TODO: Go up to parent classes (let subclasses override tags from parent classes)
        
        $reflectionClass = new Zend_Reflection_Class($class);
        
        foreach( $reflectionClass->getProperties() as $property ) {
            
            $docblock = $property->getDocComment();
            if($docblock) {
                
                $tags = $docblock->getTags('firephp');
                if($tags) {
                    foreach($tags as $tag) {
                       
                       list($name, $value) = $this->_parseAnnotationTag($tag);
                       
                       $annotations['$'.$property->getName()][$name] = $value;
                    }
                }
            }
        }
        
        return $annotations;
    }
    
    protected function _parseAnnotationTag($tag) {
        
        if(!preg_match_all('/^([^)\s]*?)\s*=\s*(.*?)$/si', $tag->getDescription(), $m)) {
            FirePHP_Annotator::setVariables(array('tag'=>$tag));
            throw new Exception('Tag format not valid!');
        }
        
        return array($m[1][0], $m[2][0]);
    }

}
