<?php

    /**
     * Class User
     * Parses and holds information about a user
     *
     * @package Core
     */

    class User {

        /**
         * The raw user mask
         * @var string
         */
        protected $mask;

        /**
         * The nick of the user
         * @var string
         */
        protected $nick;

        /**
         * The ident of the user
         * @var string
         */
        protected $ident;

        /**
         * The host of the user
         * @var string
         */
        protected $host;

        /**
         * The identifier prefix
         * @var string
         */
        protected $prefix;

        /**
         * Whether this user is the server (i.e. ping or something) or a user
         * @var bool
         */
        protected $isUser = true;

        /**
         * Whether this user is the bot itself
         * @var bool
         */
        protected $isSelf = false;

        /**
         * Parses the given raw identifier
         *
         * @param string $mask
         */
        public function __construct($mask = '') {
            if (!empty($mask)) {
                $this->parseMask($mask);
            }
        }

        /**
         * Raw parser
         *
         * @param string $mask
         * @return $this
         */
        public function parseMask($mask) {
            $sender     = $mask;
            $sender_arr = Array();

            // check if sender is a client or a server
            if (strstr($sender, '!') !== false && strstr ($sender, '@') !== false) {
                $sender_arr[0] = substr($sender, 0, strpos($sender, '!'));
                $sender_arr[2] = substr($sender, (strpos( $sender, '@') + 1));
                $sender_arr[1] = str_replace ('@' . $sender_arr[2], '', substr ($sender, (strpos ($sender, '!') + 1)));

                // Prefixed isn't really important. Store it anyways.
                if ($sender_arr[1][0] == '~') {
                    $sender_arr[1] = substr ( $sender_arr[1], 1 );
                    $sender_arr[4] = '~';
                } else {
                    $sender_arr[4] = '';
                }
            } else {
                $this->isUser  = false;
                $sender_arr[0] = $sender;
                $sender_arr[1] = $sender;
                $sender_arr[2] = $sender;
                $sender_arr[4] = '';
            }

            $this->mask   = $mask;
            $this->nick   = $sender_arr[0];
            $this->ident  = $sender_arr[1];
            $this->host   = $sender_arr[2];
            $this->prefix = $sender_arr[4];

            if ($this->nick == IRCCore::mvar('nick')) {
                $this->isSelf = true;
            } else {
                $this->isSelf = false;
            }

            return $this;
        }

        /**
         * @param string $nick
         */
        public function setNick($nick) {
            $this->nick = $nick;
        }

        /**
         * @return string
         */
        public function getHost() {
            return $this->host;
        }

        /**
         * @return string
         */
        public function getIdent() {
            return $this->ident;
        }

        /**
         * @return boolean
         */
        public function getIsSelf() {
            return $this->isSelf;
        }

        /**
         * @return string
         */
        public function getNick() {
            return $this->nick;
        }

        /**
         * @return string
         */
        public function getPrefix() {
            return $this->prefix;
        }

        /**
         * @return string
         */
        public function getMask() {
            return $this->mask;
        }



    }