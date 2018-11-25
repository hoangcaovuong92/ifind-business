<?php
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */

// Reference the Dompdf namespace
use Dompdf\Dompdf;

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

if(!function_exists ('ifind_save_pdf_file')){
	function ifind_save_pdf_file($attachment_content){
		// Notication: Add to begin of this file : use Dompdf\Dompdf;
		// Instantiate and use the dompdf class
		$dompdf = new Dompdf();
		
		// Load content from html file
		$attachment_content = ifind_sanitize_html_content($attachment_content);
		$dompdf->loadHtml($attachment_content, 'UTF-8');

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		//$dompdf->setPaper('A4', 'landscape');
		$dompdf->set_option('defaultMediaType', 'all');
		$dompdf->set_option('isFontSubsettingEnabled', true);

		// Courier (Normal, Bold, Oblique, and BoldOblique variants)
		// Helvetica (Normal, Bold, Oblique, and BoldOblique variants)
		// Times (Normal, Bold, Oblique, and BoldOblique variants)
		// Symbol
		// ZapfDingbats
		$dompdf->set_option('defaultFont', 'Helvetica'); 

		// Render the HTML as PDF
		$dompdf->render();

		//File Path
		$pdfroot  = ABSPATH.'/pdf_file/';

		//Create folder if not exists
		if (!is_dir($pdfroot)) {
			mkdir($pdfroot, 0777, true);
		}

		//File name
		$pdfroot .= 'report_'.date("F j,Y_G-i-s").'.pdf';

		//Create file if not exists
		if (!is_file($pdfroot)) {}

		//Download file
		//$dompdf->stream('title.pdf');

		$pdf_string = $dompdf->output();

		//Write pdf file - return url of file if success
		return file_put_contents($pdfroot, $pdf_string) ? $pdfroot : false;
	}
}

if(!function_exists ('ifind_get_list_statictis_detail')){
	function ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to){
		$meta_key = '_ifind_click_counter';
		$data = get_post_meta($business_id, $meta_key, true);
		$one_day_timestamp = (3600 * 24) - 1;
		$from_timestamp = ($datepicker_from) ? date_format(date_create($datepicker_from), 'U') : '';
		$to_timestamp = ($datepicker_to) ? date_format(date_create($datepicker_to), 'U') + $one_day_timestamp : '';
		// var_dump($from_timestamp);
		// var_dump($to_timestamp);

		// if only from date is set
		if ($from_timestamp && !$to_timestamp){
			$to_timestamp = $from_timestamp + $one_day_timestamp;
		}

		// if only to date is set
		if (!$from_timestamp && $to_timestamp){
			$from_timestamp = $to_timestamp - $one_day_timestamp;
		}

		$summary_data = array(
			'all' => array(
				'display' => false,
				'count' => 0,
				'first' => '',
				'last' => '',
			),
			'small-slider' => array(
				'display' => true,
				'count' => 0,
				'first' => '',
				'last' => '',
			),
			'logo' => array(
				'display' => true,
				'count' => 0,
				'first' => '',
				'last' => '',
			),
			'footer-slider' => array(
				'display' => true,
				'count' => 0,
				'first' => '',
				'last' => '',
			),
		);

		$detail_data = array();
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $click_info) {
				if (!$from_timestamp || !$to_timestamp 
					|| ($from_timestamp <= $click_info['timestamp'] && $click_info['timestamp'] < $to_timestamp) ) {
					
					// Set data for each position
					$summary_data['all']['count'] += 1; 
					$summary_data['all']['first'] = !($summary_data['all']['first']) 
																	  ? ifind_convert_timestamp_to_time($click_info['timestamp']) 
																	  : $summary_data['all']['first'];
					$summary_data['all']['last'] = ifind_convert_timestamp_to_time($click_info['timestamp']);

					$summary_data[$click_info['position']]['count'] += 1; 
					$summary_data[$click_info['position']]['first'] = !($summary_data[$click_info['position']]['first']) 
																	  ? ifind_convert_timestamp_to_time($click_info['timestamp']) 
																	  : $summary_data[$click_info['position']]['first'];
					$summary_data[$click_info['position']]['last'] = ifind_convert_timestamp_to_time($click_info['timestamp']);

					$detail_data[] = $click_info;
				}
			}
		}

		$summary_data['all']['count'] = count($detail_data);

		// Filter data
		$info_data = array();
		$info_data['business_name'] = get_the_title($business_id);
		$info_data['time_start'] = ifind_convert_timestamp_to_time($from_timestamp);
		$info_data['time_end'] = ifind_convert_timestamp_to_time($to_timestamp);
		$info_data['click_count'] = count($detail_data);

		$fitered_data = array();
		$fitered_data['overview'] = $info_data;
		$fitered_data['summary'] = $summary_data;
		$fitered_data['detail'] = $detail_data;

		return $fitered_data;
	}
}

if(!function_exists ('ifind_get_list_location_statictis_general')){
	function ifind_get_list_location_statictis_general($business_id, $datepicker_from, $datepicker_to){
		$meta_key = '_ifind_click_counter';
		$data = get_post_meta($business_id, $meta_key, true);
		$fitered_data = array();
		$from_timestamp = ($datepicker_from) ? date_format(date_create($datepicker_from), 'U') : '';
		$to_timestamp = ($datepicker_to) ? date_format(date_create($datepicker_to), 'U') + (3600 * 24) : '';
		// var_dump($from_timestamp);
		// var_dump($to_timestamp);

		// if only from date is set
		if ($from_timestamp && !$to_timestamp){
			$to_timestamp = $from_timestamp + (3600 * 24);
		}

		// if only to date is set
		if (!$from_timestamp && $to_timestamp){
			$from_timestamp = $to_timestamp - (3600 * 24);
		}

		// Filter data
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $click_info) {
				// var_dump($click_info['timestamp'] . ' - ' .ifind_convert_timestamp_to_time($click_info['timestamp']));
				if (!$from_timestamp || !$to_timestamp || ($from_timestamp <= $click_info['timestamp'] && $click_info['timestamp'] < $to_timestamp) ) {
					$fitered_data[] = $click_info;
				}
			}
		}
		$fitered_data = array();
		return $fitered_data;
	}
}

// $ouput_type: table | pdf | array
if( !function_exists('ifind_get_table_statistics') ){
	function ifind_get_table_statistics($location_id, $business_id, $datepicker_from = '', $datepicker_to = '', $ouput_type = 'array', $echo = false ){
		$statistics_overview = ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to)['overview'];
		$statistics_summary = ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to)['summary'];
		$statistics_detail = ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to)['detail'];

		$statistics_general = ifind_get_list_location_statictis_general($business_id, $datepicker_from, $datepicker_to);
		//var_dump(ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to));
		if ($ouput_type === 'table' || $ouput_type === 'pdf') { ?>
			<?php ob_start(); ?>
			<?php if ($ouput_type === 'pdf'){ ?>
				<style type="text/css">
					#ifind-business-statistics-wrap{
						border: 1px solid #ddd;
						border-radius: 5px;
						padding: 8px;
						background: #fff;
					}
					.ifind-statistics-result{
						overflow-x:auto;
					}
					.ifind-statistics-result-title{
						text-align: center;
						margin: 10px 0;
					}
					
					.ifind-statistics-result-desc{
						font-style: italic;
						margin: 10px 0;
					}

					table.ifind-table{
						border-collapse: collapse;
						border-spacing: 0;
						width: 100%;
    					table-layout: fixed;
					}
					.ifind-table-business-overview tr td:first-child{
						font-weight: bold;
						text-align: right;
					}
					table.ifind-table th{
						border: 1px solid #ddd;
						text-align: left;
						background-color: #4CAF50;
						border: 1px solid #ddd;
						color: white;
					}
					table.ifind-table td{
						border: 1px solid #ddd;
					}
					.ifind-statistics-item-no{
						font-weight: bold;
					}
					table.ifind-table tr:nth-child(even){background-color: #F1F1F1;}

					table.ifind-table tr:hover {background-color: #ddd;}

					.ifind-statistics-result-title{
						font-size: 20px;
					}

					.ifind-statistics-result-desc{
						font-size: 12px;
					}

					.ifind-statistics-item-title{
						font-size: 15px;
						margin: 20px 0 10px;
						text-decoration: underline;
					}

					table.ifind-table{
						font-size: 10px;
					}
					table.ifind-table th{
						padding: 4px;
					}
					table.ifind-table td{
						padding: 4px;
					}
				</style>
			<?php } ?>

			<div id="ifind-business-statistics-wrap">
				<h2 class="ifind-statistics-result-title">
					<?php 
					printf(esc_html__( 'Statistics for %s', 'ifind' ), get_the_title($business_id));
					if ($datepicker_from && $datepicker_to && $datepicker_from !== $datepicker_to) {
						printf(esc_html__( ' from %s to %s', 'ifind' ), $datepicker_from, $datepicker_to);
					}
					if ($datepicker_from && $datepicker_to && $datepicker_from === $datepicker_to) {
						printf(esc_html__( ' on %s', 'ifind' ), $datepicker_from);
					}
					if ($datepicker_from !== '' && $datepicker_to === '') {
						printf(esc_html__( ' on %s', 'ifind' ), $datepicker_from);
					}
					if ($datepicker_from === '' && $datepicker_to !== '') {
						printf(esc_html__( ' on %s', 'ifind' ), $datepicker_to);
					}
					?>
				</h2>

				<div class="ifind-statistics-result">
					
					<h3 class="ifind-statistics-item-title"><?php esc_html_e('I. OVERVIEW','ifind'); ?></h3>
					<table border="1" class="ifind-table ifind-table-business-overview">
						<tr>
							<td><?php esc_html_e('Business Name:','ifind'); ?></td>
							<td><?php echo $statistics_overview['business_name']; ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Start time:','ifind'); ?></td>
							<td><?php echo $statistics_overview['time_start']; ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e('End time:','ifind'); ?></td>
							<td><?php echo $statistics_overview['time_end']; ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e('Total number of clicks:','ifind'); ?></td>
							<td><?php echo $statistics_overview['click_count']; ?></td>
						</tr>
					</table>

					<h3 class="ifind-statistics-item-title"><?php esc_html_e('II. GENERAL STATISTICS','ifind'); ?></h3>
					<?php if ($statistics_overview['click_count'] > 0) {
						$all_click_count = $statistics_summary['all']['count'];
						$i = 1; ?>
						<table border="1" class="ifind-table ifind-table-list-business">
							<tr>
								<th><?php esc_html_e('#','ifind'); ?></th>
								<th><?php esc_html_e('Position','ifind'); ?></th>
								<th><?php esc_html_e('Frequency','ifind'); ?></th>
								<th><?php esc_html_e('First time','ifind'); ?></th>
								<th><?php esc_html_e('Last time','ifind'); ?></th>
							</tr>
							<?php foreach ($statistics_summary as $key => $business_summary) { ?>
								<?php if ($business_summary['display']) {
									$frequence = $business_summary['count'] / ($all_click_count / 100) . '%';
									$frequence .= ' ('.$business_summary['count'].'/'.$all_click_count.')';
									?>
									<tr>
										<td class="ifind-statistics-item-no"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></td>
										<td><?php echo $key; ?></td>
										<td><?php echo $frequence; ?></td>
										<td><?php echo ($business_summary['first']) ? $business_summary['first'] : __("Unknown", 'ifind'); ?></td>
										<td><?php echo ($business_summary['last']) ? $business_summary['last'] : __("Unknown", 'ifind'); ?></td>
									</tr>
									<?php $i++; 
								}
							} // end foreach  ?>
						</table>
					<?php } else { ?>
						<h4 class="ifind-statistics-result-desc"><?php esc_html_e( 'No record exists!', 'ifind' ); ?></h4>
					<?php } ?>

					<h3 class="ifind-statistics-item-title"><?php esc_html_e('III. STATISTICS DETAIL','ifind'); ?></h3>
					<?php if (is_array($statistics_detail) && count($statistics_detail) > 0) {
						$i = 1; ?>
						<h4 class="ifind-statistics-result-desc"><?php printf(__( 'There are <strong>%d</strong> records', 'ifind' ), count($statistics_detail)); ?></h4>
						<table border="1" class="ifind-table ifind-table-click-counter">
							<tr>
								<th><?php esc_html_e('#','ifind'); ?></th>
								<th><?php esc_html_e('Position','ifind'); ?></th>
								<th><?php esc_html_e('Time','ifind'); ?></th>
								<th><?php esc_html_e('Location','ifind'); ?></th>
								<th><?php esc_html_e('IP Address','ifind'); ?></th>
							</tr>
							<?php foreach ($statistics_detail as $click_info) { ?>
								<tr>
									<td class="ifind-statistics-item-no"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></td>
									<td><?php echo $click_info['position'] ?></td>
									<td><?php echo ifind_convert_timestamp_to_time($click_info['timestamp']); ?></td>
									<td><?php echo get_the_title($click_info['location_id']) ?></td>
									<td><?php echo $click_info['ip_address'] ?></td>
								</tr>
								<?php $i++;
							} // end foreach
							$i = 1; ?>
						</table>
					<?php } else { ?>
						<h4 class="ifind-statistics-result-desc"><?php esc_html_e( 'No record exists!', 'ifind' ); ?></h4>
					<?php } ?>
				</div>
			</div>
			<?php 
			$result = ob_get_clean();
		}
		if( $echo ){
			echo $result;
		} else{
			return $result;
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