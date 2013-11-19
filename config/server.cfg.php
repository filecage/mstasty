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
    
    
    // edit the following block for any changes pointing to the server the bot will connect to
    
    $config['CNT']['host'] = '127.0.0.1';       // The host(name) the bot will open the socket to. It can be a valid IP or a hostname.
    $config['CNT']['port'] = 6667;              // The port the bot will point the socket at.
    $config['CNT']['pass'] = '';                // The password the bot sends to connect. (leave empty if no password)
    
    $config['CNT']['retries'] = 5;              // The maximum tries the bot reconnects after disconnecting and before exitting. After every retry it will wait 30 seconds longer until the next.
    $config['CNT']['bindto']  = '';             // The bindto-option lets you tell the bot if it shall bind the connection to an IP. (Can be very useful if you are using more than 1 IP)
    $config['CNT']['timeout'] = 30;             // The timeout in seconds before the connection closes (after no response from the target server)
    
?>