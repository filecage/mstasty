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
    
    class IRCcore {
    
        // set vars
        /**
         * The main socket of the connection
         * @var resource
         */
        public $socket;

        /**
         * @var resource
         */
        static public $o_socket;

        /**
         * List of module references, ordered by id => module
         * @var AbstractModule[]
         */
        static protected $mod_list;

        /**
         * Mapping for module names to id
         * @var Array
         */
        static protected $mod_name2id;

        /**
         * Mapping for module file names to id
         * @var Array
         */
        static protected $mod_file2id;

        /**
         * Mapping for module ids to command
         * @var Array
         */
        static protected $id2command;

        /**
         * Mapping for commands to module id
         * @var Array
         */
        static protected $bound_commands;

        /**
         * Information about the bot itself
         * @var mixed
         */
        static public $me;

        /**
         * Config array
         * @var Array
         */
        static protected $config;

        /**
         * List of timers in queue
         * @var Array
         */
        static protected $timer;

        /**
         * Static reference to $this
         * @var $this
         */
        static protected $obj;

        /**
         * Private config array
         * TODO: Use static property only
         * @var Array
         */
        protected $cfg;

        /**
         * Number of the times we tried to reconnect
         * @var int
         */
        private $has_retried;

        /**
         * Send buffer (strings in queue)
         * @var Array
         */
        private $send_buffer = Array();
    
        public function __construct ($config) {

            // make config global useable
            $this -> cfg         = $config;
            IRCCore::$config     = $config;
            IRCCore::$me         = array();
            IRCCore::$me['nick'] = $config ['BOT']['nick'];
            IRCCore::$obj        = &$this;
            
            // init timers
            IRCCore::$timer[0] = array();
            IRCCore::$timer[1] = array();
            
            // require all core-children
            require_once ( CORE_PATH . 'connect.core.php' );
            require_once ( CORE_PATH . 'parse.core.php' );
            require_once ( CORE_PATH . 'channels.core.php' );
            require_once ( CORE_PATH . 'modules.core.php' );
            require_once ( CORE_PATH . 'handler.core.php' );
            require_once ( CORE_PATH . 'http.core.php' );
            require_once ( CORE_PATH . 'MessageParser.php' );
            require_once ( CORE_PATH . 'User.php' );

            // create all core-children
            $this -> connect  = new core_connect( $this -> cfg );
            $this -> parser   = new core_parser();
            $this -> channels = new core_channels();
            $this -> modules  = new core_modules();
            $this -> handler  = new core_handler();
            $this -> http     = new core_http();
            
        
        }
        
        public function keepup () {

            // keep the connection up until it's closing
            while (!feof($this->socket)) {

                $message = fgets($this -> socket);
                $in = $this->parser->parse($message);
                
                // check if we got an empty string
                if ($in != 'PARSE_EMPTY_STRING') {
                    out ('<- ' . $in[4], false, 2, true);

                    if ($in['command'] == 'PING') {
                        $this->write('PONG :' . $in['text']['full']);
                    }
                
                    if (isset(IRCCore::$bound_commands[$in[1]])) {

                        // There are bound commands - parse input to MessageParser object (AbstractModules to expect it as object)
                        $messageObject = new MessageParser($message);

                        // Go through all the command bindings to see if theres a module we need to call
                        foreach (IRCCore::$bound_commands[$in[1]] as $mod_key => $mod_arr) {

                            // If the module does no longer exist, remove the binding and continue
                            if (!isset(IRCCore::$mod_list[ $mod_arr['id']])) {
                                unset (IRCCore::$bound_commands [$in[1]][$mod_key]);
                                continue;
                            }

                            $module = IRCCore::$mod_list[$mod_arr['id']];

                            if (is_object($module)) {
                                // For legacy reasons, only childs of AbstractModule do accept MessageParser objects
                                $moduleInput = ($module instanceof AbstractModule) ? $messageObject : $in;
                        
                                if (!empty($mod_arr['text'])) {
                                    // Replace wildcards in registrated text snippet
                                    $txt = str_replace ('*', '', $mod_arr['text'], $count);

                                    if ($count > 0) {
                                        if (strtoupper($txt) == strtoupper (substr($in['text']['full'], 0, strlen($txt)))) {
                                            $module->in($moduleInput);
                                        }
                                    }
                                    elseif (strtoupper($mod_arr['text']) == strtoupper(implode(' ', array_slice($in[3][0], 0, count(explode(' ', $mod_arr['text'])))))) {
                                        $module->in($moduleInput);
                                    }
                                    
                                }
                                else {
                                    $module->in($moduleInput);
                                }
                            }
                        
                        }
                        
                    }
                    
                    // check blocking timers for something to do
                    if ( count ( IRCCore::$timer[1] > 0 ) ) {
                    
                        foreach ( IRCCore::$timer[1] as $id => $opt ) {
                        
                            if ( $opt[0] <= time() ) {
                            
                                // if we got a list of commands, send all
                                if ( is_array ( $opt [1] ) ) {
                                
                                    foreach ( $opt[1] as $cmd ) {
                                    
                                        $this -> snd ( $cmd );
                                    
                                    }
                                    
                                }
                                else {
                                    // if its only one, send this one
                                    $this -> snd ( $opt[1] );
                                    
                                }
                                // forget timer
                                unset ( IRCCore::$timer[1][$id] );
                            
                            }
                        
                        }
                    
                    }
                    
                }
                
                else {
                
                    // if it's empty, check our timers
                    if ( count ( IRCCore::$timer[0] > 0 ) ) {
                    
                        foreach ( IRCCore::$timer[0] as $id => $opt ) {
                        
                            if ( $opt[0] <= time() ) {
                            
                                // if we got a list of commands, send all
                                if ( is_array ( $opt [1] ) ) {
                                
                                    foreach ( $opt[1] as $cmd ) {
                                    
                                        $this -> snd ( $cmd );
                                    
                                    }
                                    
                                }
                                else {
                                    // if its only one, send this one
                                    $this -> snd ( $opt[1] );
                                    
                                }
                                // forget timer
                                unset ( IRCCore::$timer[0][$id] );
                                
                            
                            }
                        
                        }
                    
                    }
                    // and http-requests
                    if ( $this -> http -> connections () > 0 ) {
                    
                        $this -> http -> check_open_requests ();
                        
                    }
                    // save cpu
                    usleep(5000);
                    
                    
                    // turn noblock off when theres nothing left to do
                    if ( count ( IRCCore::$timer[0] ) == 0  ) {
                    
                        stream_set_blocking ( IRCCore::$o_socket, true );
                        
                    }
                    
                
                }
            
            }
        
        }

        /**
         * Directly writes to the socket
         *
         * @deprecated
         * @see IRCCore::write
         * @param string $str The string to write to the socket
         * @param string $ostr The string to write to the output console (optional)
         * @param resource $socket A socket to write to
         */
        public function snd($str, $ostr = '', $socket=null) {
            $this->write($str, $ostr, $socket);
        }

        /**
         * Directly writes to the socket
         *
         * @param string $str The string to write to the socket
         * @param string $ostr The string to write to the output console (optional)
         * @param resource $socket A socket to write to
         */
        public function write($str, $ostr='', $socket=null) {
        
            // Get stream from given socket, current instance, static core or just cry
            if (!is_resource($socket)) {
                if ( is_resource ($this->socket)) {
                    $socket = $this->socket;
                } elseif (is_resource(IRCCore::$o_socket)) {
                    $socket = IRCCore::$o_socket;
                } else {
                    $socket = false;
                }
            }
        
            if (is_resource($socket)) {
            
                // create replace array
                $_replace = Array (
                    'nick' => $this->mvar('nick'),
                );
            
                // clear sendbuffer if we got lines in it
                if (count($this->send_buffer) > 0) {
                    foreach ($this->send_buffer as $str_arr) {
                        fwrite ($socket, $str_arr[0] . "\r\n");
               
                        if (!empty ($str_arr[1])) {
                            // if given, output another string than the real-send command
                            $str_arr[0] = $str_arr[1];
                        }

                        out ('(->) ' . $str_arr[0], false, 2, true );
                    
                    }
                }
                
                $str = parse_vars($str, $_replace);
        
                // write to socket
                fwrite($socket, $str . "\r\n");
               
                if (!empty($ostr)) {
                    // if given, output another string than the real-send command
                    $str = $ostr;
                }
                out ( '-> ' . $str, false, 2, true );
               
            } else {
                // Write to buffer if no stream context is available
                $this->send_buffer[] = Array($str, $ostr);
            
                if (!empty($ostr)) {
                    // if given, output another string than the real-send command (password protection)
                    $str = $ostr;
                }
            
                aout ('SENDBUFFER_NEW_ELEMENT', $str);

            }
        
        }
        
        public function disconnect ( $reason='MsTasty2 - www.dbeuchert.com/mstasty', $socket=false ) {
        
            // if we are still online
            if ( is_resource ( IRCCore::$o_socket ) ) {
        
                aout('SERVER_CONNECTION_CLOSED');
                
                if ( is_string ( $reason ) ) {
                
                    // quit, wait and
                    $this -> snd ( 'QUIT :' . $reason );
                    sleep(2);
                    
                }
                
                // close
                fclose ( IRCCore::$o_socket );
                
            }
        
        }
    
        public function load_mod ( $file ) {
        
            if ( file_exists ( MODULE_PATH . $file ) ) {
                
                // get file content and filter everything inside.
                if ( preg_match ( '/<\?php(.*?)\?>/si', file_get_contents ( MODULE_PATH . $file ), $match ) ) {
                    
                    $mod_raw = $match[1];
                
                    // get module name and replace with id (to load the same modules in runtime)
                    $mod_id = 'm_' . gen_sess_id ( 30 );
                    if (! preg_match ( '/class ([a-zA-Z0-9\-_]+)(.*){/i', $mod_raw, $match ) ) {
                    
                        // if there is no module name found, throw error
                        aout('MODULE_ERROR_NOCLASS',MODULE_PATH . $file );
                        return false;
                    
                    }
                    
                    $mod_name = trim ($match[1]);
                    $mod_com  = str_ireplace ( 'class ' . $mod_name . $match[2] . '{', 'class ' . $mod_id . $match[2] . '{', $mod_raw );
                    
                    if ( isset ( IRCCore::$mod_name2id [ $mod_name ] ) ) {
                    
                        aout('MODULE_ERROR_ALREADYGOT',$mod_name);
                        return 'already_got';
                    
                    }
                                    
                    error_reporting(E_ERROR);
                    $exec = eval ( $mod_com . 'return true;' );
                    error_reporting(E_ALL);
                    
                    if ( $exec !== true && $error = error_get_last() ) {
                    
                        aout('MODULE_ERROR_EXECFAIL',array( MODULE_PATH . $file, $error['message'], $error['line'],  $error['type'], $error['file'] ) );
                    
                    }
                    elseif ( $exec === true ) {
                    
                        IRCCore::$mod_list [ $mod_id ]             = new $mod_id( $this , $mod_id );
                        IRCCore::$mod_list [ $mod_id ] -> mod_name = $mod_name;
                        IRCCore::$mod_list [ $mod_id ] -> mod_load = time();
                        IRCCore::$mod_name2id [ $mod_name ]        = $mod_id;
                        IRCCore::$mod_file2id [ $file ]            = $mod_id;
                        
                        
                        return true;
                    
                    }
                
                }
                else {
                
                    aout('MODULE_ERROR_PARSEFAIL', MODULE_PATH . $file);
                    return false;
                    
                }

            }
            else {
            
                aout('MODULE_ERROR_READFAIL', MODULE_PATH . $file);
                return false;
                    
            }
        }
        public function unload_mod ( $file ) {
        
        
            if ( isset ( IRCCore::$mod_file2id [ $file ] ) ) {
            
                $mod_id   = IRCCore::$mod_file2id [ $file ];
                $mod_name = IRCCore::$mod_list [ $mod_id ] -> mod_name;
                
                $this -> unregister ( $mod_id );
                unset ( IRCCore::$mod_name2id [ $mod_name ] );
                unset ( IRCCore::$mod_file2id [ $file ] );
                unset ( IRCCore::$mod_list [ $mod_id ] );
                
                return true;
                
            
            }
            else {
            
                return 'err_nosuchmodule';
                
            }
        
        }
        public function register ( $commands, $text='', $silence=false, $mod_id='' ) {
        
            if ( empty ( $this -> mod_name ) ) {
            
                $mod_name = $mod_id;
                
            }
            else {
            
                $mod_name = $this -> mod_name;
                
            }
            if ( empty ( $mod_name ) ) {
            
                aout('MOD_REGISTER_INVALID_MOD');
                return false;
            
            }
        
            if ( is_string ( $commands ) ) {
        
                $commands = trim ( $commands );
                $text     = trim ( $text );
                
                if ( empty ( $commands ) ) {
                
                    aout('MOD_REGISTER_INVALID_VALUES');
                    
                }
                
            }
            else {
            
                $text = trim ( $text );
                
            }
            
            if ( !empty ( $this -> mod_id ) && empty ( $mod_id ) ) {
            
                $mod_id = $this -> mod_id;
                
            }
                
            // check if this module has registered another commands yet
            if ( !isset ( IRCCore::$mod_list [ $mod_id ] ) && empty ( $mod_id ) ) {
            
                if ( is_object ( $this ) ) {
            
                    $mod_id         = gen_sess_id ( 30 );
                    $this -> mod_id = $mod_id;
                    
                    IRCCore::$mod_list [ $this -> mod_id ] = &$this;
                    
                    if ( $silence !== false ) {
                    
                        aout('MOD_INITIALIZE_SUCCESS', $mod_name );
                        
                    }
                    
                }
            
            }
            
            if ( !is_array ( $commands ) ) {
            
                $commands = array ( $commands );
                
            }
            
            IRCCore::$id2command [ $mod_id ] = array();
            
            foreach ( $commands as $command ) {
            
                IRCCore::$id2command [ $mod_id ] [] = $command;
        
                if ( !isset ( IRCCore::$bound_commands [ $command ] ) ) {
                
                    IRCCore::$bound_commands [ $command ]    = array();
                    IRCCore::$bound_commands [ $command ] [] = array ( 'id'=>$mod_id, 'text'=>$text );

                }
                else {
                
                    IRCCore::$bound_commands [ $command ] [] = array ( 'id'=>$mod_id, 'text'=>$text );
                
                }
            
            }
        
        }
        public function unregister ( $id ) {
        
            if ( is_array ( IRCCore::$id2command [ $id ] ) ) {
            
                foreach ( IRCCore::$id2command [ $id ] as $command ) {
                
                    foreach ( IRCCore::$bound_commands [ $command ] as $key => $mod_arr ) {
                    
                        if ( $mod_arr['id'] == $id ) {

                            unset ( IRCCore::$bound_commands [ $command ] [ $key ] );
                            
                        }
                    
                    }
                    return true;
                
                }
            
            }
        
        }
        public function set_socket ( $sock ) {
        
            if ( is_resource ( $sock ) ) {
        
                IRCcore::$o_socket  = $sock;
                return true;
                
            }
            else {
            
                return false;
                
            }
            
        }
        
        static function mvar ( $varname ) {
        
            
            return IRCCore::$me[$varname];
            
        }
        protected function set_mvar ( $varname, $set_to='' ) {
        
            if ( !empty ( $set_to ) ) {
            
                IRCCore::$me[$varname] = $set_to;
            
            }
            
            return IRCCore::$me[$varname];
            
        }
        static function cvar ( $cname, $varname ) {
        
            return IRCCore::$config[$cname][$varname];
            
        }

        static function timer ( $sec, $command, $remove=false ) {
        
            // if we got no id to remove, we add a new timer
            if ( !is_string ( $remove ) ) {
        
                if ( is_array ( $command ) || !empty ( $command ) ) {
        
                    if ( $sec < 200 && $sec > 0 ) {
                    
                        $timer_id = '0_' . uniqid();
                        IRCCore::$timer[0] [ $timer_id ] = array ( time() + $sec, $command );
                        stream_set_blocking ( IRCCore::$o_socket, false );
                    
                    }
                    elseif ( $sec >= 200 && $sec < (86400*7*2) ) {
                    
                        $timer_id = '1_' . uniqid();
                        IRCCore::$timer[1] [ $timer_id ] = array ( time() + $sec, $command );
                    
                    }
                    else {
                    
                        return false;
                        
                    }
                    
                    
                    echo "\nnew timer $command\n";
                    
                    return $timer_id;
                    
                }
                else {
                
                    return false;
                    
                }
                
            }
            // if we got an id, remove it
            else {
            
                unset ( IRCCore::$timer[0] [ $remove ] );
                unset ( IRCCore::$timer[1] [ $remove ] );
                
                return true;
            
            }
        
        }
    
        // irc related sending methods
        // the real function is send_msg; the others are aliases. (but required)
        public function privmsg ( $target, $message, $opt=false, $noparse=false ) {
        
            $this -> send_msg ( 'PRIVMSG', $target, $message, $opt, $noparse );
        
        }
        public function notice ( $target, $message, $opt=false, $noparse=false  ) {
        
            $this -> send_msg ( 'NOTICE', $target, $message, $opt, $noparse );
        
        }
        public function action ( $target, $message, $opt=false, $noparse=false  ) {
        
            $this -> send_msg ( 'ACTION', $target, $message, $opt, $noparse );
        
        }
        public function ctcp ( $target, $message ) {
        
            $this -> send_msg ( 'NOTICE', $target, "\x01" . $message . "\x01", false, true );
        
        }
        public function send_msg ( $msg_type, $target, $message, $opt=false, $noparse=false  ) {
        
            if ( !empty ( $message ) ) {
            
                if (substr ( $message, 0, 1 ) == '%' && substr ( $message, -1, 1 ) == '%' ) {
                    global $lang_mng;
                
                    $message = $lang_mng -> parse_term ( substr( $message, 1, strlen( $message ) -2 ), $opt );
                    
                }
                
                $message = explode ( "\n", $message );
                
                foreach ( $message as $line ) {
                
                    if ( in_array ( $msg_type, array ( 'PRIVMSG', 'NOTICE' ) ) ) {
            
                        $this -> snd ( $msg_type . ' ' .$target . ' :' . $line );
                        
                    }
                    elseif ( $msg_type == 'ACTION' ) {
                    
                        $this -> snd ( 'PRIVMSG ' . $target . ' :' . "\x01ACTION " . $line . "\x01" );
                    
                    }
                
                }
                
            }
        
        }

        static public function getInstance() {
            return self::$obj;
        }
    
    }

}
    
?>