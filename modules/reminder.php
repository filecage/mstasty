<?php

    class mod_reminder extends AbstractModule implements InteractiveModule {

        public function initialize() {
            $this->register('PRIVMSG', 'remind*');
        }

        public function in($message) {

            // Regex the interval and the action
            if (!preg_match('/remind me (in )?(.+) to (.+)/i', $message->getBodyAsText(), $match)) {
                return;
            }

            $interval = $match[2];
            $action   = trim(str_ireplace('my', 'your', $match[3]));
            $nick     = $message->getUser()->getNick();
            $sender   = $message->getSender();

            // Determine the target time
            $intervalObj = DateInterval::createFromDateString($interval);

            // Try another way if createFromDateString didn't work
            if ($this->getIntervalSeconds($intervalObj) < 1) {
                try {
                    $intervalObj = new DateInterval($interval);
                } catch (Exception $e) {
                    $this->core->notice($nick, 'Sorry, your interval format is invalid');
                    return;
                }
            } elseif ($this->getIntervalSeconds($intervalObj) < 30) {
                $this->core->notice($nick, 'You can\'t be that forgetful! Try a reminder in at least 30 seconds.');
                return;
            }

            $now    = new DateTime();
            $remind = $now->add($intervalObj);

            $this->core->privmsg($sender, 'Okay ' . $nick . ', I will remind you to ' . $action . ' on ' . $remind->format('l, jS F Y H:i'));
            $this->core->timer($this->getIntervalSeconds($intervalObj), 'PRIVMSG ' . $sender . ' :' . $nick . ', you asked me to remind you to ' . $action . '!');

        }

        /**
         * Returns an DateInterval in seconds
         *
         * @param DateInterval $delta
         * @return int
         */
        protected function getIntervalSeconds($delta) {
            $seconds = ($delta->s)
                + ($delta->i * 60)
                + ($delta->h * 60 * 60)
                + ($delta->d * 60 * 60 * 24)
                + ($delta->m * 60 * 60 * 24 * 30)
                + ($delta->y * 60 * 60 * 24 * 365);

            return $seconds;
        }

    }

?>