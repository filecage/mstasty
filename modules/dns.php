<?php

    class module_dnsresolver {
    
    
        function __construct (&$core,$id) {
        
            $this -> core = &$core;
            $this -> id   = $id;
                
            $core -> register ( 'PRIVMSG', '?dns', true, $id );
            
            // introduce antispam array
            $this -> last_dns_of = array();
            
        
        }
        
        function in ( $in ) {
        
            // if nothing is set yet, then set it
            if ( !isset ( $this ->last_dns_of [ $in['args'][0] ] ) ) {
            
                $this -> last_dns_of [$in['args'][0]] = 0;
                
            }
            
            
            // check for antispam
            if ( $this -> last_dns_of [$in['args'][0]] < time() ) {
            
                // check if we don't get messed up
                if ( isset ( $in['atext'][1] ) ) {
            
                    $query = $in['atext'][1];
                    
                    if ( isValidIp ( $query ) ) {
                    
                        // we need to prepare for a reverse lookup
                        $ip     = array_reverse ( explode ( '.', $query ) );
                        $_query = implode ( '.', $ip ) . '.in-addr.arpa';
                        $type   = DNS_PTR;
                    
                    }
                    else {
                    
                        $type   = DNS_A;
                        $_query = trim ( $query, '.' );
                    
                    }
                    
                    // get records
                    $q_result = dns_get_record ( $_query, $type );
                    
                    
                    // check if there are records
                    if ( count ( $q_result ) > 0 ) {
                    
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
                    
                    // message output and set timer
                    $this -> privmsg ( $in['args'][0], '[' . "\x02" . 'DNS' . "\x02" . '] ' . $return );
                    $this -> last_dns_of [$in['args'][0]] = 15;
                        
                
                }
            
            
            }
            
        
        }
        
        
        function privmsg ( $chan, $msg ) {
        
            $this -> core -> privmsg ( $chan, $msg );
            
        }
            
    }
        
        
?>