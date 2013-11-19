<?php
    
    /***************************************************
    *    __  __    _____         _           ____      *
    *   |  \/  |__|_   _|_ _ ___| |_ _   _  |___ \     *
    *   | |\/| / __|| |/ _` / __| __| | | |   __) |    *
    *   | |  | \__ \| | (_| \__ \ |_| |_| |  / __/     *
    *   |_|  |_|___/|_|\__,_|___/\__|\__, | |_____|    *
    *                                |___/             *
    *   MsTasty Core 2                                 *
    *   (C) 2010 dbeuchert.com // David Beuchert       *
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
    
    
    class core_connect Extends IRCCore {
    
        public $socket;
    
        public function __construct ( $cfg ) { $this -> cfg = $cfg; }
    
    
        public function to_main () {
        
            // reading connect data out of the config and starting process
            
            // connection info
            $host    = $this -> cfg ['CNT']['host'];
            $port    = $this -> cfg ['CNT']['port'];
            $bindto  = $this -> cfg ['CNT']['bindto'];
            $retries = $this -> cfg ['CNT']['retries'];
            $timeout = $this -> cfg ['CNT']['timeout'];
            $pass    = $this -> cfg ['CNT']['pass'];
            
            
            // bot info
            $nick    = $this -> cfg ['BOT']['nick'];
            $user    = $this -> cfg ['BOT']['user'];
            $real    = $this -> cfg ['BOT']['real'];
            
            $done_retries = 1;
            
            while ( $this -> open_connection ( $host, $port, $nick, $user, $real, $retries, $timeout, $pass, $bindto ) === false && $done_retries <= $retries ) {
            
                // if the connection could not be established, wait 30 seconds (*retries, so if 6 retries it will wait 3 minutes between the last retry and the next)
                aout('SERVER_CONNECTION_RETRYPENDING', ( $done_retries * 30 ) );
                sleep ( $done_retries * 30 );
                aout('SERVER_CONNECTION_RETRY');
                
                $done_retries++;
            
            }
            
            // if we hit the retry-limit, there is no connection
            if ( $done_retries >= $retries ) {
            
                // tell if still no connection after n retries
                aout ('SERVER_CONNECTION_NORETRY',$retries);
                
            }
            else {
            
                // if not, there is one (but check to be sure)
                if ( is_resource ( $this -> socket ) ) {
                
                }
                
            
            }
        
        }
        
        private function open_connection ( $host, $port, $nick, $user, $real, $retries, $timeout, $pass='', $bindto=false ) {
        
            aout('SERVER_CONNECT',array($host,$port,$nick,$user,$real));
            
            // if we got an IP, bind it to
            if ( $bindto !== false && !empty ( $bindto ) ) {
            
                aout ('SERVER_BINDTO',$bindto);
                
                $opts = array(
                    'socket' => array(
                        'bindto' => $bindto . ':0'
                    )
                );
            
            }
            else {
            
                // leave the options empty
                $opts = array();
                
            }
            
            // create context
            $context = stream_context_create ($opts);
            
            if ( $this -> socket = @stream_socket_client ( $host . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context) ) {
                
                aout ( 'SERVER_CONNECTION_ESTABLISHED' );
                
                out('');
                
                // identify yourself
                
                if ( !empty ( $pass ) ) {
                
                    // send password if required
                    $this -> snd ( 'PASS ' . $pass, 'PASS ****' );
                    
                }
                
                $this -> snd ( 'USER ' . $user . ' * * :' . $real );
                $this -> snd ( 'NICK '.$nick );
                
                // hold until bot is ready
                return $this -> make_ready ();
            
            }
            else {
            
                aout ('SERVER_CONNECTION_FAILED',array($host,$port,$errstr,$errno));
                return false;
            
            }
        
        }
        
        private function make_ready () {
        
            $signup_is_done = false;
        
            // hold connection up until the identify is done or the connection is lost
            while ( !feof ( $this -> socket ) && $signup_is_done !== true && is_resource ( $this -> socket ) ) {
            
            
                // get line (if something goes in)
                $in = trim ( fgets ( $this -> socket ) );
                out ('<- ' . $in,false,2,true);
                
                $inar = explode ( ' ', $in );
                
                
                // answer to ping
                if ( $inar[0] == 'PING' ) {
                
                    $this -> snd ( 'PONG ' . $inar[1] );
                    
                }
                
                // if no ping, handle else (to get info about network name, server name and when to end login-process)
                else {
                
                    switch ( $inar[1] ) {
                    
                        // handle nickname collisions
                        case '431':
                        case '432':
                        case '433':
                        case '436':
                            out();
                            $this -> disconnect ( false );
                            aout('SERVER_NICKNAME_FAIL',$inar[1]);
                            sleep(2);
                            return false;
                        break;
                    
                        // filter servername out of this
                        case '004':
                            $server_name = $inar[3];
                        break;
                        
                        // filter networkname
                        case '005':
                        
                            if ( preg_match ( '/NETWORK=([^\s]+)/i', implode ( ' ', $inar ), $match ) ) {
                            
                                $network_name   = $match[1];
                                $signup_is_done = true;
                                continue;
                            
                            }
                            
                        break;
                    
                    }
                    
                }
                
            
            }
            
            if ( $signup_is_done === true ) {
            
                aout('SERVER_CONNECTION_LOGIN',array($server_name,$network_name));
                return true;
                
            }
            else {
            
                aout ( 'SERVER_CONNECTION_LOST' );
                return false;
                
            }
        
        }
        
        public function return_res () {
        
            return $this -> socket;
        
        }
    
    }
    
    
?>