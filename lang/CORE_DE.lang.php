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

    $translation['CORE'] = array(
        
        'LANG_NAME' => 'Deutsch',
        'LANG_INIT' => 'Initialisiere Bot auf Deutsch...',
        
        'SERVER_CONNECT' => 'Verbinde zu %0%:%1% als %2%!%3%:%4%',
        'SERVER_BINDTO'  => 'Versuche die Verbindung an die angegebene IP-Adresse %0% zu binden',
        
        'SERVER_CONNECTION_ESTABLISHED'  => 'Verbindung erfolgreich hergestellt. Anmeldungsprozess wird gestartet..',
        'SERVER_CONNECTION_FAILED'       => 'Schwerwiegender Fehler: Verbindung zu %0% über Port %1% konnte nicht hergestellt werden. Fehler: %2% (#%3%)',
        'SERVER_CONNECTION_LOST'         => 'Verbindung zum IRC-Server verloren.',
        'SERVER_CONNECTION_LOGIN'        => 'Mit %0%: %1% erfolgreich verbunden.',
        'SERVER_CONNECTION_RETRY'        => 'Versuche erneuten Verbindungsaufbau zum Zielserver.',
        'SERVER_CONNECTION_RETRYPENDING' => 'Verbindung fehlgeschlagen. Erneuter Verbindungsaufbau in %0% Sekunden.',
        'SERVER_CONNECTION_NORETRY'      => 'Verbindungsaufbau gescheitert nach %0% erneuten Versuchen.',
        'SERVER_CONNECTION_CLOSED'       => 'Verbindung geschlossen.',
        
        'SERVER_NICKNAME_FAIL'           => 'Fehler beim Anwenden des Nicknamen. Er ist möglicherweise vergeben, verboten oder fehlerhaft. Fehlernummer %0%)',
                
        'SENDBUFFER_NEW_ELEMENT' => 'Zum Sendungspuffer hinzugefügt: %0%',
        
        'SYSTEM_ADMIN_DBFAIL'       => 'Fataler Fehler: Beim Schreiben oder Lesen der Adminstratorenliste (tdb/adminlist.txt) ist ein Fehler aufgetreten. Überprüfe die Berechtigungen!',        
        'SYSTEM_UPDATE_CHECK'       => 'Überprüfe auf neue Updates...',
        'SYSTEM_UPDATE_CHECKFAIL'   => 'Die Überprüfung auf neue Updates ist fehlgeschlagen. Bitte Verbindung und Uptime vom Zielserver überprüfen!',
        'SYSTEM_UPDATE_NONEW'       => 'Keine neuen Updates gefunden, die verwendete Version scheint aktuell zu sein.',
        'SYSTEM_UPDATE_NEW'         => '*****************************************'."\n".
                                       '           *                                       *'."\n".
                                       '           *  Eine neue Bot-Version ist verfügbar  *'."\n".
                                       '           *  Informationen zum Update findest du  *'."\n".
                                       '           *    unter www.dbeuchert.com/mstasty    *'."\n".
                                       '           *                                       *'."\n".
                                       '           *****************************************',

                                       
        'ERR_CHANNELISFULL'           => 'Raum %0% ist voll.',
        'ERR_INVITEONLY'              => 'Raum %0% benötigt eine Einladung.',
        'ERR_BANNEDFROMCHAN'          => 'Der Bot ist im Raum %0% gebannt.',
        'ERR_BADKEY'                  => 'Raum %0% benötigt ein Passwort.',
        'ERR_TOOMANYCHANS'            => 'Kann %0% nicht betreten; der Bot ist bereits in zu vielen Räumen.',
                
        'MOD_REGISTER_INVALID_VALUES' => 'Ein Modul hat versucht, einen Befehl zu registrieren, sendete jedoch einen leeren oder falschen Befehl.',
        'MOD_REGISTER_INVALID_MOD'    => 'Ein Modul hat versucht, einen Befehl zu registrieren, konnte jedoch nicht identifiziert werden',
        'MOD_INITIALIZE_SUCCESS'      => 'Modul %0% wurde erfolgreich initialisiert.',
        'MODULE_ERROR_PARSEFAIL'      => 'Fehler beim Verarbeiten vom Modul %0%. Ist es echtes PHP?',
        'MODULE_ERROR_READFAIL'       => 'Befehl zum Laden von Modul %0% erhalten, es existiert aber nichts derartiges.',
        'MODULE_ERROR_EXECFAIL'       => 'Fehler beim Ausführen von %0%: %1% (%3) in der Datei [%4%] in der Zeile %2%.',
        'MODULE_ERROR_NOCLASS'        => 'Fehler beim Verarbeiten von Modul %0%: Es scheint keine valide PHP-Klasse zu sein.',
        'MODULE_ERROR_ALREADYGOT'     => 'Fehler beim Laden von Modul %0%, es ist bereits geladen. Versuche zunächst, es mit '."\x02".'/MSG %%nick%% MODULE UNLOAD %0%'."\x02".' zu entladen oder mit '."\x02".'/MSG %%nick%% MODULE REHASH %0%'."\x02".' neu zu laden.',
        'MODULE_LOAD'                 => 'Lade Modul %0%...',
        'MODULE_LOAD_FAIL_NONEXIST'   => 'Fehler: Modul %0% existiert nicht oder kann nicht gelesen werden.',
        'MODULE_LOAD_FAIL'            => 'Fehler beim Laden von Modul %0%. Überprüfe den Log bzw. die Konsolenausgabe für weitere Informationen.',
        'MODULE_LOAD_SUCCESS'         => 'Modul %0% erfolgreich geladen.',
        'MODULE_UNLOAD_SUCCESS'       => 'Modul %0% erfolgreich entladen.',
        'MODULE_REHASH_SUCCESS'       => 'Modul %0% wurde erfolgreich neu geladen.',
        'MODULE_UNLOAD_FAIL_NOLOAD'   => 'Modul %0% kann nicht entladen werden, da es nicht geladen ist.',
        'MODULE_LIST_NOMOD'           => 'Derzeit ist kein Modul geladen.',
        'MODULE_LIST_INTRO'           => 'Liste aller geladenen Module:'."\n".
                                         '-------------------------------------------------------------',
        'MODULE_LIST_OUTTRO'          => '-------------------------------------------------------------'."\n".
                                         'Geladene Module: %0%',
        
        'COMMAND_PERMISSION_DENIED'   => 'Deine Berechtigung reicht für diesen Befehl nicht aus.',
    
    );
    
    $translation['TEST'] = array(
    
        'HELLO_WORLD' => 'Hallo Welt!',
        
    );
    
    
    
?>