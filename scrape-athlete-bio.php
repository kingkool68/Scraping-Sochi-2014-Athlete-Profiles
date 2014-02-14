<?php
include( 'functions.php' );
$url = $_GET['url'];

$html = file_get_html($url);
$bio = $html->find( '.athletedetail', 0 );

/***
 * Medals
 ***/

$medals_gold = $bio->find( '.medalslist .gold span', 0 )->plaintext;
$medals_silver = $bio->find( '.medalslist .silver span', 0 )->plaintext;
$medals_bronze = $bio->find( '.medalslist .bronze span', 0 )->plaintext;
$medals_total = $bio->find( '.medalslist .noskew span', 0 )->plaintext;
$medals_total = trim( str_replace('Total:', '', $medals_total) );



/***
 * Name
 ***/ 
 
$name = $bio->find( 'h2',0 )->plaintext;
$name_parts = explode( ' ', $name );

$first_name = $name_parts[0];
$last_name = '';

if( count($name_parts) <= 2 ) {
	$last_name = ucfirst( strtolower($name_parts[1]) );
} else {
	for( $i = 1; $i < count($name_parts); $i++ ) {
		$name_part = $name_parts[$i];
	
		//If the name part is all uppercase then it is part of their last name.
		if( $name_part == strtoupper( $name_part ) ) {
			$last_name .= ' ' . ucfirst( strtolower( $name_part ) );
		} else {
			$first_name .= ' ' . ucfirst( strtolower( $name_part ) );
		}
	}
	
	// Hyphens surrounded by spaces are weird...
	$first_name = str_replace(' - ', '-', $first_name);
	$last_name = str_replace(' - ', '-', $last_name);
}


/***
 * Details
 ***/ 
 
$photo = $bio->find( 'img.photo', 0 )->src;
$country = $bio->find( '.country', 0 )->plaintext;

$sports = array();
foreach( $bio->find( '.sportlist a' ) as $sport ) {
	$sports[] = $sport->innertext;
}
$sports = implode( ', ', $sports );


foreach( $bio->find( '.table li' ) as $count => $row ) {
	//Remove the <span> which holds the label leaving only the value as the text inside of the <li>. Yay!
	$row->find( 'span', 0 )->outertext = '';
	$val = cleanup_the_record( $row->innertext );
	
	echo $count . ' : ' . $val . "<br>";
	
	switch( $count ) {
		case 0:
			$gender = 'Female';
			if( strstr( $val, 'Male' ) ) {
				$gender = 'Male';
			}
		break;
		
		case 1:
			$nationality = $val;
		break;
		
		case 2:
			$date = strtotime( $val );
			$birth_date = date( 'Y-m-d', $date );
			$birth_year = date( 'Y', $date );
			$birth_month = date( 'n', $date );
			$birth_day = date( 'j', $date );
		break;
		
		case 3:
			$age = $val;
		break;
		
		case 4:
			$height_parts = explode( 'm', $val );
			array_map('trim', $height_parts);
			
			$height_meters = $height_parts[0];
	
			//Removes ' (feet) and " (inches) which are encoded and mess things up when searching for the numbers.
			$height_parts[1] = str_replace('&#39;', '', $height_parts[1]);
			preg_match_all('/\d+/', $height_parts[1], $matches);
			$feet = intval($matches[0][0]);
			$inches = intval($matches[0][1]);
			$height_inches = ($feet * 12) + $inches;
		break;
		
		case 5:
			preg_match_all('/\d+/', $val, $matches);
			$weight_kg = intval( $matches[0][0] );
			$weight_lbs = intval( $matches[0][1] );
		break;
		
		case 6:
			$birth_place = $val;
		break;
		
		case 7:
			$residence = $val;
		break;
	}
}

/***
 * Save the Results to the DB
 ***/

//Documentation -> http://phplens.com/lens/adodb/docs-adodb.htm
$db = get_db();
$record = array(
	'sport' => $sports,
	'first_name' => $first_name,
	'last_name' => $last_name,
	'age' => $age,
	'gender' => $gender,
	'birth_place' => $birth_place,
	'birth_date' => $birth_date,
	'birth_year' => $birth_year,
	'birth_month' => $birth_month,
	'birth_day' => $birth_day,
	'height_meters' => $height_meters,
	'height_inches' => $height_inches,
	'weight_kg' => $weight_kg,
	'weight_lbs' => $weight_lbs,
	'team_country' => $country,
	'residence' => $residence,
	'nationality' => $nationality,
	'medals_total' => $medals_total,
	'medals_gold' => $medals_gold,
	'medals_silver' => $medals_silver,
	'medals_bronze' => $medals_bronze,
	'photo' => $photo,
	'url' => $url
);
var_dump( $record );
$record = array_map('cleanup_the_record', $record);
var_dump( $record );

$action = 'INSERT';
$where = false;
$table = '`athlete-bios`';
$db->SetFetchMode(ADODB_FETCH_ASSOC);

$already_exists = $db->Execute('SELECT `url` FROM ' . $table . ' WHERE `url` = "' . $url . '"');

if( $already_exists->fields ) {
	$action = 'UPDATE';
	$where = '`url` = "' . $url . '"';	
}

$inserted = $db->AutoExecute($table, $record, $action, $where);

if( !$inserted ) {
	echo $db->ErrorMsg();
}