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
0.3 - alpha

=====================================================

*/

$plugin_info = array(
						'pi_name'			=> "RogEE Email-from-Template",
						'pi_version'		=> "0.3",
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

	function Email_from_template($str = '')
	{

	    $this->EE =& get_instance() ;

		/** ---------------------------------------
		/**  defaults
		/** ---------------------------------------*/
	    
	    $this->to = $this->EE->config->item('webmaster_email') ; 
		$this->from = $this->EE->config->item('webmaster_email') ;
		$this->subject = "email from ".$this->EE->uri->uri_string() ;
		$this->echo_tagdata = TRUE ;

		/** ---------------------------------------
		/**  params: fetch / sanitize / validate
		/** ---------------------------------------*/
		
		$to = (($to = $this->EE->TMPL->fetch_param('to')) === FALSE) ? $this->to : $this->EE->security->xss_clean($to);
		$from = (($from = $this->EE->TMPL->fetch_param('from')) === FALSE) ? $this->from : $this->EE->security->xss_clean($from);
		$subject = (($subject = $this->EE->TMPL->fetch_param('subject')) === FALSE) ? $this->subject : $this->EE->security->xss_clean($subject);
		$echo_tagdata = (strtolower($this->EE->TMPL->fetch_param('echo')) == "no") ? FALSE : $this->echo_tagdata ;
		
		/** ---------------------------------------
		/**  tag data: fetch / sanitize
		/** ---------------------------------------*/
    
		if ($str == '')
		{
			$str = $this->EE->TMPL->tagdata ;
		}
    
   		$tagdata = $this->EE->security->xss_clean($str) ;
		
		/** ---------------------------------------
		/**  assemble variables
		/** ---------------------------------------*/
		
		$variables = array();
		
		$single_variables = array(
			'to' => $to,
			'from' => $from,
			'subject' => $subject,
			'ip' => $this->EE->input->ip_address(),
			'httpagent' => $this->EE->input->user_agent(),
			'uri_string' => $this->EE->uri->uri_string()
		);
		
		$variables[] = $single_variables ;
		
		/** ---------------------------------------
		/**  format data & mail the message
		/** ---------------------------------------*/
		
		$message = $this->EE->TMPL->parse_variables($tagdata, $variables) ;

		$from_header = "From: $from\r\n";
		
		mail($to, $subject, $message, $from_header);
				
		/** ---------------------------------------
		/**  return data to template
		/** ---------------------------------------*/
		
		$this->return_data = ($echo_tagdata) ? $message : "" ;

	}

	/** ----------------------------------------
	/**  Plugin Usage
	/** ----------------------------------------*/
	function usage()
	{
	ob_start(); 
	?>

	This plugin emails the enclosed content to a provided email address.
	
	Parameters:
	
	to - destination email address (defaults to site webmaster)
	from - sender email address (defaults to site webmaster)
	subject - email subject line (defaults to template URI)
	echo - Set to "off" if you don't want to display the tag contents in the template.
	
	Variables:
	
	{to}
	{from}
	{subject}
	{ip}
	{httpagent}
	{uri_string}
	
	Example usage:
	
	{exp:email-from-template to="admin@ee.com" from="server@ee.com" subject="Hello!" echo="off"}

		This tag content is being viewed at {ip} by {httpagent}. Sending notification to {to}.

	{/exp:email-from-template}	

	<?php
	$buffer = ob_get_contents();
	
	ob_end_clean(); 

	return $buffer;
	}

} // END class Email-from-template

/* End of file pi.email-from-template.php */ 
/* Location: ./system/expressionengine/third_party/email-from-template/pi.email-from-template.php */
