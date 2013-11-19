<?php

    class MessageParser {

        /**
         * The raw input string
         * @var string
         */
        protected $raw;

        /**
         * The sending user
         * @var Array
         */
        protected $user;

        /**
         * The command which has been dispatched
         * @var string
         */
        protected $command = '';

        /**
         * An array of arguments given to the command
         * @var Array
         */
        protected $arguments;

        /**
         * The command body given
         * @var Array
         */
        protected $body = Array();

        /**
         * Whether the message was a private message or not
         * @var bool
         */
        protected $isPrivate = false;


        /**
         * Parses the message
         *
         * @param $raw_src
         */
        public function __construct($raw_src) {

            $this->raw = $raw_src;

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
            if (empty ($raw)) {
                return 'PARSE_EMPTY_STRING';
            }

            // explode by whitespace for better parsing
            $raw_arr = explode ( ' ', $raw );

            // first parse sender
            $sender     = $raw_arr[0];
            $sender_arr = Array();

            // check if incoming is sent by command or a ping
            if ($not_real === true) {
                $raw_arr    = array_merge(Array (0 => NULL), $raw_arr);
                $sender_arr = Array(0 => NULL, 1 => NULL, 2 => NULL, 3 => NULL, 4 => NULL);
            }


            // check if sender is a client or a server
            elseif (strstr($sender, '!') !== false && strstr ($sender, '@') !== false) {
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
            } else {
                $sender_arr[0] = $sender;
                $sender_arr[1] = $sender;
                $sender_arr[2] = $sender;
                $sender_arr[4] = '';
            }

            $sender_arr[3] = $raw_arr[0];

            // get command
            $command = $raw_arr[1];

            // get arguments (all between command and the : which introduces text)
            $arguments = explode (' ', trim(substr(implode(' ', array_slice($raw_arr, 2 )), 0, strpos(implode(' ', array_slice($raw_arr, 2 )), ':'))));
            if (empty($arguments[0])) {
                $arguments = array();
            }

            // and get the text
            $text    = array_slice($raw_arr, (2 + count($arguments)));
            $text[0] = substr($text[0], 1);

            if ($command == 'PRIVMSG' && strtolower ($arguments[0]) == strtolower ($this->mvar('nick'))) {
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