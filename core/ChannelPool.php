<?php

    class ChannelPool extends CoreModule {

        const RPL_NAMREPLY   = 353;
        const RPL_ENDOFNAMES = 366;

        /**
         * Current active instance
         * @var $this
         */
        static protected $instance;

        /**
         * List of channel objects
         * @var Channel[]
         */
        protected $channels = Array();

        /**
         * An array keeping channel information while the pool is still waiting for more data
         * @var Channel[]
         */
        protected $temporaryChannels = Array();

        /**
         * @throws Exception
         */
        public function __construct() {
            if (!is_null(self::$instance)) {
                throw new Exception('Cannot create second instance of ChannelPool, class is supposed to be singleton');
            }

            parent::__construct();

            $this->register(self::RPL_NAMREPLY);
            $this->register(self::RPL_ENDOFNAMES);
        }

        /**
         * @return $this
         */
        static public function getInstance() {
           if (is_null(self::$instance)) {
               self::$instance = new self;
           }

            return self::$instance;
        }

        /**
         * @param MessageParser $message
         */
        public function in(MessageParser $message) {

            var_dump($message);

            switch ($message->getCommand()) {

                case self::RPL_NAMREPLY:
                    $this->incomingNamesReply($message);
                    break;

                case self::RPL_ENDOFNAMES:
                    $this->storeNamesReply($message);
                    break;

            }

        }

        protected function incomingNamesReply(MessageParser $message) {
            if (!isset($this->temporaryChannels[$message->getChannel()])) {
                $this->temporaryChannels[$message->getChannel()] = new Channel();
            }

            $channel = $this->temporaryChannels[$message->getChannel()];
            $privacy = ($message->getArguments()[0] != $message->getChannel()) ? $message->getArguments() : Channel::PRIVACY_PUBLIC;
            $channel->setPrivacy($privacy);

            foreach ($message->getBody() as $nick) {
                $delimiter = substr($nick, 0, 1);
                $nick      = str_replace(Array('@', '+'), '', $nick);

                switch ($delimiter) {
                    case Channel::NICK_DELIMITER_OP:
                        $channel->addOp(new User($nick));
                        break;

                    case Channel::NICK_DELIMITER_VOICE:
                        $channel->addVoice(new User($nick));
                        break;

                    default:
                        $channel->addUser(new User($nick));
                        break;
                }
            }
        }

        /**
         * Called when RPL_ENDOFNAMES is incoming
         * @param MessageParser $message
         */
        protected function storeNamesReply(MessageParser $message) {
            if (isset($this->temporaryChannels[$message->getChannel()])) {
                $this->channels[$message->getChannel()] = $this->temporaryChannels[$message->getChannel()];

                var_dump($this->channels[$message->getChannel()]);


                unset($this->temporaryChannels[$message->getChannel()]);
            }
        }

        /**
         * Returns a channel object from pool
         *
         * @param string $channel
         * @return bool|Channel
         */
        public function getChannel($channel) {
            return (isset($this->channels[$channel])) ? $this->channels[$channel] : false;
        }


    }