<?php

class FirePHP_Client_CLI extends FirePHP_Client
{
    
    
    
    public function log(FirePHP_Rep $rep)
    {
        
        echo $rep->toString() . "\n";
        
    }
    
    
}
