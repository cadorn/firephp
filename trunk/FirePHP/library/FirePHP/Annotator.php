<?php

class FirePHP_Annotator
{
    protected static $_variablesStack = array();


    public static function setVariables($variables)
    {
        array_push(self::$_variablesStack, $variables);
    }
    
    public static function clearVariables()
    {
        array_pop(self::$_variablesStack);
    }
    
    public static function getVariables()
    {
        if(!self::$_variablesStack) {
            return null;
        }
        return self::$_variablesStack[sizeof(self::$_variablesStack)-1];
    }

}
