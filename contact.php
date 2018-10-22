<?php
/*
Plugin Name: Contact Form Plugin
Plugin URI: http://github.com/DimkaGlamazda/wp-contact-form
Description: Simple non-bloated WordPress Contact Form
Version: 1.0
Author: Dmitrii Glamazda
Author https://github.com/DimkaGlamazda
*/

require_once 'includes/ContactFormHandler.php';

function contact_sort_code()
{
	ob_start();

	$form = new ContactFormHandler();

	$form->send();
	$form->get();

	return ob_get_clean();
}

add_shortcode( 'contact_form', 'contact_sort_code' );