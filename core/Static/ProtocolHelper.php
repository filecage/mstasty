<?php

    class ProtocolHelper {

        /**
         * Returns whether a channel is valid by RFC or not
         *
         * @param string $channel
         * @return int
         */
        public static function isChannel($channel) {
            return preg_match('/([#&][^\x07\x2C\s]{0,200})/', $channel);
        }

    }