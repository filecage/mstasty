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

        /**
         * Matches a user identifier mask
         * @param $mask
         * @param $haystack
         * @return bool
         */
        public static function doesMaskMatch($mask, $haystack) {
            if ($mask == $haystack) {
                return true;
            }

            // the simple one doesn't match? no problem, maybe it matches with a simple wildcard
            $nick  = substr($mask, 0, strpos($mask, '!'));
            $host  = substr($mask, (strpos($mask, '@') + 1));
            $ident = str_replace('@' . $host, '', substr($mask, (strpos($mask, '!') + 1)));

            $h_nick  = substr($haystack, 0, strpos($haystack, '!'));
            $h_host  = substr($haystack, (strpos($haystack, '@') + 1));
            $h_ident = str_replace('@' . $h_host, '', substr($haystack, (strpos($haystack, '!') + 1)));

            if ($nick == $h_nick || $nick == '*' || $h_nick == '*') {
                if ($host == $h_host || $host == '*' || $h_host == '*') {
                    if ($ident == $h_ident || $ident == '*' || $h_ident == '*') {
                        return true;
                    }
                }
            }

            // still no match? try regex and complex wildcards.
            $h_nick  = str_replace('*', '[^!]+', $h_nick);
            $h_nick  = str_replace('?', '(\S{1})', $h_nick);
            $h_ident = str_replace('*', '[^@]+', $h_ident);
            $h_ident = str_replace('?', '(\S{1})', $h_ident);
            $h_host  = str_replace('*', '(.*)', $h_host);
            $h_host  = str_replace('?', '(\S{1})', $h_host);

            if (preg_match('/' . $h_nick . '!' . $h_ident . '@' . $h_host . '/', $mask)) {
                return true;
            }

            // if theres still no match, there is nothing to match. so return false.
            return false;

        }

    }