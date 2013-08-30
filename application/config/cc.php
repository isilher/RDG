<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  Calculated modifiers
| -------------------------------------------------------------------
|
*/

/*
| -------------------------------------------------------------------
|  Stats to be determined on calling Character::calc()
| -------------------------------------------------------------------
|
*/

$config['cc_stats'] = array(
			'str', 
			'dex', 
			'con', 
			'int', 
			'wis', 
			'cha', 
			'alignment_morality', 
			'alignment_lawfulness', 
			'age', 
			'height', 
			'weight', 
			'eyes', 
			'hair', 
			'deity', 
			'homeland',
			'gender', 
			'race',
			'size',
			'hp_current'
		);

/*
| -------------------------------------------------------------------
|  All bonustypes
| -------------------------------------------------------------------
|
*/

$config['bonustypes'] = array(
		'alchemical',
		'armor',
		'circumstance',
		'deflection',
		'dodge',
		'enhancement',
		'insight',
		'luck',
		'morale',
		'natural armor',
		'profane',
		'racial',
		'resistance',
		'sacred',
		'shield',
		'size',
		'trait',
		'untyped'
		);

/* End of file cc.php */
/* Location: ./application/config/cc.php */