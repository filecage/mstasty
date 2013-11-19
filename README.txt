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


============================================
1.  Long list of unstructured documentations
1.1    More unstructured documentations explaining the module framework
1.2    Lot of unstructured documentations explaining global available functions

2.  The http handler
============================================




====================================
To make someone admin, add the nick!ident@host to the tdb/adminlist.txt in format
nick!ident@host=user_flags (user_flags are default set to mo)
You can use wildcards. Entry is ignored if not in format mask=flags
====================================

====================================
To make the bot joining any channels, write them down in the tdb/chanlist.txt
One channel per line.
====================================

As there is currently no online help or something else, I'm just going to explain the BASIC functions of the bot.
The only command working yet is the MODULE command, used by /MSG <Botnick> MODULE (LOAD|UNLOAD|REHASH|LIST) <Modulefile>
Modulefile is the filename like it's called in the directory /modules. DO NOT (!) USE THE COMMAND WITH DIRECTORIES!
The bot will search for the script in the modules directory AUTOMATICALLY.

Writing a module is easy. To see the basics, just open the test.module.php in the modules directory.

Notice: All variables written in [ ] are optional.

In constructor: register command by $core -> register ( string($command), [string($text)], [bool($silenced)], [string($mod_id)] )

$command    is the command, like JOIN, KICK, PRIVMSG or a numeric.
$text       if $command is PRIVMSG, you can tell the bot to only send in commands that use a trigger, e.g. !foo or something like that.
            leave empty if you are not using a PRIVMSG (the bot will ignore it either way) (Leave it empty doesn't mean DON'T give the variable; it means an empty string.)
$silenced   the bot gives an echo to the console when a new module is loaded by it's first registered event. Only required for the core, so it just needs to be false. (which is default)
$mod_id     if you are registering the event in your object constructor, you need to send your mod ID (which is given to you as second argument when calling the constructor; remember this!)
            if you are registering any commands later, than you can only use $core -> register ( $command ).
return: true if register was successfull, false if not
            

Function called by the core which NEEDS to be in your object; otherwise the bot will crash by fatal (no such function)
$your_module -> in ( $in )

$in         is the output given to you by the IRC parser. It's actually the following array:
array (
        sender  => array (
                          nick   => string ( SENDER_NICK ),
                          ident  => string ( SENDER_IDENT ),
                          host   => string ( SENDER_HOST ),
                          full   => string ( SENDER_FULL ),
                          prefix => string ( SENDER_IDENT_PREFIX ),
        )
        command => string ( COMMAND )
        args    => array ( ARGUMENTS )
        atext   => array ( WORD1, WORD2, etc. ),
        text    => array (        
                          full  => string ( FULL TEXT )
                          priv  => bool ( IS_PRIVATE_MESSAGE )
        )
        raw  => string ( RAW LINE )
)
We first had all those informations sorted by a numeric key. This is also still implemented but we don't recommend to use it. We will delete it in later versions.


Thats all you need in your object.
You can use the following methods to improve your module.

    $core -> ( str($send), [str($a_send)], [resource($socket)] )
    
    $send       the string you'd like to send to the server (the \r\n comes automatically, don't put it to your string: it'll be removed)
    $a_send     an alternative line you would like the bot to echo to the console. Usefull for password auths (so the bot owner can't see, what another user really sent)
    $socket     the use of this variable is not recommended, but it provides the method to send to an alternative stream.
    return: nothing
    
    $core -> privmsg / $core -> action / $core -> notice / $core -> ctcp ( $target, $message, [$lang_opt], [$noparse] )
    
    those methods all do (almost) the same, so they are only explained once.
        
    $target     the message target; either a channel or a nick.
    $message    the message to be sent. if the string given is packed in %, the langmanager will try to find a translation for that. (e.g. %FOO% results in bar.)
                you will have to add your own languages to the language file used. In the alpha version, there is no multilanguage and adding new language packs while connected is a feature
                which will probably take a few weeks more (but it will surely come.)
    $lang_opt   [NOT FOR CTCP!] if you use language variables in your language file, you can tell the langengine to replace them. If you only use one, you can use a string, otherwise an array.
    $noparse    [NOT FOR CTCP!] it maybe occurs that you want to sent a plain text (not translated by the lang engine) which starts with a % and ends with it. Set this variable to true, so the
                                lang engine will not be asked for this one. (e.g. %FOO% stays %FOO% if set to true.)
    return: nothing                            
    
                                
    
    $core -> channels -> join / $core -> channels -> part ( str($channel) )
    
        Nothing to explain here. 
        You can (if you want) also use the $core -> snd() to join/part a channel, but parts should be made by this object so that the bot knows in which channels it is ideling.
        return: nothing. possible errors will be shown in the console output.
        
        
    $core -> mvar ( str($varname) )
    
        $varname        can be any key of the core::$me variable (currently only supports nick)
        return: string
        
        
    $core -> cvar ( str($class), str($var) )
    
        $class      the config class. if you edited the config files yet, you may have recognized that they are categorized. e.g. BOT means the bot itself or CNT the connection variables
        $var        the variable name of the $class. same here: if you have edited the files yet, you recognized the principle.
        return: string
        
        
    $core -> timer ( int($sec), str($command), [str($remove)] )
    
        $sec        the delay for the send of $command in seconds
        $command    the command which should be sent after $sec is over
        $remove     if given, the ID which was returned by an earlier call of $core -> timer(); will deactivate a running (and not-yet-executed) timer
        return: an unique id for the timer to stop it
    
    
    
    The following commands are provided by the functions.lib.php. Some are not explained because their names should tell you all you need.
    They are not part of an object so you can call them easily.
    
    out / aout ( [str($message)], [str($lang_opt)], [bool($scream)], [bool($clean)] )
    $message    a message or language object.
    $lang_opt   see line 86
    $scream     if set to true, $message will be echoed even if output is set to 0. (use only for fatal errors)
    $clean      if set to true, $message will not be treated as language object and outputted as plain (similar to $noparse from line 87)
    
    
    
    isValidEmail ( str($mail), [str($type)] )
    $type must be either ereg (only check format), dns (check if theres an mx) or both
    return: true if email is valid
    
    gen_session_id ( [int($len)] )
    return: string
    
    duration ( int($timestamp_start), int($timestamp_stop) )
    return: string [ 1 week, 1 day, 1 hour, 1 minute, 1 second ]
    
    durationApprox ( int($sec) ) [NEEDS TO BE TRANSLATED MANUALLY!; line 396]
    return: string
    
    is_utf8 ( $string )
    return: bool
    
    num2word ( int($num_between_0_and_12) ) [LANGUAGE CAN BE CHANGED; line 516]
    return: string
    
    isinIpRange ( str($ip), str($iprange) ) [IPRANGE MUST BE LIKE 8.8.0.0 TO MATCH 8.8.4.2]
    return: bool
    
    filter_html ( str($source), [array($allowed_tags)] )
    return: string
    
    readEqualDb ( str($file) )
    reads $file and parses values seperated by = (e.g. foo=bar is returned as $ret['foo'] = 'bar')
    return: array
    
    has_flags ( str($usermask), str($flags) )
    $usermask   the mask of the user in format nick!user@host OR (which is better because it also matches if the user gots a ~ prefix) send the whole $in['sender']-array.
    $flags      the flags (mstasty2 only supports one-char flags), if more than 1 given, it will check everyone.
    return: bool
    
    get_rights ( str($usermask) )
    $usermask   see line 163
    return: string
    
    does_match ( str($usermask), str($haystack_mask) )
    $usermask           see line 163
    $haystack_mask      a mask that shall be matched. e.g. if haystack is *!*@*.org, all $usermask which end with .org will return true
                        * means a wildcard for everything
                        ? means only one sign; e.g. dbeuchert.com will match dbeuchert?com
    return: bool



======================================
2. The HTTP-Handler
======================================
One of the biggest problems PHP has is, probably, that it doesn't support multithreading.
When making a HTTP request (for an rss reader for example) the whole bot is in block mode.
It won't be able to answer PONG to PING and, in the worst case, if a site is unavailable, the bot will ping timeout.
To Avoid that problem, we had an idea. You can handle different streams in one process by making them noblock.
So we decided: If someone wants to make a http-request (for a module or whatever), we are opening a new stream and
using our own http handler. We can use all streams in noblock and the bot will be able to handle lots of requests without
being blocked. However, we haven't implemented this yet (it might take a while) but we implemented the structure and so you
can use the current methods for your module and we'll replace them in later versions.

We are explaining what to send, where to send it and where to receive what.

Syntax: 
        $core -> http -> request ( 
	                           object(&$modul),
                                   string($url),
                                   [array($post_data)]
                                 )
Return: string

This will create a new http requests and return an ID. In your receive method, you will get that ID back and have to identify where to send the http response.
$modul can be $this (or better said: it has to be $this, otherwise your receive methode won't be called)
$url is the url where to send the http request
$post_data is an array with post data you'd like to sent (not implemented yet, will come in later versions)


Syntax:
       $module -> http_in ( 
                            string($id), 
                            string($content)
                            array($http_head)
                          )
Return: nothing

This is your receive method.
$id is the ID you got back when calling $core -> http -> request()
$content is the content sent by the server
$http_head is an array with the http-response header (required for you to handle 4xx)



That's it! Have fun using this bot. If you have any questions, feel free to mail to mail [the sign which means at] dbeuchert.com
        
        
        
        
        
        
        
        
        
        
        
                                    
        
        
       
    
    