<?php

    error_reporting ( E_ALL );

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
    
    echo "\nMsTasty 2 (C) dbeuchert.com\nwww.dbeuchert.com/mstasty\n";
    
    set_time_limit(0);
    
    // define pathes
    
    define ( 'ROOT_PATH',    str_replace ( '\\', '/' ,  dirname(__FILE__)=='/'?'':dirname(__FILE__).'/' ) );
    
    define ( 'CONFIG_PATH',  ROOT_PATH . 'config/'  );
    define ( 'LANG_PATH',    ROOT_PATH . 'lang/'    );
    define ( 'MODULE_PATH',  ROOT_PATH . 'modules/' );
    define ( 'CORE_PATH',    ROOT_PATH . 'core/'    );
    
    
    // require important functions
    require_once( 'includes/functions.lib.php' );
    require_once( 'includes/langmanager.class.php' );
    require_once( 'core/main.core.php' );
    
    // require config files
    require_once( 'config/bot.cfg.php' );
    require_once( 'config/server.cfg.php' );
    require_once( 'config/authority.cfg.php' );
    require_once( 'config/http.cfg.php' );
    
    // init langmanager
    $lang_mng = new LangManager ( $config [ 'BOT' ] [ 'default_lang' ] );
    
    // init core
    $core     = new IRCcore ( $config );
    
    // start connection
    $core -> connect -> to_main();
    $core -> socket     = $core -> connect -> return_res();
    $core -> set_socket ( $core -> socket );
    $core -> channels -> join_all ();
    
    // load modules (req_ocon)
    require_once ( 'config/modload.cfg.php' );
    
    // keep alive
    $core->keepup();
    
    echo "\n";
    
?>