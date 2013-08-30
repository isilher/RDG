<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function carabiner_display($flag){
	$CI =& get_instance();
	$CI->load->library('carabiner');
	$CI->carabiner->display($flag);
}