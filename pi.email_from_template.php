<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================

RogEE Email-from-Template
a plug-in for ExpressionEngine 2
by Michael Rog

Please e-mail me with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com
>> http://michaelrog.com/ee

This plugin is compatible with NSM Addon Updater:
>> http://github.com/newism/nsm.addon_updater.ee_addon

=====================================================

*/

$plugin_info = array(
	'pi_name'			=> "RogEE Email-from-Template",
	'pi_version'		=> "1.4.0",
	'pi_author'			=> "Michael Rog",
	'pi_author_url'		=> "http://michaelrog.com/ee",
	'pi_description'	=> "Emails enclosed contents to a provided email address.",
	'pi_usage'			=> Email_from_template::usage()
);

/** ---------------------------------------
/**  Email_from_template class
/** ---------------------------------------*/

class Email_from_template {

	var $return_data = "";

	function Email_from_template($str = '')
	{

	    $this->EE =& get_instance() ;

		// defaults
	    
	    $this->to = $this->EE->config->item('webmaster_email');
	    $this->cc = "";
	    $this->bcc = "";
		$this->from = $this->EE->config->item('webmaster_email');
		$this->subject = "Email-from-Template: ".$this->EE->uri->uri_string();
		$this->echo_tagdata = TRUE;

		// params: fetch / sanitize / validate
		
		$to = (($to = $this->EE->TMPL->fetch_param('to')) === FALSE) ? $this->to : $this->EE->security->xss_clean($to);
		$cc = (($cc = $this->EE->TMPL->fetch_param('cc')) === FALSE) ? FALSE : $this->EE->security->xss_clean($cc);
		$bcc = (($bcc = $this->EE->TMPL->fetch_param('bcc')) === FALSE) ? FALSE : $this->EE->security->xss_clean($bcc);
		$from = (($from = $this->EE->TMPL->fetch_param('from')) === FALSE) ? $this->from : $this->EE->security->xss_clean($from);
		$subject = (($subject = $this->EE->TMPL->fetch_param('subject')) === FALSE) ? $this->subject : $subject;
		$echo_tagdata = (strtolower($this->EE->TMPL->fetch_param('echo')) == "no" || strtolower($this->EE->TMPL->fetch_param('echo')) == "off") ? FALSE : TRUE ;
		$decode_subject_entities = (strtolower($this->EE->TMPL->fetch_param('decode_subject_entities')) == "no") ? FALSE : TRUE ;
		$decode_message_entities = (strtolower($this->EE->TMPL->fetch_param('decode_message_entities')) == "no") ? FALSE : TRUE ;
		
		// fetch tag data
    
		if ($str == '')
		{
			$str = $this->EE->TMPL->tagdata ;
		}

		$tagdata = $str;
		
		// assemble and parse template variables
		
		$variables = array();
		
		$single_variables = array(
			'to' => $to,
			'cc' => $cc,
			'from' => $from,
			'subject' => $subject,
			'ip' => $this->EE->input->ip_address(),
			'httpagent' => $this->EE->input->user_agent(),
			'uri_string' => $this->EE->uri->uri_string()
		);

		$variables[] = $single_variables;

		$message = $this->EE->TMPL->parse_variables($tagdata, $variables) ;
		
		// parse global variables
		
		$subject = $this->EE->TMPL->parse_globals($subject);
		$message = $this->EE->TMPL->parse_globals($message);

		// template debugging
		
		$this->EE->TMPL->log_item('Sending email from template...');
		$this->EE->TMPL->log_item('TO: ' . $to);
		$this->EE->TMPL->log_item('CC: ' . ($cc ? $cc : '(none)'));
		$this->EE->TMPL->log_item('BCC: ' . ($bcc ? $bcc : '(none)'));
		$this->EE->TMPL->log_item('FROM: ' . $from);
		$this->EE->TMPL->log_item('SUBJECT: ' . $subject);
		if ($decode_subject_entities) { $this->EE->TMPL->log_item('Decoding HTML entities in subject...'); }
		if ($decode_message_entities) { $this->EE->TMPL->log_item('Decoding HTML entities in message...'); }		
		
		// decode HTML entities
		
		$subject = $decode_subject_entities ? html_entity_decode($subject) : $subject;
		$message = $decode_message_entities ? html_entity_decode($message) : $message;

		// mail the message
				
		$this->EE->load->library('email');
		$this->EE->email->initialize() ;
		$this->EE->email->from($from);
		$this->EE->email->to($to); 
		$this->EE->email->cc($cc);
		$this->EE->email->bcc($bcc);
		$this->EE->email->subject($subject);
		$this->EE->email->message($message);
		$this->EE->email->Send();

		// more template debugging

		$this->EE->TMPL->log_item('Email sent!');
		
		if (! $echo_tagdata) { $this->EE->TMPL->log_item('Echo is off. Outputting nothing to template.'); }
		else { $this->EE->TMPL->log_item('Echo is on. Repeating message to template.'); }
		
		// return data to template
		
		$this->return_data = ($echo_tagdata) ? $message : "" ;

	} // END Email_from_template() constructor

	/** ----------------------------------------
	/**  Plugin Usage
	/** ----------------------------------------*/
	
	function usage()
	{
	
		ob_start(); 
		?>
	
		This plugin emails the enclosed content to a provided email address.
		
		PARAMETERS:
		
		to - destination email address (default: site webmaster)
		cc - email addresses to carbon copy
		bcc - email addresses to blind carbon copy
		from - sender email address (default: site webmaster)
		subject - email subject line (default: template URI)
		echo - Set to "off" if you don't want to display the tag contents in the template.
		decode_subject_entities - Set to "no" if you don't want to parse the HTML entities in the subject line.
		decode_message_entities - Set to "no" if you don't want to parse the HTML entities in the message text.
		
		VARIABLES:
		
		{to}
		{from}
		{subject}
		{ip}
		{httpagent}
		{uri_string}
		
		EXAMPLE USAGE:
		
		{exp:email_from_template to="admin@ee.com" from="server@ee.com" subject="Hello!" echo="off"}

			This tag content is being viewed at {uri_string}. Sending notification to {to}!

		{/exp:email_from_template}	
	
		USING WITH OTHER PLUGINS AND TAGS:
		
		When you want to email the output of other tags, put Email_from_Template INSIDE the other tag and use parse="inward" on the outer tags.
	
		<?php
		$buffer = ob_get_contents();
		
		ob_end_clean(); 
	
		return $buffer;
	
	} // END usage()

} // END class Email-from-template

/* End of file pi.email-from-template.php */ 
/* Location: ./system/expressionengine/third_party/email-from-template/pi.email-from-template.php */
