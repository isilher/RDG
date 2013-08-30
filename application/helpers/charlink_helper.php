<?php

function charlink($character)
{
	$charname = $character->name;
	$charname = str_replace(' ', '_', $charname);
	
	return $charname . '-' . $character->unique;
}