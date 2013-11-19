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
    
    
    class core_channels Extends IRCCore {
    
        private $chanlist;
    
    
        public function __construct () {
        
            $this -> chanlist   = array();
            $this -> num_to_err = array(
                '405' => 'ERR_TOOMANYCHANS',
                '471' => 'ERR_CHANNELISFULL',
                '473' => 'ERR_INVITEONLY',
                '474' => 'ERR_BANNEDFROMCHAN',
                '475' => 'ERR_BADKEY',
            );
        
            $this -> mod_name = 'core_channels';
            
            // register join numerics
            $this -> register ( 'JOIN','',false );
            $this -> register ( '471','',false );
            $this -> register ( '473','',false );
            $this -> register ( '474','',false );
            $this -> register ( '475','',false );
            
            // register part numerics
            $this -> register ( 'KICK','',false );
            //$this -> register ( '','',false );
        
        }
        
        public function join_all ( ) {
        
            $chans = file ( ROOT_PATH . 'tdb/chanlist.txt' );
            foreach ( $chans as $chan ) {
            
                $this -> join ( $chan );
                
            }
        
        }
        
        public function join ( $chan ) {
        
            $this -> snd ( 'JOIN ' . $chan );
        
        }
        public function part ( $chan ) {
        
            $this -> snd ( 'PART ' . $chan );
        
        }
        
        public function in ( $in ) {
        
            switch ( $in [ 1 ] ) {
            
                case 'JOIN':
                    if ( $in [0][0] == $this -> mvar('nick') ) {
                        $this -> chanlist [ $in [3][0][0] ] = true;
                    }
                break;
                case '471':
                case '473':
                case '474':
                case '475':
                case '405':
                    aout( $this -> num_to_err [ $in [1] ], $in [2][1] ); 
                break;
                
                case 'KICK':
                    if ( $in [2][1] == $this -> mvar('nick') ) {
                        unset ( $this -> chanlist [ $in [2][0] ] );
                        
                        if ( $this -> cvar ( 'BOT', 'rejoin' ) !== false ) {
                        
                            if ( is_numeric ( $this -> cvar ( 'BOT', 'rejoin' ) ) ) {
                            
                                $this -> timer ( $this -> cvar ( 'BOT', 'rejoin' ), 'JOIN ' . $in[2][0] );
                                
                            }
                            else {
                            
                                $this -> join ( $in [2] [0] );
                                
                            }
                            
                        }
                    }
                break;
                
            }
        
        }
        
    }