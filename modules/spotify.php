<?php

    // Requires MetaTune Lib (https://github.com/mikaelbr/metatune)
    // Dependencies.php is a file including all metatune libs from /lib/MetaTune/
    require_once MODULE_PATH . 'vendor/MetaTune/Dependencies.php';

    class mod_spotify extends AbstractModule implements InteractiveModule {

        public function initialize() {
            $this->register('PRIVMSG', '*');
        }

        /**
         * @param Array|MessageParser $message
         */
        public function in($message) {
            // If not public, fuck off.
            if ($message->isPrivate()) {
                return;
            }

            // Is there a spotify url in the message?
            if (!preg_match('/spotify:(album|artist|track):([a-zA-Z0-9]{22}\b)/', $message->getBodyAsText(), $matches)) {
                return;
            }

            $type = $matches[1];
            $id   = $matches[2];

            try {
                switch ($type) {
                    case 'album':
                        $album   = $this->lookupAlbum($id);
                        if (is_array($album->getArtist())) {
                            $artists = Array();
                            foreach ($album->getArtist() as $artist) {
                                $artists[] = $artist->getName();
                            }
                        } else {
                            $artists = Array($album->getArtist()->getName());
                        }
                        $return  = "\x02" . $album->getName() . "\x02" . ' by ' . implode("\x1f" . ', ' . "\x1f", $artists);
                        break;

                    case 'artist':
                        $artist = $this->lookupArtist($id);
                        $return = "\x02" . $artist->getName() . "\x02";
                        break;

                    case 'track':
                        $track  = $this->lookupTrack($id);
                        if (is_array($track->getArtist())) {
                            $artists = Array();
                            foreach ($track->getArtist() as $artist) {
                                $artists[] = $artist->getName();
                            }
                        } else {
                            $artists = Array($track->getArtist()->getName());
                        }
                        $return = "\x02" . $track->getTitle() . "\x02" . ' by ' . "\x1f" . implode("\x1f" . ', ' . "\x1f", $artists);
                        break;

                    default:
                        return;
                        break;
                }

            } catch (Exception $e) {
                $this->privmsg($message->getChannel(), 'It\'s a trap! Theres no such entity on Spotify.');
                return;
            }

            $this->privmsg($message->getChannel(), ucfirst(strtolower($type)) . ": " . $return);

        }

        /**
         * @param $id
         * @return \MetaTune\Entity\Album
         */
        protected function lookupAlbum($id) {
            return \MetaTune\MetaTune::getInstance()->lookupAlbum($id);
        }

        /**
         * @param $id
         * @return \MetaTune\Entity\Artist
         */
        protected function lookupArtist($id) {
            return \MetaTune\MetaTune::getInstance()->lookupArtist($id);
        }

        /**
         * @param $id
         * @return \MetaTune\Entity\Track
         */
        protected function lookupTrack($id) {
            return \MetaTune\MetaTune::getInstance()->lookupTrack($id);
        }


    }

?>