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
    
    
    class core_handler extends IRCCore {
    
        function __construct () {
        
            $this -> mod_name = 'core_handler';
            $this -> register ( 'NICK', '', false );
            $this -> register ( 'PRIVMSG', "\x01" . '*', false );
            
        }
        
        function in ( $in ) {
        
        
            if ( $in['command'] == 'PRIVMSG' && $in['text']['priv'] == true && substr ( $in['text']['full'], -1 ) == "\x01" ) {
            
                foreach ( $in ['atext'] as $key => $text ) {
                
                    $in ['atext'][$key] = str_replace ( "\x01", '', $text );
                
                }
                
                if ( $in['atext'][0] == 'VERSION' ) {
                
                    $this -> ctcp ( $in['sender']['nick'], 'VERSION MsTasty 2 IRC Bot (v2.0.1 pre-alpha rev4) - http://mstasty.sourceforge.net' );
                    
                }
            
            }
            
            elseif ( $in['command'] == 'NICK' && $in['sender']['nick'] == $this -> mvar ( 'nick' ) ) {
            
                $this -> set_mvar ( 'nick', $in['atext'][0] );
            
            }
        
        }
    
    }