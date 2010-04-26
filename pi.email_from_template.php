<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================

RogEE Email-from-Template
a plug-in for ExpressionEngine 2
by Michael Rog
v0.1

email Michael with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com

changelog:
0.2 - alpha

=====================================================

*/

$plugin_info = array(
						'pi_name'			=> "RogEE Email-from-Template",
						'pi_version'		=> "0.2",
						'pi_author'			=> "Michael Rog",
						'pi_author_url'		=> "http://michaelrog.com/go/ee",
						'pi_description'	=> "Emails enclosed contents to a provided email address.",
						'pi_usage'			=> Email_from_template::usage()
					);


/**
 * Email_from_template Class
 *
 */

class Email_from_template {

	var $return_data = "";
	
	var $to = "2010@michaelrog.com" ; 
	var $from = "test@michaelrog.com" ;
	var $subject = "Email-from-Template" ;

	var $echo = "y" ;

	function Email_from_template($str = '')
	{

	    $this->EE =& get_instance() ;

		/** ---------------------------------------
		/**  params: fetch / validate / sanitize
		/** ---------------------------------------*/
		
		$this->to = (($to = $this->EE->TMPL->fetch_param('to')) === FALSE) ? $this->to : $to;
		$this->from = (($from = $this->EE->TMPL->fetch_param('from')) === FALSE) ? $this->from : $from;
		$this->subject = (($subject = $this->EE->TMPL->fetch_param('subject')) === FALSE) ? $this->subject : $subject;
		$this->echo = (($echo = $this->EE->TMPL->fetch_param('echo')) === FALSE) ? $this->echo : $echo;

		/*
		
		$to = $this->EE->security->xss_clean($this->to) ;
		$from = $this->EE->security->xss_clean($this->from) ;
		$subject = $this->EE->security->xss_clean($this->subject) ;
		
		$echo = strtolower($this->EE->security->xss_clean($echo)) ;
		$valid_echo = array('y', 'n');
		$echo = (in_array($echo, $valid_foo)) ? $foo : '';
		
		*/

		/** ---------------------------------------
		/**  tag data: fetch / sanitize
		/** ---------------------------------------*/
    
		if ($str == '')
		{
			$str = $this->EE->TMPL->tagdata ;
		}
    
   		$tagdata = $this->EE->security->xss_clean($str) ;
		
		/** ---------------------------------------
		/**  Assemble variables
		/** ---------------------------------------*/		
		
		echo $to . " " . $from . " " . $subject . " " . $ip . " " . $httpagent . " " . $tagdata ;
		
		$vars = array(
			'to' => $to,
			'from' => $from,
			'subject' => $subject,
			'ip' => $this->EE->input->ip_address(), // getenv("REMOTE_ADDR"),
			'httpagent' => $this->EE->input->user_agent() // getenv("HTTP_USER_AGENT")
		);
		
		// format message for mailing
		
		$message = $tagdata ;
		
		$from_header = "From: $from\r\n";
		
		// mail it
		
		mail($this->to, $this->subject, $message, $from_header);
		
		// echo enclosed contents to template (?)
		
				
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
