There is lots of data about the 2014 Winter Olympic athletes available at http://www.sochi2014.com/en/athletes-search but it's not in a standardized format that is easy to work with. This scraper parses the athlete bio pages and stores the data in a simple database for better analysis. 

## I Just Want The Data ##
Check the `final data` folder for a `csv`, `json`, and a `sql dump` of the data.

## How To Run the Scraper ##
0. Clone this repo
0. Set-up a MySQL database
0. Edit `/config/db-config.php` with your database credentials
0. Run the `create-tables.sql` file to create the table structure for storing the data in the database
0. Run the `index.php` file by visiting it in your browser

## Other Files ##
### athlete-bio-links.txt ###
This is a list of URLs to all of the athlete bios from Sochi's search engine. 

I got this list by running the following JavaScript in Chrome's Dev Tools console. When it's done it copies a list of URLs separated by commas to your clipboard. 

```javascript
	jQuery(document).ready(function($){
		var iterations = 0;
		var output = '';
		
		function pushTheMoreButton() {
			if( iterations < 191 ) {
				$('#show-more-button a').trigger('click');
				iterations++;
			} else {
				window.clearInterval(intervalID);
				alert( 'all done!' );
				
				$('.athletes .athlete a').each( function() {
					output+= this.href + ', ';
				});
				copy(output);
			}
		}
		intervalID = window.setInterval(pushTheMoreButton, 500);
	});
```

### athletes-search-full.html ###
This is the full web page with all of the athlete links visible. Use this if you don't want to wait for the script above to click the "More" button 191 times.

Uses http://adodb.sourceforge.net/ and http://simplehtmldom.sourceforge.net/

Enjoy!
