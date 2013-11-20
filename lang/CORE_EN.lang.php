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

    $translation['CORE'] = array(
        
        'LANG_NAME' => 'English',
        'LANG_INIT' => 'Initializing bot in English...',
        
        'SERVER_CONNECT' => 'Connecting to %0%:%1% as %2%!%3%:%4%',
        'SERVER_BINDTO'  => 'Trying to bind connection to given IP address %0%',
        
        'SERVER_CONNECTION_ESTABLISHED'  => 'Connection successfully created! Starting Log-In-Process.',
        'SERVER_CONNECTION_FAILED'       => 'Fatal error: Could not connect to %0% on port %1%. Error: %2% (#%3%)',
        'SERVER_CONNECTION_LOST'         => 'Lost connection to IRC server.',
        'SERVER_CONNECTION_LOGIN'        => 'Connection successfully established to %0%: %1%',
        'SERVER_CONNECTION_RETRY'        => 'Trying to reconnect to IRC server.',
        'SERVER_CONNECTION_RETRYPENDING' => 'Connection failed. Reconnect in %0% seconds.',
        'SERVER_CONNECTION_NORETRY'      => 'Connection failed after %0% retries.',
        'SERVER_CONNECTION_CLOSED'       => 'Connection closed.',
        
        'SERVER_NICKNAME_FAIL'           => 'Error while assuming nickname; seems already being used, forbidden or invalid. (errorno %0%)',
        
        'SENDBUFFER_NEW_ELEMENT' => 'Added to sendbuffer: %0%',
        
        'SYSTEM_ADMIN_DBFAIL'       => 'Fatal error: Could not find, read or write to the admin file (tdb/adminlist.txt). Please set the right chmod and try again.',
        'SYSTEM_UPDATE_CHECK'       => 'Checking for new updates...',
        'SYSTEM_UPDATE_CHECKFAIL'   => 'Checking for updates failed. Check internet connection or uptime of mirror!',
        'SYSTEM_UPDATE_NONEW'       => 'No new updates found. Your version seems to be the newest.',
        'SYSTEM_UPDATE_NEW'         => '*****************************************'."\n".
                                       '           *                                       *'."\n".
                                       '           *  There is a new bot update available  *'."\n".
                                       '           * To get more information about changes *'."\n".
                                       '           *    visit www.dbeuchert.com/mstasty    *'."\n".
                                       '           *                                       *'."\n".
                                       '           *****************************************',
                                       
        
        
        
        
        
        'ERR_CHANNELISFULL'           => 'Channel %0% is full.',
        'ERR_INVITEONLY'              => 'Channel %0% requires an invite.',
        'ERR_BANNEDFROMCHAN'          => 'The bot is banned from channel %0%.',
        'ERR_BADKEY'                  => 'Channel %0% requires keyword.',
        'ERR_TOOMANYCHANS'            => 'Cannot join %0%; bot already joined too many chans.',
        
        'MOD_REGISTER_INVALID_VALUES' => 'A module tried to register at the core but sent an empty or invalid command.',
        'MOD_REGISTER_INVALID_MOD'    => 'A module tried to register at the core but it could not be identified.',
        'MOD_INITIALIZE_SUCCESS'      => 'Module %0% has been successfully initialized.',
        'MODULE_ERROR_PARSEFAIL'      => 'Error parsing module %0%: is it true PHP?',
        'MODULE_ERROR_READFAIL'       => 'Got instruction to load module %0%, but it doesn\'t exist.',
        'MODULE_ERROR_EXECFAIL'       => 'Error while executing %0%: %1% (%3%) in file [%4%] on line %2%.',
        'MODULE_ERROR_ALREADYGOT'     => 'Error while loading module %0%, it\'s already loaded.',
        'MODULE_ERROR_NOCLASS'        => 'Error parsing module %0%, file does not match module formation rules',
        'MODULE_LOAD'                 => 'Loading module %0%...',
        'MODULE_LOAD_FAIL_NONEXIST'   => 'Error: Module %0% does not exist or can not be loaded.',
        'MODULE_LOAD_FAIL'            => 'Error while loading module %0%. Check the log or the console output for more information.',
        'MODULE_LOAD_SUCCESS'         => 'Module %0% successfully loaded.',
        'MODULE_UNLOAD_SUCCESS'       => 'Module %0% successfully unloaded.',
        'MODULE_REHASH_SUCCESS'       => 'Module %0% successfully rehashed.',
        'MODULE_UNLOAD_FAIL_NOLOAD'   => 'Module %0% can not be unloaded; there is no such module loaded.',
        'MODULE_LIST_NOMOD'           => 'There is no module loaded.',
        'MODULE_LIST_INTRO'           => 'Loaded modules:'."\n".
                                         '-------------------------------------------------------------',
        'MODULE_LIST_OUTTRO'          => '-------------------------------------------------------------'."\n".
                                         'Total: %0%',
        
        'COMMAND_PERMISSION_DENIED'   => 'One does not simply admin into MsTasty.',
        'COMMAND_UNKNOWN'             => 'So, tell me what you want, what you really really want (because I don\'t know that command)',
    
    );
    
    
            
    $translation['TEST'] = array(
    
        'HELLO_WORLD' => 'Hello World!',
        
    );
    
    
    
    
?>