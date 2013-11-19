<?php

    class module_decide {
    
        // this has been taken from mstasty1
        // the decide()-method is taken 1:1 and we did not make any changes
    
        function __construct (&$core,$id) {
        
            $this -> core = &$core;
            $this -> id   = $id;
                
            $core -> register ( 'PRIVMSG', '!decide', true, $id );
            
        
        }
        
        function in ( $in ) {
        
            $this -> decide ( $in['args'][0], $in['sender']['nick'], explode ( ' ', $in['raw'] ) );
        
        }
        
        function decide($chan,$nick,$strRet) {
                $elements = array_slice($strRet,4);
                $newElemn = array();
                foreach ($elements as $key => $str) {
                    if (empty($str)) {
                        unset($elements[$key]);
                    }
                }
                $incoming = implode(' ',$elements);
                preg_match_all('/"([^"]+)"/i',$incoming,$match);
                foreach($match[1] as $element) {
                    $element = trim($element);
                    if (!empty($element)) {
                        array_push($newElemn,$element);
                        $incoming = str_replace('"'.$element.'"','',$incoming);
                    }
                }
                $incoming = trim($incoming);
                foreach (explode(' ',$incoming) as $word) {
                    array_push($newElemn,$word);
                }
                if (count($newElemn) > 0) {
                    $this->privmsg($chan,$nick.', you should choose '.$newElemn[rand(0,(count($newElemn)-1))]);
                }
                else {
                    $this->privmsg($chan,$nick.', you should choose giving me some arguments.');
                }
        }
        
        function privmsg ( $chan, $msg ) {
        
            $this -> core -> privmsg ( $chan, $msg );
            
        }
            
    }
        
        
?>