<?php

class FirePHP_Logger
{

    protected $_bootstrap = null;
    
    
    public function __construct(FirePHP_Bootstrap $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }





    public function log($repString, $message)
    {
        $rep = FirePHP_Rep::factory($repString);
        
        $rep->setMessage($message);
        
        // Check if the representation should actually be displayed
        if(!$rep->shouldDisplay()) {
            return false;
        }
        
        foreach( $this->_bootstrap->getClients() as $client ) {
            $client->log($rep);
        }
        
        return true;
    }
    
    
    

}
