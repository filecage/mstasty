<?php

    abstract class CoreModule {

        /**
         * Module name - generated randomly and unique
         * @var string
         */
        public $mod_name;

        /**
         * Module id
         * @var string
         */
        public $mod_id;

        /**
         * Instance of IRCCore
         * @var IRCCore
         */
        protected $core;

        /**
         * Gets an instance of core and registers all necessary commands/privmsgs
         *
         * @throws Exception
         * @constructor
         */
        public function __construct() {
            $this->core     = IRCCore::getInstance();
            $this->mod_name = uniqid('core-mod_');

            if (!$this->core instanceof IRCCore) {
                throw new Exception('Cannot create instance of Core module before core has been initialized');
            }
        }

        /**
         * Wrapper method for registering commands
         *
         * @param string $command
         * @param string $arguments
         * @param bool $silence
         * @see IRCCore::register
         */
        protected function register($command, $arguments = '', $silence = false) {
            $this->core->register((string) $command, $arguments, $silence, $this->mod_id, $this);
        }

        /**
         * @param MessageParser $message
         */
        abstract public function in(MessageParser $message);

    }