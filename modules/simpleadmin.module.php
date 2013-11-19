<?php
    class mod_simpleadmin {
    
        function __construct (&$core,$id) {
        
            $this -> core = &$core;
            $this -> id   = $id;
            
            $core -> register ( 'PRIVMSG','!*',true, $this->id );
        
        }
        
        function in ( $in ) {
            $core = $this -> core;
        
            if ( has_flags ( $in['sender'], $core -> cvar ( 'ADM', 'admin' ) ) ) {
            
                $cmd = strtoupper ( substr ( $in['atext'][0], 1 ) );
                
                if ( $cmd == 'ACTION' ) {
                
                    $core->action ( $in['args'][0], implode ( ' ', array_slice ( $in['atext'], 1 ) ) );
                
                }
                
            }
            else {
            
                $core -> notice ( $in['sender']['nick'], '%COMMAND_PERMISSION_DENIED%' );
                
            }
            
        }
        
    }
?>