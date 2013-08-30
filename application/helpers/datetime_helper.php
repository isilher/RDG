<?php
/**
 * Convert a datetime in a string that states how long ago the datetime was.
 * 
 * @param datetime $datetime
 */
function time_elapsed_string($datetime) {
	$ptime = strtotime($datetime);
	$etime = time() - $ptime;

	if ($etime < 1) {
		return '0 seconds';
	}

	$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
			30 * 24 * 60 * 60       =>  'month',
			24 * 60 * 60            =>  'day',
			60 * 60                 =>  'hour',
			60                      =>  'minute',
			1                       =>  'second'
	);

	foreach ($a as $secs => $str) {
		$d = $etime / $secs;
		if ($d >= 1) {
			$r = round($d);
			return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
		}
	}
}