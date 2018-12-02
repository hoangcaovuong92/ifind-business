<?php
/**
 * TVLGIAO WPDANCE FRAMEWORK 2017.
 *
 * @author : Cao Vuong.
 * -Email  : hoangcaovuong92@gmail.com.
 */

// Reference the Dompdf namespace
use Dompdf\Dompdf;

if( !function_exists('ifind_save_business_statistics') ){
	function ifind_save_business_statistics( $location_id, $business_id, $click_info ){
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

add_action('deleted_post', 'ifind_delete_business_statistics');
if( !function_exists('ifind_delete_business_statistics') ){
	function ifind_delete_business_statistics( $post_id ){
		$count_key = '_ifind_click_counter';
		delete_post_meta($post_id, $count_key);
	}
}

if( !function_exists('ifind_save_statistics_email_sender') ){
	function ifind_save_statistics_email_sender( $email_info ){
		if( ifind_is_robot() ){
			return;
		}
		$option_key = '_ifind_list_statistics_email_sender';
		$data = get_option($option_key);
		if (is_array($data)) {
			//Add data to the first of data array
			array_unshift($data, $email_info);
		}else{
			$data = array();
			array_unshift($data, $email_info);
		}

		if( $data ){
			$result = update_option($option_key, $data);
		}
	}
}

if( !function_exists('ifind_remove_statistics_email_sender') ){
	function ifind_remove_statistics_email_sender( $index, $attachment_file ){
		if( ifind_is_robot() ){
			return;
		}
		$option_key = '_ifind_list_statistics_email_sender';
		$data = get_option($option_key);
		unset($data[$index]);

		if($attachment_file && file_exists($attachment_file)){
			unlink($attachment_file);
		}

		$new_data = array();
		if(is_array($data) && count($data) > 0){
			foreach ($data as $value) {
				array_push($new_data, $value);
			}
		}

		update_option($option_key, $new_data);
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
			'direction' => array(
				'display' => true,
				'count' => 0,
				'first' => '',
				'last' => '',
			),
			'contact' => array(
				'display' => true,
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
					//$summary_data[$click_info['position']]['display'] = true; 
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

if(!function_exists ('ifind_get_list_statistics_email_sender')){
	function ifind_get_list_statistics_email_sender(){
		$option_key = '_ifind_list_statistics_email_sender';
		$data = get_option($option_key);
		return $data;
	}
}

// $ouput_type: admin | email_content
if( !function_exists('ifind_get_table_statistics') ){
	function ifind_get_table_statistics($location_id, $business_id, $datepicker_from = '', $datepicker_to = '', $ouput_type = 'admin', $echo = false ){
		$statistics_overview = ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to)['overview'];
		$statistics_summary = ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to)['summary'];
		$statistics_detail = ifind_get_list_statictis_detail($business_id, $datepicker_from, $datepicker_to)['detail'];

		$statistics_general = ifind_get_list_location_statictis_general($business_id, $datepicker_from, $datepicker_to);
		$list_email = ifind_get_list_statistics_email_sender();  
		ob_start(); ?>
			<?php if ($ouput_type === 'email_content'){ ?>
				<style type="text/css">
					.ifind-section{
						border: 1px solid #ddd;
						border-radius: 5px;
						padding: 8px;
						background: #fff;
    					margin-bottom: 0;
					}
					.ifind-table-wrap{
						overflow-x:auto;
					}
					.ifind-main-title{
						text-align: center;
						font-size: 20px;
						line-height: 1.2;
						margin: 10px 0;
						text-transform: uppercase;
					}

					.ifind-subtitle{
						font-size: 15px;
						margin: 20px 0 10px;
						text-decoration: underline;
					}
					
					.ifind-desc{
						font-style: italic;
						font-size: 12px;
						margin: 10px 0;
					}

					table.ifind-table{
						border-collapse: collapse;
						border-spacing: 0;
						width: 100%;
    					table-layout: fixed;
					}
					.ifind-horizontal-table tr td:first-child{
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

			<div id="ifind-business-statistics-wrap" class="ifind-section">
				<h2 class="ifind-main-title ifind-statistics-result-title">
					<?php 
					printf(esc_html__( 'Statistics for %s', 'ifind' ), get_the_title($business_id));
					if ($datepicker_from && $datepicker_to && $datepicker_from !== $datepicker_to) {
						echo '<br/>';
						printf(esc_html__( ' from %s to %s', 'ifind' ), $datepicker_from, $datepicker_to);
					}
					if ($datepicker_from && $datepicker_to && $datepicker_from === $datepicker_to) {
						echo '<br/>';
						printf(esc_html__( ' on %s', 'ifind' ), $datepicker_from);
					}
					if ($datepicker_from !== '' && $datepicker_to === '') {
						echo '<br/>';
						printf(esc_html__( ' on %s', 'ifind' ), $datepicker_from);
					}
					if ($datepicker_from === '' && $datepicker_to !== '') {
						echo '<br/>';
						printf(esc_html__( ' on %s', 'ifind' ), $datepicker_to);
					}
					?>
				</h2>

				<div class="ifind-table-wrap ifind-statistics-result">
					
					<h3 class="ifind-subtitle ifind-statistics-item-title"><?php esc_html_e('I. Overview','ifind'); ?></h3>
					<table border="1" class="ifind-table ifind-horizontal-table ifind-table-business-overview">
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

					<h3 class="ifind-subtitle ifind-statistics-item-title"><?php esc_html_e('II. General Statistics','ifind'); ?></h3>
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
									$percent = $business_summary['count'] / ($all_click_count / 100);
									$frequence = number_format((float)$percent, 2, '.', '') . '%';
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
						<h4 class="ifind-desc ifind-statistics-result-desc"><?php esc_html_e( 'No record exists!', 'ifind' ); ?></h4>
					<?php } ?>

					<h3 class="ifind-subtitle ifind-statistics-item-title"><?php esc_html_e('III. Statistics Detail','ifind'); ?></h3>
					<?php if (is_array($statistics_detail) && count($statistics_detail) > 0) {
						$i = 1; ?>
						<h4 class="ifind-desc ifind-statistics-result-desc"><?php //printf(__( 'There are <strong>%d</strong> records', 'ifind' ), count($statistics_detail)); ?></h4>
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
						<h4 class="ifind-desc ifind-statistics-result-desc"><?php esc_html_e( 'No record exists!', 'ifind' ); ?></h4>
					<?php } ?>
				</div>
			</div>

			<?php if ($ouput_type === 'admin'){ ?>
				<div id="ifind-business-list-statistics-email-wrap" class="ifind-section">
					<h2 class="ifind-main-title ifind-statistics-email-title"><?php esc_html_e( 'Sending emails history', 'ifind' ); ?></h2>
					<div class="ifind-table-wrap ifind-statistics-result">
						<?php if (is_array($list_email) && count($list_email) > 0) {
							$i = 1; ?>
							<table border="1" class="ifind-table ifind-table-list-email">
								<tr>
									<th><?php esc_html_e('#','ifind'); ?></th>
									<th><?php esc_html_e('Email','ifind'); ?></th>
									<th><?php esc_html_e('Time','ifind'); ?></th>
									<th><?php esc_html_e('Attachment','ifind'); ?></th>
									<th><?php esc_html_e('Action','ifind'); ?></th>
								</tr>
								<?php foreach ($list_email as $email) { ?>
									<tr>
										<td class="ifind-statistics-item-no"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></td>
										<td><?php echo $email['email']; ?></td>
										<td><?php echo ifind_convert_timestamp_to_time($email['time']); ?></td>
										<td><?php echo $email['direct_link'] ? '<a href="'.$email['direct_link'].'" target="_blank">'.__('Permalink','ifind').'</a>' : __('N/A','ifind'); ?></td>
										<td><a class="ifind-delete-statistics-email" href="#" 
												data-index="<?php echo $i - 1; ?>"
												data-attachment_file="<?php echo $email['attachment_file']; ?>" >
											<i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
										</td>
									</tr>
									<?php $i++; 
								} // end foreach  ?>
							</table>
						<?php } else { ?>
							<h4 class="ifind-desc ifind-statistics-result-desc"><?php esc_html_e( 'No record exists!', 'ifind' ); ?></h4>
						<?php } ?>
					</div>
				</div>
			<?php }
		$result = ob_get_clean();
		
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