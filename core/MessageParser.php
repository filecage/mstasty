<?php

    /**
     * Class MessageParser
     * Required for parsing an incoming message
     *
     * @package Core
     */

    class MessageParser {

        /**
         * The raw input string
         * @var string
         */
        protected $raw;

        /**
         * The sending user
         * @var User
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

            if ($raw_src[0] == ':') {
                // if the command is prefixed with :, delete this char and store it's meaning
                $raw      = substr(trim($raw_src), 1);
                $not_real = false;
            }
            else {
                $raw      = trim($raw_src);
                $not_real = true;
            }

            // if the string is empty, return PARSE_EMPTY_STRING (important to save CPU when in noblock-mode)
            if (empty ($raw)) {
                return 'PARSE_EMPTY_STRING';
            }

            // explode by whitespace for better parsing
            $raw_arr = explode(' ', $raw);

            // check if incoming is sent by command or a ping
            if ($not_real === true) {
                $raw_arr    = array_merge(Array (0 => NULL), $raw_arr);
                $this->user = new User();
            } else {
                $this->user = new User($raw_arr[0]);
            }

            // get command
            $command = $raw_arr[1];

            // get arguments (all between command and the : which introduces text)
            $arguments = explode (' ', trim(substr(implode(' ', array_slice($raw_arr, 2 )), 0, strpos(implode(' ', array_slice($raw_arr, 2 )), ':'))));
            if (empty($arguments[0])) {
                $this->arguments = array();
            } else {
                $this->arguments = $arguments;
            }

            // and get the text
            $text    = array_slice($raw_arr, (2 + count($arguments)));
            $text[0] = substr($text[0], 1);

            if ($command == 'PRIVMSG' && strtolower ($arguments[0]) == strtolower ($this->mvar('nick'))) {
                $this->isPrivate = true;
            } else {
                $this->isPrivate = false;
            }

            $this->command = $command;
            $this->body    = $text;

        }
    }