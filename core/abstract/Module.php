<?php

    abstract class AbstractModule {

        /**
         * @var IRCCore
         */
        protected $core;

        /**
         * The current instance module id
         * @var string
         */
        protected $id;

        /**
         * Mod name (will be set to class name automatically)
         * @var string
         */
        public $mod_name;

        /**
         * @param IRCCore $core
         * @param string $id
         */
        public final function __construct($core, $id) {
            $this->core     = $core;
            $this->id       = $id;
            $this->mod_name = $id;

            $this->initialize();
        }

        abstract protected function initialize();

        /**
         * @param MessageParser|Array $message
         * @return void
         */
        abstract public function in($message);


        /**
         * @param string $chan
         * @param string $msg
         * @see IRCCore::privmsg
         */
        protected function privmsg ($chan, $msg) {
            $this->core->privmsg($chan, $msg);
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
            $this->core->register($command, $arguments, $silence, $this->id, $this);
        }

    }