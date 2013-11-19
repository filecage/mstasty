<?php
    
    /***************************************************
    *    __  __    _____         _           ____      *
    *   |  \/  |__|_   _|_ _ ___| |_ _   _  |___ \     *
    *   | |\/| / __|| |/ _` / __| __| | | |   __) |    *
    *   | |  | \__ \| | (_| \__ \ |_| |_| |  / __/     *
    *   |_|  |_|___/|_|\__,_|___/\__|\__, | |_____|    *
    *                                |___/             *
    *   MsTasty Core 2                                 *
    *   http://github.com/filecage/mstasty             *
    *                                                  *
    *   This software is licensed under GNU GPL.       *
    *   You can use, edit and redistribute it for free *
    *   For further information see the LICENSE.txt in *
    *   the license-directory. You should also check   *
    *   the WARRANTY.txt in the same directory when    *
    *   changing or using this software.               *
    *                                                  *
    *   When making changes, please do not remove this *
    *   mark as we spent a lot of time creating this   *
    *   software.                                      *
    *                                                  *
    ***************************************************/
    
    class core_http extends IRCCore {
    
        private $open_connections;
    
        public function __construct () {
        
            ini_set( 'user_agent', $this -> cvar ( 'HTTP', 'user-agent') );
            
            $this -> user_agent = $this -> cvar ( 'HTTP', 'user-agent' );
            $this -> timeout    = $this -> cvar ( 'HTTP', 'timeout' );
            
            $this -> context    = stream_context_create (
                array ( 'http' => array (
                    'user-agent' => $this -> user_agent,
                    'timeout'    => $this -> timeout,
                ) )
            );
        
        }
        
        public function request ( &$modul, $url, $post_data = false ) {
            
            $url = trim ( $url );
            
            if ( !empty ( $url ) ) {
        
                $id                          = gen_sess_id(18);
                $this -> open_connections [] = array ( $id, $url, $modul, $post_data );
                stream_set_blocking ( IRCCore::$o_socket, false );
                
                return $id;
            
            }
            else {
            
                return false;
                
            }
        
        }
        
        public function check_open_requests () {
        
            foreach ( $this -> open_connections as $id => $con_arr ) {
            
                $content = file_get_contents ( $con_arr [1], false, $this -> context );
                $con_arr[2] -> http_in ( $con_arr[0], $content );
                unset ( $this -> open_connections [ $id ] );
            }
            
            if ( count ( $this -> open_connections ) == 0 && count ( IRCCore::$timer[0] ) == 0 ) {
                
                stream_set_blocking ( IRCCore::$o_socket, true );
            
            }
        
        }
        
        public function connections () {
        
            return count ( $this -> open_connections );
        
        }
        
        
    
    }
    
?>