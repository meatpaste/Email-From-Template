<?php

/*
=====================================================

RogEE Email-from-Template
a plug-in for ExpressionEngine 2
by Michael Rog
v0.1

email Michael with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com

changelog:
0.1 - alpha

=====================================================

*/


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> "RogEE Email-from-Template",
						'pi_version'		=> "0.1",
						'pi_author'			=> "Michael Rog",
						'pi_author_url'		=> "http://michaelrog.com/go/ee",
						'pi_description'	=> "Emails enclosed contents to a provided email address.",
						'pi_usage'			=> Email_from_template::usage()
					);

class Email_from_template {

var $return_data = "";

  function Email_from_template($str = '')
  {

    $this->EE =& get_instance();
    
    if ($str == '')
    {
      $str = $this->EE->TMPL->tagdata;
    }
    
	// assemble variables
	
	$todayis = date("l, F j, Y, g:i a") ;
	
	$content = "test content." ; // stripcslashes($str) ;
	$to = "2010@michaelrog.com" ; // stripcslashes($to) ;
	$from = "test@michaelrog.com" ; // stripcslashes($from) ;
	$subject = "test" ; // stripcslashes($subject);
	
	$ip = getenv("REMOTE_ADDR") ;
	$httpref = getenv ("HTTP_REFERER") ;
	$httpagent = getenv ("HTTP_USER_AGENT") ;
	
	// format message for mailing
	
	$message = "$todayis \n \n
	content: $content \n \n
	IP = $ip \n
	Browser Info: $httpagent \n
	Referral : $httpref \n
	";

	$from_header = "From: $from\r\n";
	
	// mail it
	
	mail($to, $subject, $message, $from_header);
    
    // echo enclosed contents to template
    
    $this->return_data = $str;

	}

	/** ----------------------------------------
	/**  Plugin Usage
	/** ----------------------------------------*/
	function usage()
	{
	ob_start(); 
	?>

	This plugin emails the enclosed content to a provided email address.

	<?php
	$buffer = ob_get_contents();
	
	ob_end_clean(); 

	return $buffer;
	}

} // END class Email-from-template

/* End of file pi.email-from-template.php */ 
/* Location: ./system/expressionengine/third_party/email-from-template/pi.email-from-template.php */
