<?php

    class Channel {

        // Privacy constants
        const PRIVACY_PUBLIC  = '=';
        const PRIVACY_PRIVATE = '*';
        const PRIVACY_SECRET  = '@';

        // Nick delimiter constants
        const NICK_DELIMITER_OP    = '@';
        const NICK_DELIMITER_VOICE = '+';

        /**
         * @var string
         */
        protected $channel;

        /**
         * @var string
         */
        protected $privacy;

        /**
         * @var User[]
         */
        protected $users = Array();

        /**
         * @var User[]
         */
        protected $voiced = Array();

        /**
         * @var User[]
         */
        protected $ops = Array();

        /**
         * Adds a user as OP to the channel
         *
         * @param User $op
         */
        public function addOp(User $op) {
            if (!in_array($op, $this->ops)) {
                $this->ops[] = $op;
            }
        }

        /**
         * Adds a user as voiced to the channel
         *
         * @param User $voiced
         */
        public function addVoice(User $voiced) {
            if (!in_array($voiced, $this->voiced)) {
                $this->voiced[] = $voiced;
            }
        }

        /**
         * Adds a normal user to the channel
         *
         * @param User $user
         */
        public function addUser(User $user) {
            if (!in_array($user, $this->users)) {
                $this->users[] = $user;
            }
        }

        /**
         * Sets the privacy of the channel
         *
         * @param $privacy
         */
        public function setPrivacy($privacy) {
            if (in_array($privacy, Array(self::PRIVACY_PRIVATE, self::PRIVACY_PUBLIC, self::PRIVACY_SECRET))) {
                $this->privacy = $privacy;
            }
        }



    }