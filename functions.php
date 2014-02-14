<?php
//Need to set this otherwise we get a warning.
date_default_timezone_set( 'America/New_York' );

//Includes
include_once('config/db-config.php');
include_once('lib/adodb5/adodb.inc.php');
include_once('lib/simple_html_dom.php');

function get_db() {
	// Documentation -> http://phplens.com/lens/adodb/docs-adodb.htm
	$db = NewADOConnection('mysql');
	$db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	return $db;
}

function cleanup_the_record( $val ) {
	//Need to remove stray &nbsp; characters;
	$val = str_replace('&nbsp;', '', $val);
	
	return $val;
}