<?php
class Wsu_Networksecurities_Model_Checker extends Mage_Core_Model_Abstract {
    public function init($params) {
        $firstname   = $params['firstname'];
        $lastname    = $params['lastname'];
        $emailprefix = explode('@', $params['email']);
        $emailprefix = $emailprefix[0];
        $params      = array(
            $firstname,
            $lastname,
            $emailprefix
        );
        return $this->check($firstname, $lastname, $emailprefix, $params);
    }


	function email_isValid($email) {
		if(preg_match($this->emailpattern(), $email, $matches)) {
			return true;
		}else{
			return false;	
		}
	}

	function email_resolves($email) {
		// Check email syntax
		if($this->email_isValid($email)) {
			//this is a real formate for an email so it's good to go
			$emailparts = explode('@', $params['email']);
			$user = $emailparts[0];
			$domain = $emailparts[1];
	
			// Check availability of DNS MX records
			if(getmxrr($domain, $mxhosts, $mxweight)) {
				for($i=0;$i<count($mxhosts);$i++) {
					$mxs[$mxhosts[$i]] = $mxweight[$i];
				}
	
				// Sort the records
				asort($mxs);
				$mailers = array_keys($mxs);
			}elseif(checkdnsrr($domain, 'A')) {
				$mailers[0] = gethostbyname($domain);
			}else{ $mailers = array();
			}
			$total = count($mailers);
			// Added to still catch domains with no MX records
			if($total == 0 || !$total) {
				$error = "No MX record found for the domain.";
			}
		}
		return ($error ? $error : true);
	}





    ///http://www.projecthoneypot.org/httpbl_api.php
    //jradpkbwwnqd.7.1.1.127.dnsbl.httpbl.org
    //[Access Key].[Octet-Reversed IP].dnsbl.httpbl.org 
    //Query: abcdefghijkl.2.1.9.127.dnsbl.httpbl.org
    //Response: 127.3.5.1
    // The visitor verification function
    function httpbl_check_visitor() {
		$HELPER = Mage::helper('wsu_networksecurities');
        $key    = $HELPER->getConfig('honeypot/hpp_api_key');
        // The http:BL query
		
		if($HELPER->getConfig('honeypot/hpp_test_mode')) {
			$ip = $HELPER->getConfig('honeypot/hpp_test_ip');
		}else{
			$ip = $HELPER->get_ip_address();
		}

		$OctetReversedIP = implode(".", array_reverse(explode(".",$ip)));
		$query = $key . "." . $OctetReversedIP . ".dnsbl.httpbl.org";
		
		echo $query.'</br>';

		$response = gethostbyname($query);

		if ($response == $query) {
			//if the domain does not resolve then it will be the same thing we passed to gethostbyname
			return false;
		}
		
		$result = explode(".", $response);
		
        // If the response is positive,
        if ($result[0] == 127) {
            // Get thresholds
            $lastseen_threshold		= $HELPER->getConfig('honeypot/hpp_lastseen_threshold');
            $threatscore_threshold	= $HELPER->getConfig('honeypot/hpp_threatscore_threshold');
            $suspicious_threshold 	= $HELPER->getConfig('honeypot/hpp_suspicious_threshold');
            $harvester_threshold 	= $HELPER->getConfig('honeypot/hpp_harvester_threshold');
            $comment_threshold 		= $HELPER->getConfig('honeypot/hpp_comment_threshold');
            /*
			for ($i = 0; pow(2, $i) <= 4; $i++) {
                $value          = pow(2, $i);
                $denied[$value] = get_option('httpbl_deny_' . $value);
            }
            $hp      = get_option('httpbl_hp');

            // Assume that visitor's OK
            $age     = false;
            $threat  = false;
            $deny    = false;
            $blocked = false;
            if ($result[1] < $lastseen_threshold)
                $age = true;
            // Check suspicious threat
            if ($result[3] & 1) {
                if ($suspicious_threshold) {
                    if ($result[2] > $suspicious_threshold)
                        $threat = true;
                }else{ if ($result[2] > $threatscore_threshold)
                        $threat = true;
                }
            }
            // Check harvester threat
            if ($result[3] & 2) {
                if ($harvester_threshold) {
                    if ($result[2] > $harvester_threshold)
                        $threat = true;
                }else{ if ($result[2] > $threatscore_threshold)
                        $threat = true;
                }
            }
            // Check comment spammer threat
            if ($result[3] & 4) {
                if ($comment_threshold) {
                    if ($result[2] > $comment_threshold)
                        $threat = true;
                }else{ if ($result[2] > $threatscore_threshold)
                        $threat = true;
                }
            }			*/
            /*
				foreach ($denied as $key => $value) {
					if (($result[3] - $result[3] % $key) > 0 and $value)
						$deny = true;
				}
				// If he's not OK
				if ($deny && $age && $threat) {
					$blocked = true;
					// If we've got a Honey Pot link
					if ($hp) {
						header("HTTP/1.1 301 Moved Permanently ");
						header("Location: $hp");
					}
				}
			*/			
			$blocked=false;
			return $ip.'--'.$_SERVER["HTTP_USER_AGENT"].'--'.implode(".",$result).'--'.$blocked;
            /*// Are we logging?
            if (get_option("httpbl_log") == true) {
                // At first we assume that the visitor
                // should be logged
                $log = true;
                // Checking if he's not one of those, who
                // are not logged
                $ips = explode(" ", get_option("httpbl_not_logged_ips"));
                foreach ($ips as $ip) {
                    if ($ip == $_SERVER["REMOTE_ADDR"])
                        $log = false;
                }
                // Don't log search engine bots
                if ($result[3] == 0)
                    $log = false;
                // If we log only blocked ones
                if (get_option("httpbl_log_blocked_only") and !$blocked) {
                    $log = false;
                }
                // If he can be logged, we log him
                if ($log)
                    httpbl_add_log($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"], implode($result, "."), $blocked);
            }*/
        }
    }
    public function simplecheck($firstname, $lastname, $emailprefix, $params) {
        $_index = 0;
        // Two fields identical
        if ($firstname == $lastname) {
            $_index += 1;
            // the third one?
            if ($lastname == $emailprefix) {
                $_index += 2;
            }
            // Two fields...
        }else if ($firstname == $emailprefix) {
            $_index += 1;
            if ($lastname = $firstname) {
                // the third one?
                $_index += 2;
            }
        }else if ($lastname == $emailprefix) {
            $_index += 1;
            if ($firstname == $lastname) {
                $_index += 2;
            }
        }
        /**
         *
         * This loop checks all parameters seperately on
         * different aspects such as lenght or content
         *
         **/
        foreach ($params as $param) {
            if (strlen($param) >= 15) { // item has more than 15 chars = spam possibility increases a little
                $_index += 1;
                Mage::log("SPAM: " . $param . " has more than 15 Characters");
            }
            if (is_numeric($param)) { // Param contains numbers only == spam (heavy rating!
                $_index += 2.5;
                Mage::log("SPAM: " . $param . " contains only numbers");
            }
            if (preg_match("([b-df-hj-np-tv-z]{3})", $param, $matches)) { // More than 3 consecutive consonants == Spam!
                if (!($matches[0] == "rrm")) { // Herrmann is okay
                    $_index += 1;
                    Mage::log("SPAM: " . $param . " contains 3 or more consecutive consonants");
                }
            }
            if (preg_match("([aeiou]{3})", $param, $matches)) { // More than 3 consecutive vouwels == spam
                if (!($matches[0] == "eie")) {
                    Mage::log("matches: " . $matches[0]); // Meier is okay
                    $_index += 1;
                    Mage::log("SPAM: " . $param . " contains 3 consecutive vowels");
                }
            }
            if (preg_match("([A-Z]{2,})", substr($param, -4))) { // At least two CAPITALS at the end of a string == Spam!
                $_index += 1;
                Mage::log("SPAM: " . $param . " has at least 2 CAPITAL letters at the end");
            }
            if (preg_match_all("([A-Z])", $param, $matches) > 3) { // Param contains more than 3 Capital letters at all
                $_index += 1;
                Mage::log("SPAM: " . $param . " contains more than 3 CAPITALS at all");
            }
            if (preg_match("([a-z])", substr($param, 1, 1)) && preg_match("([A-Z])", substr($param, 1, 1))) { // Param starts with a lowercase+uppercase
                $_index += 1;
                Mage::log("SPAM: " . $param . " starts with a combination lc/uc. E.g. aJohn, bSmith...");
            }
        }
        return $_index;
    }
//this may go away
//or replace with filter_var($email, FILTER_VALIDATE_EMAIL) but that is not pass RFC5321 100%
	public function emailpattern() {
		return '/(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[\t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[\t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*:(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)(?:,\s*(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[\t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*))*)?;\s*)/';
	}
	
	
	
	
}