<?php

    class module_bams {
    
        public function __construct (&$core,$id) {
        
            $this -> core = &$core;
            $this -> id   = $id;
                
            $core -> register ( 'PRIVMSG', '!rss', true, $id );
            
            include ( CONFIG_PATH . 'rssreader.cfg.php' );
            $this -> config = $rss;
            
            foreach ( $this -> config as $block_name => $block_info ) {
            
                if ( $block_info [ 'is_active'] == true ) {
            
                    $this -> config [ $block_name ] ['timer_hash']   = gen_sess_id (16);
                    $this -> config [ $block_name ] ['last_checked'] = time();
                    $core -> timer ( $block_info ['check_delay'], 'PRIVMSG %nick% :!rss ' . $block_name . ' ' . $this -> config [$block_name]['timer_hash'] );
                        
                }
                else {
                    
                    unset ( $this -> config [ $block_name ] );
                        
                }
                
            }
            
        }
        
        public function in ( $in ) {
        
            if ( $in['atext'][1] == 'rehash' ) {
            
                include ( CONFIG_PATH . 'rssreader.cfg.php' );
                $this -> config = $rss;
                foreach ( $this -> config as $block_name => $block_info ) {
            
                    if ( $block_info ['is_active'] == true ) {
            
                        $this -> config [ $block_name ] ['last_checked'] = time();
                        
                    }
                    else {
                    
                        unset ( $this -> config [ $block_name ] );
                        
                    }
                
                }
                $this -> core -> notice ( $in['sender']['nick'], 'RSS config successfully rehashed' );
               
            }
            else {
        
                $this -> read_feeds = array();
                $set_new_timer      = false;
                
                if ( isset ( $in ['atext'] [1] ) ) {
                
                    if ( isset ( $this -> config [ $in['atext'][1] ] ) ) {
                    
                        $read_feeds = array ( $in['atext'][1] => $this -> config [ $in['atext'][1] ] );
                        if ( isset ( $in ['atext'] [2] ) ) {
                        
                            if ( $in['atext'][2] == $this -> config [ $in['atext'][1] ] ['timer_hash'] ) {
                            
                                $set_new_timer = true;
                            
                            }
                        
                        }
                        
                    }
                    else {
                    
                        $read_feeds = $this -> config;
                    
                    }
                }
                else {
                
                    $read_feeds = $this -> config;
                
                }
                
                foreach ( $read_feeds as $block_name => $rss_block ) {
                
                    if ( !isset ( $rss_block ['feed_url'] ) ) {
                    
                        $rss_block ['feed_url'] = file ( ROOT_PATH . 'tdb/rssfeeds.txt' );
                        
                    }
                
                    elseif ( !is_array ( $rss_block ['feed_url'] ) ) {
                    
                        $rss_block ['feed_url'] = explode ( ' ', $rss_block ['feed_url'] );
                    
                    }
                    if ( !is_array ( $rss_block ['post_chan'] ) ) {
                    
                        $rss_block ['post_chan'] = explode ( ' ', $rss_block ['post_chan'] );
                    
                    }
                    
                    foreach ( $rss_block ['feed_url'] as $url ) {
            
                        $id = $this -> core -> http -> request ( $this, $url );
                        $this -> read_feeds [$id] = array (
                        
                            'post_chan'   => $rss_block ['post_chan'],
                            'post_format' => $rss_block ['post_format'],
                            'max_updates' => $rss_block ['max_updates'],
                            'save_type'   => $rss_block ['save_type'],
                            'new_timer'   => $set_new_timer,
                            'has_posted'  => false,
                            'block_name'  => $block_name,
                            'feed_url'    => $url
                            
                        );
                        
                    }
                    
                }
                
            }
        
        }
        
        public function http_in ( $id, $content ) {
        
        
            try {
            
                $rss_output = new SimpleXMLElement ( $content );
            
            }
            
            catch (Exception $e) {
            
                echo"\n".$e->getMessage()." for feed " . $id . "\n";
                return false;
            
            }
            
            
            $irc_output = array();
            
            
            
            
            $irc_output ['channel'] = $rss_output -> channel -> title;
            $irc_output ['link']    = $rss_output -> channel -> link;
            $irc_output ['desc']    = $rss_output -> channel -> desc;
            
            $message_scheme = str_replace ( '%chan_title%', $rss_output -> channel -> title, $this -> read_feeds [$id] ['post_format'] );
            
            // check structure of feed (for invalid feeds)
            if ( !isset ( $rss_output -> channel -> item ) ) {
            
                $rss_item_array = $rss_output -> item;
            
            }
            else {
            
                $rss_item_array = $rss_output -> channel -> item;
            
            }
            
            // read database (if exist)
            if ( file_exists ( ROOT_PATH . 'tdb/rss_' . md5 ( $this -> read_feeds [$id] [ 'feed_url' ] ) ) ) {
            
                $_already_read = file ( ROOT_PATH .  'tdb/rss_' . md5 ( $this -> read_feeds [$id] [ 'feed_url' ] ) );
            
            }
            // and else, send array for inarray()
            else {
            
                $_already_read = array();
                
            }
            
            // before running the items in foreach, check wether they are array and transform if required (occurs when only one post is made)
            if ( !is_array ( $rss_item_array ) ) {
            
                $rss_item_array = array ( $rss_item_array );
                
            }
            
            // prepare for saving new posts
            $_sent_items = array();
            
            foreach ( $this -> read_feeds [$id] ['post_chan'] as $channel ) {
            
                $sent_items = 0;
            
                foreach ( $rss_item_array as $entry ) {
                
                    // create feed identifier by savetype
                    switch ( $this -> read_feeds [$id] ['save_type'] ) {
                    
                        case 0:  $_feed_identifier = sha1 ( $entry -> link );
                        case 1:  $_feed_identifier = sha1 ( $entry -> description );
                        case 2:  $_feed_identifier = sha1 ( $entry -> title );
                        
                        break;
                        
                    }
                
                    if ( $sent_items < $this -> read_feeds [$id] ['max_updates'] && !in_array ( $_feed_identifier . "\n", $_already_read ) ) {
                
                        $sent_items++;
                    
                        $message = str_replace ( '%title%', $entry -> title, $message_scheme );
                        $message = str_replace ( '%url%', $entry -> link, $message );
                    
                        $this -> core -> privmsg ( $channel, $message );
                        
                        // add to new sent items by key (so we have no multiple entries)
                        $_sent_items [ $_feed_identifier ] = true;
                    
                    }
                    else {
                        echo "\nalready got {$entry->title}\n";
                        break;
                        
                    }
                
                }
                
            }
            
            if ( count ( $_sent_items ) > 0 ) {
            
                // open database
                $_already_read_db = fopen ( ROOT_PATH . 'tdb/rss_' . md5 ( $this -> read_feeds [$id] [ 'feed_url' ] ), 'w' );
            
                // save latest read news from $_sent_items array
                foreach ( $_sent_items as $identifier => $bool ) {
                
                    fwrite ( $_already_read_db, $identifier . "\n" );
                
                }
                // and close file
                fclose ( $_already_read_db );
                
            }
            
            if ( $this -> read_feeds [$id] ['new_timer'] == true ) {
            
                $_hash                                                                        = gen_sess_id (16);
                $this -> config [ $this -> read_feeds [$id] ['block_name'] ] ['timer_hash']   = $_hash;
                $this -> config [ $this -> read_feeds [$id] ['block_name'] ] ['last_checked'] = time();
                $this -> core -> timer ( $this -> config [ $this -> read_feeds [$id] ['block_name'] ] ['check_delay'], 'PRIVMSG %nick% :!rss ' . $this -> read_feeds [$id] ['block_name'] . ' ' . $_hash );
            
            }
            
            unset ( $this -> read_feeds [$id] );
            
            
        
        }
       
        
    }
    
?>