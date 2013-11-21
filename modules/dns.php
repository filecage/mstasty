<?php

    class module_dnsresolver extends AbstractModule implements InteractiveModule {

        /**
         * @var Array
         */
        protected $last_dns_of = Array();

        /**
         * Usage: ?dns <ip|host>
         */
        protected function initialize() {
            $this->core->register('PRIVMSG', '?dns', true, $this->id);
        }

        /**
         * @param MessageParser $message
         * @return void
         */
        public function in ($message) {

            // No private messages or empty bodies
            if ($message->isPrivate() || count($message->getBody()) < 2) {
                return;
            }

            $channel = $message->getChannel();
            $query   = $message->getBody()[1];
        
            // if nothing is set yet, then set it
            if (!isset($this->last_dns_of[$channel])) {
                $this->last_dns_of[$channel] = 0;
            }

            // check for antispam
            if ($this->last_dns_of[$channel] < time()) {

                if (isValidIp($query)) {
                    // we need to prepare for a reverse lookup
                    $ip     = array_reverse(explode ('.', $query ));
                    $_query = implode('.', $ip) . '.in-addr.arpa';
                    $type   = DNS_PTR;
                }
                else {
                    $type   = DNS_A;
                    $_query = trim ( $query, '.' );
                }

                // get records
                $q_result = dns_get_record ( $_query, $type );


                // check if there are records
                if (is_array($q_result) && !empty($q_result)) {

                    switch ( $type ) {

                        case DNS_PTR:

                            $return = 'Target IP "' . $query . '" looked up, target host ' . $q_result[0]['target'];

                        break;

                        case DNS_A:

                            $result_ips = array();
                            $return     = 'Target host "' . $query . '" resolved, target IP ';

                            foreach ( $q_result as $target ) {

                                if ( $target ['class'] == 'IN' ) {

                                    $result_ips[] = $target['ip'];

                                }

                            }

                            $return .= implode ( ', ', $result_ips );

                        break;

                    }


                }
                else {
                    $return = 'Lookup for "' . $query . '" failed, target is unknown to me.';
                }

                if (!isset($return)) {
                    return;
                }

                // message output and set timer
                $this -> privmsg ( $channel, '[' . "\x02" . 'DNS' . "\x02" . '] ' . $return );
                $this->last_dns_of[$channel] = 15;


            }
            
        
        }
            
    }
        
        
?>