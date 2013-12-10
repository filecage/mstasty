<?php
    class mod_simpleadmin {

        /**
         * @var IRCCore
         */
        protected $core;

        /**
         * @var string
         */
        protected $id;

        /**
         * @param IRCCore $core
         * @param string $id
         */

        function __construct ($core,$id) {
        
            $this -> core = $core;
            $this -> id   = $id;

            $bind = Array(
                '!*',
                'JOIN'
            );
            
            $core -> register ('PRIVMSG', $bind,true, $this->id );
        
        }
        
        function in ($in) {
            $core = $this -> core;
        
            if ( has_flags ( $in['sender'], $core -> cvar ( 'ADM', 'admin' ) ) ) {
            
                $cmd  = strtoupper ( substr ( $in['atext'][0], 1 ) );
                $args = array_slice($in['atext'], 1);
                
                switch($cmd) {
                    case 'ACTION':
                        $core->action ( $in['args'][0], implode ( ' ', array_slice ( $in['atext'], 1 ) ) );
                        break;

                    case 'JOIN':
                        foreach ($args as $chan) {
                            $core->write('JOIN ' . $chan);
                        }
                        break;
                }
                
            }
            
        }
        
    }
?>