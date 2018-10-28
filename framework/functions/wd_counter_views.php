<?php
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */

if( !function_exists('ifind_get_click_counter') ){
	function ifind_get_click_counter( $post_id = 0, $echo = true ){
		global $post;
		if( !$post_id && isset($post->ID) ){
			$post_id = $post->ID;
		}

		$count_key = '_ifind_click_counter';
		$count = get_post_meta($post_id, $count_key, true);
		if( $count == '' ){
			delete_post_meta($post_id, $count_key);
			add_post_meta($post_id, $count_key, '0');
			$count = '0';
		}

		if( $echo ){
			echo esc_html($count);
		}
		else{
			return $count;
		}
	}
}

add_action('wp_ajax_nopriv_set_click_counter', 'ifind_set_click_counter');
add_action('wp_ajax_set_click_counter', 'ifind_set_click_counter');
if( !function_exists('ifind_set_click_counter') ){
	function ifind_set_click_counter( $post_id = 0, $banner_position ){
		global $post;
		if( !$post_id ){
			$post_id = $post->ID;
		}	
		
		if( ifind_is_robot() ){
			return;
		}
		
		$count_key = '_ifind_click_counter';
		$count = get_post_meta($post_id, $count_key, true);
		if( $count == '' ){
			$count = 0;
			delete_post_meta($post_id, $count_key);
			add_post_meta($post_id, $count_key, '1');
		}else{
			$count++;
			update_post_meta($post_id, $count_key, $count,false);
		}
	}
}

if( !function_exists('ifind_is_robot') ){
	function ifind_is_robot(){
		if(!isset($_SERVER['HTTP_USER_AGENT']) || (isset($_SERVER['HTTP_USER_AGENT']) && trim($_SERVER['HTTP_USER_AGENT']) === ''))
			return false;

		$robots = array(
			'bot', 'b0t', 'Acme.Spider', 'Ahoy! The Homepage Finder', 'Alkaline', 'Anthill', 'Walhello appie', 'Arachnophilia', 'Arale', 'Araneo', 'ArchitextSpider', 'Aretha', 'ARIADNE', 'arks', 'AskJeeves', 'ASpider (Associative Spider)', 'ATN Worldwide', 'AURESYS', 'BackRub', 'Bay Spider', 'Big Brother', 'Bjaaland', 'BlackWidow', 'Die Blinde Kuh', 'Bloodhound', 'BSpider', 'CACTVS Chemistry Spider', 'Calif', 'Cassandra', 'Digimarc Marcspider/CGI', 'ChristCrawler.com', 'churl', 'cIeNcIaFiCcIoN.nEt', 'CMC/0.01', 'Collective', 'Combine System', 'Web Core / Roots', 'Cusco', 'CyberSpyder Link Test', 'CydralSpider', 'Desert Realm Spider', 'DeWeb(c) Katalog/Index', 'DienstSpider', 'Digger', 'Direct Hit Grabber', 'DownLoad Express', 'DWCP (Dridus\' Web Cataloging Project)', 'e-collector', 'EbiNess', 'Emacs-w3 Search Engine', 'ananzi', 'esculapio', 'Esther', 'Evliya Celebi', 'FastCrawler', 'Felix IDE', 'Wild Ferret Web Hopper #1, #2, #3', 'FetchRover', 'fido', 'KIT-Fireball', 'Fish search', 'Fouineur', 'Freecrawl', 'FunnelWeb', 'gammaSpider, FocusedCrawler', 'gazz', 'GCreep', 'GetURL', 'Golem', 'Grapnel/0.01 Experiment', 'Griffon', 'Gromit', 'Northern Light Gulliver', 'Harvest', 'havIndex', 'HI (HTML Index) Search', 'Hometown Spider Pro', 'ht://Dig', 'HTMLgobble', 'Hyper-Decontextualizer', 'IBM_Planetwide', 'Popular Iconoclast', 'Ingrid', 'Imagelock', 'IncyWincy', 'Informant', 'Infoseek Sidewinder', 'InfoSpiders', 'Inspector Web', 'IntelliAgent', 'Iron33', 'Israeli-search', 'JavaBee', 'JCrawler', 'Jeeves', 'JumpStation', 'image.kapsi.net', 'Katipo', 'KDD-Explorer', 'Kilroy', 'LabelGrabber', 'larbin', 'legs', 'Link Validator', 'LinkScan', 'LinkWalker', 'Lockon', 'logo.gif Crawler', 'Lycos', 'Mac WWWWorm', 'Magpie', 'marvin/infoseek', 'Mattie', 'MediaFox', 'MerzScope', 'NEC-MeshExplorer', 'MindCrawler', 'mnoGoSearch search engine software', 'moget', 'MOMspider', 'Monster', 'Motor', 'Muncher', 'Muninn', 'Muscat Ferret', 'Mwd.Search', 'Internet Shinchakubin', 'NDSpider', 'Nederland.zoek', 'NetCarta WebMap Engine', 'NetMechanic', 'NetScoop', 'newscan-online', 'NHSE Web Forager', 'Nomad', 'nzexplorer', 'ObjectsSearch', 'Occam', 'HKU WWW Octopus', 'OntoSpider', 'Openfind data gatherer', 'Orb Search', 'Pack Rat', 'PageBoy', 'ParaSite', 'Patric', 'pegasus', 'The Peregrinator', 'PerlCrawler 1.0', 'Phantom', 'PhpDig', 'PiltdownMan', 'Pioneer', 'html_analyzer', 'Portal Juice Spider', 'PGP Key Agent', 'PlumtreeWebAccessor', 'Poppi', 'PortalB Spider', 'GetterroboPlus Puu', 'Raven Search', 'RBSE Spider', 'RoadHouse Crawling System', 'ComputingSite Robi/1.0', 'RoboCrawl Spider', 'RoboFox', 'Robozilla', 'RuLeS', 'Scooter', 'Sleek', 'Search.Aus-AU.COM', 'SearchProcess', 'Senrigan', 'SG-Scout', 'ShagSeeker', 'Shai\'Hulud', 'Sift', 'Site Valet', 'SiteTech-Rover', 'Skymob.com', 'SLCrawler', 'Inktomi Slurp', 'Smart Spider', 'Snooper', 'Spanner', 'Speedy Spider', 'spider_monkey', 'Spiderline Crawler', 'SpiderMan', 'SpiderView(tm)', 'Site Searcher', 'Suke', 'suntek search engine', 'Sven', 'Sygol', 'TACH Black Widow', 'Tarantula', 'tarspider', 'Templeton', 'TeomaTechnologies', 'TITAN', 'TitIn', 'TLSpider', 'UCSD Crawl', 'UdmSearch', 'URL Check', 'URL Spider Pro', 'Valkyrie', 'Verticrawl', 'Victoria', 'vision-search', 'Voyager', 'W3M2', 'WallPaper (alias crawlpaper)', 'the World Wide Web Wanderer', 'w@pSpider by wap4.com', 'WebBandit Web Spider', 'WebCatcher', 'WebCopy', 'webfetcher', 'Webinator', 'weblayers', 'WebLinker', 'WebMirror', 'The Web Moose', 'WebQuest', 'Digimarc MarcSpider', 'WebReaper', 'webs', 'Websnarf', 'WebSpider', 'WebVac', 'webwalk', 'WebWalker', 'WebWatch', 'Wget', 'whatUseek Winona', 'Wired Digital', 'Weblog Monitor', 'w3mir', 'WebStolperer', 'The Web Wombat', 'The World Wide Web Worm', 'WWWC Ver 0.2.5', 'WebZinger', 'XGET'
		);

		foreach($robots as $robot)
		{
			if(stripos($_SERVER['HTTP_USER_AGENT'], $robot) !== false)
				return true;
		}

		return false;
	}
}

add_action('deleted_post', 'ifind_delete_click_counter');
if( !function_exists('ifind_delete_click_counter') ){
	function ifind_delete_click_counter( $post_id ){
		$count_key = '_ifind_click_counter';
		delete_post_meta($post_id, $count_key);
	}
}