<?php
/*************************************
* dPHPLib Version 1.2.5 Beta         *
**************************************
* This mod is licensed under GNU/GPL *
* That means, you can EDIT and re-   *
* distribute it, as long as you dont *
* edit any copyright information.    *
*                                    *
*  SEE LICENSE.TXT FOR FURTHER INFO  *
*                                    *
* If you make any changes, you may   *
*      add a notice below.           *
*                                    *
**************************************/

    
    function out($str='',$opt=false,$scream=false,$clean=false) {
        if($scream==2&&empty($str)){
            return false;
        }
        global $lang_mng;
        global $config;
        if($scream!==false&&$scream>$config['BOT']['output']){
            return false;
        }
        if (is_object($lang_mng)&&$clean==false&&!empty($str)){
            if ($config['BOT']['output']>0||$scream==true) {
                echo "\n".'['.date("H:i:s").'] '.$lang_mng->parse_term($str,$opt);
            }
        }
        elseif ($config['BOT']['output']>0||$scream==true&&!empty($str)) {
            echo "\n".'['.date("H:i:s").'] '.$str;
        }
        elseif (empty($str)){echo"\n".'['.date("H:i:s").']';}
    }
    function aout($str='',$opt=false,$scream=false,$clean=false) {
        global $lang_mng;
        global $config;
        if (is_object($lang_mng)&&$clean==false&&!empty($str)){
            if ($config['BOT']['output']>0||$scream==true) {
                echo "\n".'['.date("H:i:s").'] *** '.$lang_mng->parse_term($str,$opt);
            }
        }
        elseif ($config['BOT']['output']>0||$scream==true&&!empty($str)) {
            echo "\n".'['.date("H:i:s").'] *** '.$str;
        }
        elseif (empty($str)){echo"\n".'['.date("H:i:s").']  ***';}
    }
    function rout($str='',$opt=false,$scream=false,$clean=false) {
        global $lang_mng;
        global $config;
        if (is_object($lang_mng)&&$clean==false&&!empty($str)){
            if ($config['BOT']['output']>0||$scream==true) {
                echo "\r".'['.date("H:i:s").'] '.$lang_mng->parse_term($str,$opt);
            }
        }
        elseif ($config['BOT']['output']>0||$scream==true&&!empty($str)) {
            echo "\r".'['.date("H:i:s").'] '.$str;
        }
        elseif (empty($str)){echo"\r".'['.date("H:i:s").'] ***';}
    }

    function file_run_contents($filename) {
        ob_start();
        include($filename);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    function bind_content($filename) {
        ob_start();
        include($filename);
        $output = ob_get_contents();
        ob_end_clean();
        return trim($output);
    }
    function week() {
        if (date('L') == 0) {
            return (ceil((date('z') + 1) / 7) + 1);
        }
        else {
            return (ceil(date('z') / 7) + 1);
        }
    }
    function makeDate($today,$month,$type) {
        $year = date("Y");
        $yeartype = date("L");
        if ($yeartype == 1) {
            return date("".$type."",mktime(0, 0, 0, ($month + 1), ($today + 1), ($year - 1)));
        }
        else {
            return date("".$type."",mktime(0, 0, 0, ($month + 1), $today, ($year - 1)));
        }
    }
    function cfx($str) {
        return base64_encode($str);
    }
    function checkOk($constant) {
        if (defined($constant)) {
            return true;
        }
        else {
            die('You are not authorized to access this site.');
        }
    }
    function escapeString($str,$method) {
        $str = trim($str);
        if ($method == 'mysql') {
            $str = mysql_escape_string($str);
        }
        elseif ($method == 'html') {
            $str = htmlentities($str, ENT_QUOTES);
        }
        elseif ($method == 'multi') {
            $str = mysql_escape_string($str);
            $str = htmlentities($str, ENT_QUOTES);
        }
        return $str; 
    }
    function dateForm($date) {
        $monat = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
        $date2 = $date;
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);
        $month = round($month);
        $month = $monat[$month];
        $date = 'am ' . $day . '. ' . $month . ' ' . $year;
        $yearMonth = date("Y-m");
        $today = date("d");
        $todayYearMonth = $yearMonth . '-' . $today;
        if ($todayYearMonth == $date2) {
            $date = 'Heute';
        }
        if ($today != '01') {
            $yesterday = ($today - 1);
            $yesterdayYearMonth = $yearMonth . '-' . $yesterday;
        }
        if ($yesterdayYearMonth == $date2) {
        $date = 'Gestern';
        }
        return $date;
    }
    function paperheight($papername) {
        $name = strtolower($papername);
        switch($name) {
            case "dl": return 11/2.54*72;
            case "m65": return paperheight("c6");
            case "ledger": case "tabloid": return  17*72;
            case "legal": return paperwidth("ledger");
            case "letter": return 11*72;
            default: return paperwidth($name)*sqrt(2);
        }
    }
    function paperwidth($papername) {
        $name = strtolower($papername);
        switch($name) {
            case "dl": return 22/2.54*72;
            case "m65": return paperwidth("c5");
            case "ledger": case "tabloid": return  14*72;
            case "legal": case "letter": return paperheight("ledger")>>1;
            default:
            $i=strpos("ebca",$name{0});
            $j=substr($name,1);
            if($i!=false && ($j>0 || $j==="0"))
                return 100/(pow(2,($i+$j*4-1)/8))/2.54*72;
            else { die("Unkown paper format: $papername"); }
        }
    }
    function hex2rgb($hex) {
        $color = str_replace('#','',$hex);
        $rgb = array(
            'r' => hexdec(substr($color,0,2)),
            'g' => hexdec(substr($color,2,2)),
            'b' => hexdec(substr($color,4,2)));
        return $rgb;
    }
    function rgb2cmyk($var1) {
        if(is_array($var1)) {
            $r = $var1['r'];
            $g = $var1['g'];
            $b = $var1['b'];
        }
        else { 
            $r=$var1;
            $cyan    = 255 - $r;
            $magenta = 255 - $g;
            $yellow  = 255 - $b;
            $black   = min($cyan, $magenta, $yellow);
            $cyan    = @(($cyan    - $black) / (255 - $black)) * 255;
            $magenta = @(($magenta - $black) / (255 - $black)) * 255;
            $yellow  = @(($yellow  - $black) / (255 - $black)) * 255;
            return array(
                'c' => $cyan / 255,
                'm' => $magenta / 255,
                'y' => $yellow / 255,
                'k' => $black / 255
            );
        }
    }
    function runtimeError($type,$function,$message) {
        die('<b>dPHPLib Error:</b> '.$type.' error while calling '.$function.'() - '.$message);
    }
    function runtimeWarning($function,$message) {
        echo '<b>dPHPLib Warning:</b> While calling '.$function.'(): '.$message;
    }
    function gen_sess_id($len=9) {
        // Define vars
        $chars    = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $session  = '';
    
        // Set session ID
        for ($i=0;$i<$len;$i++) {
            if (rand(0,15) > 9) {
                $session .= rand(0,9);
            }
            else {
                if (rand(0,10) > 4) {
                    $session .= strtolower($chars[rand(0,25)]);
                }
                else {
                    $session .= $chars[rand(0,25)];
                }
            }
        }
        return $session;
    }
    function array_sortbykey($array,$start) {
        if (!is_array($array)) {
            runtimeError('syntax','array_sortbykey','given value 1 is no array.');
        }
        $indexed = '-';
        $return  = array();
        #echo substr_count($indexed,',').'-'.count($array);
        
        while (substr_count($indexed,',') < count($array)) {
            foreach ($array as $key => $value) {
                if ($key == $start) {
                    $return[$key] = $value;
                    $start++;
                    $indexed .= $key.',';
                }
            }
        }
        return $return;
    }
    
    function replace($string,$array,$replace) {
        if (is_array($array)) {
            if (is_array($replace)) {
                foreach ($array as $key => $expr) {
                    $string = str_replace($expr,$replace[$key],$string);
                }
            }
            elseif (gettype($replace) == 'string') {
                foreach ($array as $expr) {
                    $string = str_replace($expr,$replace,$string);
                }
            }
            return $string;
        }
        else {
            runtimeError('syntax','replace','given value 2 is no array.');
        }
    }
    function isValidEmail($email,$type='ereg') {
        if (strtoupper(substr($type,0,4)) == 'SAVE') {
            $save   = true;
            $return = false;
            $type   = substr($type,4,strlen($type));
        }
        else {
            $return = true;
        }
        if ($type == 'ereg' || $type == 'both') {
            if (ereg("^.+@.+\\..+$", $email)) {
                $return = true;
            }
            else {
                $return = false;
            }
        }
        if ($type == 'dns' || $type == 'both') {
            if (lib_checkdnsrr(substr($email,(strpos($email,'@')+1),strlen($email)),'MX')) {
                if ($save) {
                    $return = true;
                }
                else {
                    $return = false;
                }
            }
            else {
                $return = false;
            }
        }
        return $return;
    }
    function operatingSystem() {
        if (strtoupper(substr(PHP_OS,0,3)) == 'WIN') {
            return 'WIN';
        }
        else {
            return 'NIX';
        }
    }
    function lib_checkdnsrr($host,$type='UNDEFINED') {
        $type  = strtoupper($type);
        $types = array('A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY');
        
        if ($type != 'UNDEFINED' && in_array($type,$types)) {
        }
        elseif (!in_array($type,$types) && $type != 'UNDEFINED') {
            runtimeWarning('lib_checkdnsrr','Given attribute 2 (type) for function is not supported. Must be one of <a href="http://www.php.net/checkdnsrr">them</a>.');
            return false;
        }
        if ($type == 'UNDEFINED') {
            $type = 'A';
        }
        if (operatingSystem() == 'WIN') {
            @exec('nslookup -type='.$type.' '.escapeshellcmd($host), $output);
            foreach ($output as $line) {
                if (preg_match('/^'.$host.'/',$line)) { 
                    return true; 
                }
                else {
                    return false;
                }
            }
        }
        else {
            if (checkdnsrr($host,$type)) {
                return true;
            }
            elseif (!checkdnsrr($host,$type)) {
                return false;
            }
            else {
                runtimeWarning('lib_checkdnsrr','dPHPLib was not able to call method "checkdnsrr()". Is your PHP installation correct?');
                if (!is_function(checkdnsrr())) {
                    runtimeError('PHP installation fail','dPHPLib was not able to call method "checkdnsrr()". It seems not to be installed on your system.');
                }
            }
        }
    }
    function micro_time() {
        $time = explode( " ", microtime() ); 
        return( (float) $time[ 0 ] + (float) $time[ 1 ] ); 
    }
    function duration($time,$time2) {
        $time  = $time2;
        $time2 = time();
        $runtime     = ($time2-$time);
        if ($runtime < 0) {
            $runtime = $runtime*(-1);
        }
        $runtimeShow = '';
        if ($runtime > 60) {
            $runtimeM = floor($runtime/60);
            $runtimeS = ($runtime-($runtimeM*60));
            if ($runtimeM > 60) {
                $runtimeH = floor($runtimeM/60);
                $runtimeM = ($runtimeM-($runtimeH*60));
                if ($runtimeH > 24) {
                    $runtimeD = floor($runtimeH/24);
                    $runtimeH = ($runtimeH-($runtimeD*24));
                    if ($runtimeD > 1) {
                        $s = 's';
                    }
                    $runtimeShow  = $runtimeD.' day'.$s.', ';
                }
                $runtimeShow  .= $runtimeH.' ';
                if ($runtimeH == 1) {
                    $runtimeShow .= 'hour, ';
                }
                else {
                    $runtimeShow .= 'hours, ';
                }
            }
            if ($runtimeM != 1) {
                $runtime_s = 's';
            }
            else {
                $runtime_s = '';
            }
            $runtimeShow  .= $runtimeM.' minute'.$runtime_s;
            if ($runtimeS != 0) {
                $runtimeShow .= ' and '.$runtimeS.' seconds';
            }
        }
        else {
            $runtimeShow = $runtime.' seconds';
        }
        return $runtimeShow;
    }
    function durationApprox($time) {
        if ($time > time()) {
            $sec    = $time-time();
            $return = 'in ';
        }
        else {
            $return = 'vor ';
            $sec    = time()-$time;
        }
        if ($sec == 0) {
            $return .= 'nicht all zu langer Zeit';
        }
        elseif ($sec < 10) {
            $return .= $sec.' Sekunden';
        }
        elseif ($sec > 10 && $sec <= 50) {
            if ($return == 'vor ') {
                $return = 'gerade eben';
            }
            else {
                $return = 'jetzt gleich';
            }
        }
        elseif ($sec > 50 && $sec <= 95) {
            $return .=  'einer Minute';
        }
        elseif ($sec > 95 && $sec <= 255) {
            $return .=  ceil(($sec/60)).' Minuten';
        }
        elseif ($sec > 255 && $sec <= 300) {
            $return .=  'less than 5 min';
        }
        elseif ($sec > 300 && $sec <= 390) {
            $return .=  '5 Minuten';
        }
        elseif ($sec > 390 && $sec <= 720) {
            $return .=  'ungefähr 10 Minuten';
        }
        elseif ($sec > 720 && $sec <= 1020) {
            $return .=  'einer viertel Stunde';
        }
        elseif ($sec > 1020 && $sec <= 2280) {
            $return .=  'einer halben Stunde';
        }
        elseif ($sec > 2280 && $sec <= 3120) {
            $return .=  'einer dreiviertel Stunde';
        }
        elseif ($sec > 3120 && $sec <= 5400) {
            $return .=  'ungefähr einer Stunde';
        }
        elseif ($sec > 5400 && $sec <= 86400) {
            $return .=  'ungefähr '.ceil($sec/4000).' Stunden';
        }
        elseif ($sec > 86400 && $sec <= 100800) {
            $return .=  'mehr als 24 Stunden';
        }
        else {
            $return = dateForm(date("Y-m-d",$time)).' um '.date("H:i",$time).' Uhr';
        }
        return $return;
    }
    function ASCIIformat($str,$len) {
        $str  = trimText($str,$len);
        $loss = $len-strlen($str);
        if ($loss > 0) {
            for ($i=0;$i<=$loss;$i++) {
                $str = ' '.$str;
            }
        }
        return $str;
    }
    function ASCIIfill($str,$len) {
        $str  = trimText($str,$len);
        $loss = $len-strlen($str);
        if ($loss > 0) {
            for ($i=0;$i<=$loss;$i++) {
                $str .= ' ';
            }
        }
        return $str;
    }

    function is_utf8($str) {
        if(preg_match(
            '/^(([\x00-\x7F])|'.
            '([\xC0-\xDF][\x80-\xBF])|'.
            '([\xE0-\xEF][\x80-\xBF]{2})|'.
            '([\xF0-\xF7][\x80-\xBF]{3}))*$/',
            $str, $match))
        {
            return true;
        }
        return false;
    }
    function encrypt($string,  $key) {
        $result = '';
        $string = $string.'/';
        for($i=0; $i<=strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }
    function decrypt($string,  $key) {
        $result = '';
        $string = base64_decode($string);
        for($i=0; $i<=strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }

        return substr($result,0,strrpos($result,'/'));
    } 
    
    function numToWord ( $num, $upperChar=false ) {
   
        $useLang   = 'de';
   
        $arr['de'] = array('keine','eine','zwei','drei','vier','fünf','sechs','sieben','acht','neun','zehn','elf','zwölf');
        $arr['en'] = array('no','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve');
        
        if ( isset ( $arr[ $useLang ] [ $num ] ) ) {
            
            if ( $upperChar ) {
            
                return substr ( strtoupper ( $arr[ $useLang ] [ $num ] ), 0, 1 ) . substr ( $arr[ $useLang ] [ $num ], 1 );
                
            }
            
            else {
            
                return $arr[ $useLang ] [ $num ];
                
            }
            
        }
        
        else {
        
            return $num;
            
        }
    
    }
    function val_url ( $str ) {
        return trim(strtolower(preg_replace('/[^0-9a-zA-Z]+/', '-', $str)));
    }
    function isinIpRange($ip,$range) {
            $ip         = trim($ip,'.');
            $startRange = $range; 
            $endRange   = $range; 
            $dots = 3-substr_count($range,'.');
            for ($i=0;$i<$dots;$i++) {
                $startRange .= '.0';
                $endRange   .= '.255';
            }
            if (ip2long($ip) >= ip2long($startRange) && ip2long($ip) <= ip2long($endRange)) {
                return true;
            }
            else {
                return false;
            }
        }
        
    function filter_html ( $text, $allowed_tags ) {
        
        preg_match_all ( '/(<[^>]+>)/i',$text, $get_html );
        
        foreach ( $get_html [0] as $tag_num => $html_tag ) {
        
            $allowed = false;
            
            foreach ( $allowed_tags as $preg_tag ) {
            
                if (preg_match ( '/<'.trim($preg_tag).'>/i', trim($html_tag), $match_out ) ) {

                    $allowed = true;
                    
                }

            }
            
            if ( $allowed == false ) {
            
                #$text = str_replace ( $html_tag, '', $text );
                $text = preg_replace ( '/(<[^>]+>)/i', '', $text );
                
            }
            
        }
        
        return $text;
    }
    function isValidIP($ip) {
        return preg_match ( '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip );
    }
    function readEqualDb ($file) {
    
        $ret  = array();
        if ( $file = file ( $file ) ) {
   
            foreach ( $file as $lineno => $line ) {
            
                $explode = explode('=',$line);
                $ret[trim($explode[0])] = trim($explode[1]);
            
            }
        
            return $file;
            
        }
        else {
        
            return false;
            
        }
    }
    
    function has_flags ( $mask, $flag ) {
    
        if ( is_array ( $mask ) ) {
        
            if ( isset ( $mask ['full'] ) ) {
        
                $nick  = $mask['nick'];
                $host  = $mask['host'];
                $ident = $mask['ident'];

            }
            else {
            
                $nick  = $mask[0];
                $host  = $mask[2];
                $ident = $mask[1];

            }
            
            $mask = $nick . '!' . $ident . '@' . $host;
        
        }
        
    
        $h_flags = get_rights ( $mask );
        if ( $h_flags !== false ) {
        
            $r_flags = str_split ( $flag );
            foreach ( $r_flags as $flag ) {
            
                if ( strstr ( $h_flags, $flag ) === false ) {
                    return false;
                
                }
            
            }
            
            return true;
            
        }
        else {
        
            return false;
            
        }
    
    }
    function get_rights ( $mask ) {
    
        if ( is_array ( $mask ) ) {
        
        
            if ( isset ( $mask ['full'] ) ) {
        
                $nick  = $mask['nick'];
                $host  = $mask['host'];
                $ident = $mask['ident'];

            }
            else {
            
                $nick  = $mask[0];
                $host  = $mask[2];
                $ident = $mask[1];

            }
            
            $mask = $nick . '!' . $ident . '@' . $host;
        
        }
    
    
        if ( $file = file ( ROOT_PATH . 'tdb/adminlist.txt' ) ) {
   
            foreach ( $file as $lineno => $line ) {
            
                $explode = explode('=',$line);
                
                if ( does_match ( $mask, trim($explode[0]) ) ) {
                
                    return trim($explode[1]);
                    
                }
                
            }
            return false;
            
        }
        else {
        
            aout('SYSTEM_ADMIN_DBFAIL');
            return false;
            
        }
    
    }
    function does_match($mask, $haystack) {
    
        if ( $mask == $haystack ) {
        
            return true;
            
        }
    
        // the simple one doesn't match? no problem, maybe it matches with a simple wildcard
        $nick  = substr ( $mask, 0, strpos ( $mask, '!' ) );
        $host  = substr ( $mask, ( strpos ( $mask, '@' ) + 1 ) );
        $ident = str_replace ( '@' . $host, '', substr ( $mask, ( strpos ( $mask, '!' ) + 1 ) ) );
        
        $h_nick  = substr ( $haystack, 0, strpos ( $haystack, '!' ) );
        $h_host  = substr ( $haystack, ( strpos ( $haystack, '@' ) + 1 ) );
        $h_ident = str_replace ( '@' . $h_host, '', substr ( $haystack, ( strpos ( $haystack, '!' ) + 1 ) ) );
        
        if ( $nick == $h_nick || $nick == '*' || $h_nick == '*' ) {
        
            if ( $host == $h_host || $host == '*' || $h_host == '*' ) {
            
                if ( $ident == $h_ident || $ident == '*' || $h_ident == '*' ) {
                
                    return true;
                
                }
                
            }
            
        }
        
        // still no match? try regex and complex wildcards.
        $h_nick  = str_replace ( '*', '[^!]+', $h_nick );
        $h_nick  = str_replace ( '?', '(\S{1})', $h_nick );
        $h_ident = str_replace ( '*', '[^@]+', $h_ident );
        $h_ident = str_replace ( '?', '(\S{1})', $h_ident );
        $h_host  = str_replace ( '*', '(.*)', $h_host );
        $h_host  = str_replace ( '?', '(\S{1})', $h_host );
        
        if ( preg_match ( '/' . $h_nick . '!' . $h_ident . '@' . $h_host . '/', $mask ) ) {
        
            return true;
        
        }
        
        // if theres still no match, there is nothing to match. so return false.
        return false;

    }
    function parse_vars ( $str, $opt, $seperated='%' ) {
    
        if ( !is_array ( $opt ) ) {
        
            return false;
            
        }
    
        if ( preg_match_all ( '/' . $seperated . '([^' . $seperated . '\s]+)' . $seperated . '/', $str, $match ) && is_array( $opt ) ) {
            
            foreach ( $match[1] as $key => $var_name ) {
                
                // when giving var-values, you can either give each a name (which would be array('foo'=>'bar');)
                if ( !isset ( $opt [ $var_name ] ) ) {
                    
                    $opt_val = $opt [ $key ];
                        
                }
                else {
                    
                    // or as a numeric list, which would be array('foo','bar'); (occurs as 0=>foo,1=>bar, etc.)
                    $opt_val = $opt [ $var_name ];
                       
                }
                
                // replace the found var with the given value
                $str = str_replace ( '%' . $var_name . '%', $opt_val, $str );
                   
            }
            
            return $str;
               
        }
        else {
        
            return $str;
            
        }
    
    }
?>