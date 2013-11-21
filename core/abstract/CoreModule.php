<?php

    abstract class CoreModule {

        /**
         * Module name - generated randomly and unique
         * @var string
         */
        public $mod_name;

        /**
         * Instance of IRCCore
         * @var IRCCore
         */
        protected $core;

        /**
         * An array of commands that should be registered to this module
         * @var Array
         */
        protected $registerCommands = Array();

        /**
         * An array of messages (PRIVMSGs) that should be registered to this module
         * @var array
         */
        protected $registerMessages = Array();

        /**
         * Gets an instance of core and registers all necessary commands/privmsgs
         *
         * @throws Exception
         * @constructor
         */
        public function __construct() {
           $this->core = IRCCore::getInstance();
            if (!$this->core instanceof IRCCore) {
                throw new Exception('Cannot create instance of Core module before core has been initialized');
            }

            foreach($this->registerCommands as $command) {
                $this->core->register($command);
            }

            foreach ($this->registerMessages as $message) {
                $this->core->register('PRIVMSG', $message);
            }
        }

    }