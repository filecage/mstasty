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
         * @param IRCCore $core
         * @param string $id
         */
        public final function __construct($core, $id) {
            $this->core = $core;
            $this->id   = $id;

            $this->initialize();
        }

        abstract protected function initialize();


        /**
         * @param string $chan
         * @param string $msg
         * @see IRCCore::privmsg
         */
        protected function privmsg ( $chan, $msg ) {
            $this->core->privmsg ( $chan, $msg );
        }

    }