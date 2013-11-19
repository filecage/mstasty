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
    
    
    class core_parser Extends IRCCore {
    
        public $raw_str;
        public $prs_arr;
    
        public function __construct () {
        
        }
        
        public function parse ( $raw_src ) {
        
            /********************************************
                //      PARSER OUTPUT
                //
                //      array (
                //             sender  => array (
                //                                 nick   => SENDER_NICK,
                //                                 ident  => SENDER_IDENT,
                //                                 host   => SENDER_HOST,
                //                                 full   => SENDER_FULL,
                //                                 prefix => SENDER_IDENT_PREFIX
                //                       )
                //             command => string ( COMMAND )
                //             args    => array ( ARGUMENTS )
                //             atext   => array ( WORD1, WORD2, etc. ),
                //             text    => array (        
                //                              full  => string ( FULL TEXT )
                //                              priv  => bool ( IS_PRIVATE_MESSAGE )
                //                  )
                //             raw  => string ( RAW LINE )
                //
                // It also returns every output above with a numeric key.
                // The numerics have not been deleted because they were implemented before we recognized that
                // it's actually better with strings.... shit happens. (don't use the numerics, they will be removed in later versions)
            ********************************************/
            
            
            
            if ( $raw_src[0] == ':' ) {
           
                // if the command is prefixed with :, we need to delete it
                $raw      = substr ( trim ( $raw_src ), 1 );
                $not_real = false;
                
            }
            else {
            
                // if not, everythings okay
                $raw      = trim ( $raw_src );
                $not_real = true;
                
            }
            
            // if the string is empty, return PARSE_EMPTY_STRING (important to save CPU when in noblock-mode)
            if ( empty ( $raw ) ) {

                return 'PARSE_EMPTY_STRING';
                
            }
            
            $raw_arr = array();
            $raw_arr = explode ( ' ', $raw );
            
            // first parse sender
            $sender     = $raw_arr[0];
            $sender_arr = array();
            
            // check if incoming is sent by command or a ping
            if ( $not_real === true ) {
            
                $raw_arr    = array_merge ( array ( 0 => NULL ), $raw_arr );
                $sender_arr = array ( 0 => NULL, 1 => NULL, 2 => NULL, 3 => NULL, 4 => NULL );
                $multiplier = 1;
                
            }
            
            
            // check if sender is a client or a server
            elseif ( strstr ( $sender, '!' ) !== false && strstr ( $sender, '@' ) !== false ) {
            
                $multiplier = 2;
            
                $sender_arr[0] = substr ( $sender, 0, strpos ( $sender, '!' ) );
                $sender_arr[2] = substr ( $sender, ( strpos ( $sender, '@' ) + 1 ) );
                $sender_arr[1] = str_replace ( '@' . $sender_arr[2], '', substr ( $sender, ( strpos ( $sender, '!' ) + 1 ) ) );
                
                if ( $sender_arr[1][0] == '~' ) {
                
                    $sender_arr[1] = substr ( $sender_arr[1], 1 );
                    $sender_arr[4] = '~';
                    
                }
                else {
                
                    $sender_arr[4] = '';
                    
                }
            
            }
            else {
            
                $multiplier    = 2;
                $sender_arr[0] = $sender;
                $sender_arr[1] = $sender;
                $sender_arr[2] = $sender;
                $sender_arr[4] = '';
                
            }
            $sender_arr[3] = $raw_arr[0];
            
            // get command (easiest part)
            $command = $raw_arr[1];
            
            // get arguments (all between command and the : which introduces text)
            $arguments = explode ( ' ', trim ( substr ( implode ( ' ', array_slice ( $raw_arr, 2 ) ), 0, strpos ( implode ( ' ', array_slice ( $raw_arr, 2 ) ), ':' ) ) ) );
            
            if ( empty ( $arguments[0] ) ) {
            
                $arguments = array();
                
            }
            // and get the text
            $text    = array_slice ( $raw_arr, ( 2 + count ( $arguments ) ) );
            $text[0] = substr ( $text[0], 1 );
            
            if ( $command == 'PRIVMSG' && strtolower ( $arguments[0] ) == strtolower ( $this->mvar('nick') ) ) {
            
                $is_private_message = true;
                
            }
            else {
            
                $is_private_message = false;
              
            }
            
            
            return array (
            
                    0 => array ( $sender_arr[0], $sender_arr[1], $sender_arr[2], $sender_arr[3], $sender_arr[4] ),
                    1 => $command,
                    2 => $arguments,
                    3 => array ( $text, implode ( ' ', $text ), $is_private_message ),
                    4 => trim ( $raw_src ),
                    'sender'  => array (
                            'nick'   => $sender_arr[0],
                            'ident'  => $sender_arr[1],
                            'host'   => $sender_arr[2],
                            'full'   => $sender_arr[3],
                            'prefix' => $sender_arr[4] ),
                    'command' => $command,
                    'args'    => $arguments,
                    'atext'   => $text,
                    'text'    => array (
                                        'full'  => implode ( ' ', $text ),
                                        'priv'  => $is_private_message ),
                    'raw'     => trim ( $raw_src )
                    
            );
  
        
        }
        
    }