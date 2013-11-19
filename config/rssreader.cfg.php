<?php

    // All feeds that match the default config tag have to be in /tdb/rssfeeds.txt (line by line)
    // You can add new config blocks and give them a name to call them by !rss <name>
    // You have to specify an url with the option feed_url when adding a new block to this config.
    // URLs may also be an array to add more than one rss feed.
    

    $rss = array (
    
        'default' => array (
        
                'post_chan'     => '#mstasty',    // the channel where to post new updates (array will post to multiple channels or you can seperate multiple chans by a whitespace)
                'post_format'   => '[%chan_title%] %title% (%url%)',
                'check_delay'   => 10,         // the time delay between two checks
                'max_updates'   => 1,           // the max amount of new updates posted
                'save_type'     => 1,           // wether to save posted updates by 0=post url (works best), by 1=description (will even post text updates), by 2=title
                'is_active'     => true         // wether this block is active or not
                
        ),
        
        'mstasty' => array (
        
                'post_chan'     => array ( '#mstasty', '#mstasty_updates' ),
                'post_format'   => 'New MsTasty2-update: %title%  [%url%]',
                'check_delay'   => 1500,
                'max_updates'   => 1,
                'save_type'     => 1,
                'feed_url'      => 'http://mstasty.sourceforge.net/feed.rss',
                'is_active'     => true
                
        ),
        
        'twitter' => array (
        
                'post_chan'     => '#mstasty',
                'post_format'   => '[Twitter] %title%',
                'check_delay'   => 500,
                'max_updates'   => 1,
                'save_type'     => 0,
                'feed_url'      => array ( 'http://twitter.com/statuses/user_timeline/23938819.rss', 'http://twitter.com/statuses/user_timeline/2425151.rss', 'http://twitter.com/statuses/user_timeline/45122445.rss' ),
                'is_active'     => true
                
        ),
        
    );
    
?>
                