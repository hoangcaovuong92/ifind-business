<?php
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */

if( !function_exists('ifind_set_click_counter') ){
	function ifind_set_click_counter( $location_id, $business_id, $click_info ){
		if( ifind_is_robot() ){
			return;
		}
		$meta_key = '_ifind_click_counter';
		$data = get_post_meta($business_id, $meta_key, true);
		if (is_array($data)) {
			array_push($data, $click_info);
		}else{
			$data = array();
			array_push($data, $click_info);
		}

		if( $data ){
			$result = update_post_meta($business_id, $meta_key, $data, false);
		}
	}
}

// $ouput_type: table | array
if( !function_exists('ifind_get_click_counter') ){
	function ifind_get_click_counter( $business_id, $ouput_type = 'array', $echo = false ){
		$meta_key = '_ifind_click_counter';
		$data = get_post_meta($business_id, $meta_key, true);
		if ($ouput_type === 'table') { ?>
			<?php ob_start(); ?>
			<h2><?php printf(esc_html__( 'Statistics for %s', 'ifind' ), get_the_title($business_id)); ?></h2>
			<div class="ifind-statistics-result">
				<?php
				if (is_array($data) && count($data) > 0) { ?>
					<p><?php printf(esc_html__( 'There are %d records', 'ifind' ), count($data)); ?></p>
					<table style="width: 100%" border="1" class="ifind-table ifind-table-click-counter">
						<tr>
							<th><?php esc_html_e('Position','ifind'); ?></th>
							<th><?php esc_html_e('Time','ifind'); ?></th>
							<th><?php esc_html_e('Location','ifind'); ?></th>
							<th><?php esc_html_e('IP Address','ifind'); ?></th>
						</tr>
						<?php foreach ($data as $click_info) { ?>
							<tr>
								<td><?php echo $click_info['position'] ?></td>
								<td><?php echo ifind_convert_timestamp_to_time($click_info['timestamp']); ?></td>
								<td><?php echo get_the_title($click_info['location_id']) ?></td>
								<td><?php echo $click_info['ip_address'] ?></td>
							</tr>
						<?php } ?>
					</table>
				<?php } else { ?>
					<p><?php esc_html_e( 'No record exists!', 'ifind' ); ?></p>
				<?php } ?>
			</div>
			<?php 
			$data = ob_get_contents();
			ob_end_clean();
		}
		if( $echo ){
			echo $data;
		}
		else{
			return $data;
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