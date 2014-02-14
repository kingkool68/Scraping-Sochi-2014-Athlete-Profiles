<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Scraping Sochi 2014 Athlete Bios</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>

<body>
	<h1>Scrape for the gold&hellip;</h1>
	<p>URLs to download: <strong id="to-download"></strong></p>
	<p>Scraped: <strong id="scraped"></strong>, <em id="percent-complete"></em></p>
	<p></p>
<script>
jQuery(document).ready(function($){
	//Get a list of the athlete URLs we're going to scrape.
	var urls = [];
	var i = 1;
	$.get('athlete-bio-links.txt', function( data ) {
		urls = data.split(/\n/);
		$('#to-download').text( urls.length );
		process();
	});
	
	function process() {
		if( i <= urls.length ) {
			var url = urls[i];
			$.get( 'scrape-athlete-bio.php', { url: url }, function() {
				$('#scraped').text( i );
				$('#percent-complete').text( (i/urls.length * 100).toFixed(0) + '% complete' );
				i++;
				process();
			});
			
		}
	}
});
</script>
</body>
</html>