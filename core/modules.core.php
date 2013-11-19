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

    require_once CORE_PATH . 'abstract/Module.php';
    require_once CORE_PATH . 'interface/InteractiveModule.php';

    
    class core_modules Extends IRCCore {
    

        public function __construct () {
        
            $this -> mod_name = 'core_modules';
            $this -> register ( 'PRIVMSG','module', false );
            
            $this -> channels = &parent::$obj -> channels;
            $this -> http     = &parent::$obj->http;
        
        }
        
        public function in ( $in ) {
        
            if ( $in['text']['priv'] == true ) {
            
                if ( has_flags ( $in['sender'], $this -> cvar ( 'ADM', 'module' ) ) ) {
            
                    switch ( strtolower( $in['atext'][1] ) ) {
                    
                        case 'load':
                            if ( is_readable ( MODULE_PATH . $in['atext'][2] ) ) {
                            
                                $mod_ret = $this -> load_mod ( $in['atext'][2] );
                                
                                if ( $mod_ret === true ) {
                                
                                    $this -> notice ( $in['sender']['nick'],'%MODULE_LOAD_SUCCESS%',$in['atext'][2] );
                                    
                                }
                                elseif ( $mod_ret == 'already_got' ) {
                                
                                    $this -> notice ( $in['sender']['nick'],'%MODULE_ERROR_ALREADYGOT%',$in['atext'][2] );
                                
                                }
                                else {
                                
                                    $this -> notice ( $in['sender']['nick'],'%MODULE_LOAD_FAIL%',$in['atext'][2] );
                                
                                }
                                
                            }
                            else {
                            
                                $this->notice($in['sender']['nick'],'%MODULE_LOAD_FAIL_NONEXIST%',$in['atext'][2]);
                                
                            }
                        break;
                        
                        case 'unload':
                            
                            $mod_return = $this -> unload_mod ( $in['atext'][2] );
                            
                            if ( $mod_return === true ) {
                            
                                $this->notice($in['sender']['nick'],'%MODULE_UNLOAD_SUCCESS%',$in['atext'][2]);
                            
                            }
                            elseif ( $mod_return == 'err_nosuchmodule' ) {
                            
                                $this->notice($in['sender']['nick'],'%MODULE_UNLOAD_FAIL_NOLOAD%',$in['atext'][2]);
                            
                            }
                        break;
                        
                        case 'rehash':
                        
                            $mod_return = $this -> unload_mod ( $in['atext'][2] );
                            if ( $mod_return === true ) {
                            
                                $mod_ret = $this -> load_mod ( $in['atext'][2] );
                                
                                if ( $mod_ret === true ) {
                                
                                    $this -> notice ( $in['sender']['nick'],'%MODULE_REHASH_SUCCESS%',$in['atext'][2] );
                                    
                                }
                                elseif ( $mod_ret == 'already_got' ) {
                                
                                    $this -> notice ( $in['sender']['nick'],'%MODULE_ERROR_ALREADYGOT%',$in['atext'][2] );
                                
                                }
                                else {
                                
                                    $this -> notice ( $in['sender']['nick'],'%MODULE_LOAD_FAIL%',$in['atext'][2] );
                                
                                }
                            
                            }
                            elseif ( $mod_return == 'err_nosuchmodule' ) {
                            
                                $this->notice($in['sender']['nick'],'%MODULE_UNLOAD_FAIL_NOLOAD%',$in['atext'][2]);
                            
                            }
                        
                        break;
                        
                        case 'list':
                        
                            if ( count ( IRCCore::$mod_file2id ) > 0 ) {
                            
                                $this->notice($in['sender']['nick'],'%MODULE_LIST_INTRO%');
                                foreach ( IRCCore::$mod_file2id as $file => $id ) {
                                
                                    $this->notice ( $in['sender']['nick'], "\x02" . $file . "\x02\t\t" . date('r', IRCCore::$mod_list [ $id ] -> mod_load ) );
                                
                                }
                                $this->notice($in['sender']['nick'],'%MODULE_LIST_OUTTRO%',count ( IRCCore::$mod_file2id ) );
                                
                            }
                            else {
                            
                                $this->notice($in['sender']['nick'],'%MODULE_LIST_NOMOD%');
                                
                            }
                        break;
                        
                    }
                
                }
                else {
                
                    $this -> notice ( $in['sender']['nick'], '%COMMAND_PERMISSION_DENIED%' );
                    
                }
            
            }
        
        }
        
    }