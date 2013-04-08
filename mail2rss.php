<?php
	// Mail2RSS - Display your latest mails in a RSS feed
	// Version: 0.1 alpha
	// Author: Kevin VUILLEUMIER (kevinvuilleumier.net)
	// Licence: http://www.opensource.org/licenses/zlib-license.php

    // User-defined constants
	// ============= CHANGE BY YOUR OWN INFORMATIONS HERE ! =============
    define('TOKEN', 'YOUR_OWN_TOKEN');
    define('MAIL_PROTO', 'IMAP');   // You can choose : IMAP, POP3
    define('MAIL_USERNAME', 'you@yourmailhost.com');
    define('MAIL_PASSWORD', 'PASSWORD');
    define('MAIL_SERVER', 'yourmailserver.com');
    define('MAIL_PORT', 993);
    define('MAIL_SECURITY', 'SSL'); // You can choose : SSL, TLS or leave blank
    //define('MAIL_CHECK_CERTIFS', true);
	// ==================================================================
    
    //error_reporting(-1); // See all errors (for debugging only)
    
	// If the token is not sent or if it is invalid, then force quit
    if (!isset($_GET['token']) || $_GET['token'] != TOKEN) exit;
    
    ob_start();

    header('Content-Type: application/rss+xml; charset=utf-8');
    
    $imap_address = '{'.MAIL_SERVER.':'.MAIL_PORT;
    
    if (MAIL_PROTO == 'IMAP') {
        $imap_address .= '/imap';
    } else if (MAIL_PROTO == 'POP3') {
        $imap_address .= '/pop3';
    }
    
    if (MAIL_SECURITY == 'SSL') {
        $imap_address .= '/ssl';
    } else if (MAIL_SECURITY == 'TLS') {
        $imap_address .= '/tls';
    }
    
    $imap_address .= '}';
    
    $mbox = imap_open($imap_address, MAIL_USERNAME, MAIL_PASSWORD);
    
    if($mbox) {
        $num = imap_num_msg($mbox);

        if($num >0) {
            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			echo "\t".'<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">'."\n";
            echo '<channel><title>'.htmlspecialchars(MAIL_USERNAME).'</title><link>http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'</link>';
            echo '<description>Latest incoming mails in the mailbox</description><language>en-en</language><copyright></copyright>'."\n\n";
         
            for ($i = $num, $j = 0; $i >= 1 && $j < 20; $i--) {
                $headerText = imap_fetchHeader($mbox, $i);
                $header = imap_rfc822_parse_headers($headerText);
                $from = $header->from;
				
				$struct = imap_fetchstructure($mbox, $i);
				$is_plain_text = false;
				$content = '';
				$charset = '';
				$title = '';
				
				if (!isset($struct->parts)) {
					$content = imap_fetchbody($mbox, $i, 1, FT_PEEK);
					$is_plain_text = true;
					
					foreach ($struct->parameters as $param) {
						if (strtolower($param->attribute) == "charset") {
							$charset = strtolower($param->value);
						}
					}
				} else {
					$content = imap_fetchbody($mbox, $i, 2, FT_PEEK);
					
					foreach ($struct->parts[0]->parameters as $param) {
						if (strtolower($param->attribute) == "charset") {
							$charset = strtolower($param->value);
						}
					}
				}
				
                $from_mail = $from[0]->mailbox.'@'.$from[0]->host;
                $date = date(DATE_RFC822, strtotime($header->date));
                $guid = $header->message_id;
				$subject = $header->subject;
				
				if ($charset == "utf-8") {
					$title = iconv_mime_decode($subject, 0, 'UTF-8');
					$content = quoted_printable_decode($content);
				} else {
					$title = utf8_encode(imap_utf8($subject));
					$content = utf8_encode(imap_utf8($content));
				}
				
				if ($is_plain_text) {
					$content = nl2br($content);
				}
                
                echo '<item><title>['.htmlspecialchars($from_mail).'] '.htmlspecialchars($title).'</title><guid isPermaLink="false">'.htmlspecialchars($guid).'</guid><pubDate>'.htmlspecialchars($date).'</pubDate>';
                echo '<description>Mail sent by '.htmlspecialchars($from_mail).' on '.htmlspecialchars($date).'</description><content:encoded><![CDATA['.$content.']]></content:encoded>'."\n</item>\n";
                
                $j++;
            }
            
            echo "</channel>\n</rss>";
        } 

         //close the stream 
        imap_close($mbox);
        ob_end_flush();
    }
?>