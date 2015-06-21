<?php
	// read target station from AJAX POST
	if(!$_POST['target']) die("0");
	$target = $_POST['target'];
	// read xml file
	date_default_timezone_set("Asia/Taipei");
	$time_now = date('H:i:s'); 
	$xml_dir  = "../XML/" . date(Ymd) . ".xml";
	if(file_exists($xml_dir)){
		$xml = simplexml_load_file($xml_dir) or die("Error: Cannot create object");
	}
	else{  // if xml file doesn't exist, go to download
		header("Location:downloadXML.php");
		exit();
	}
	// start to search available train schedules
	$schedule_array = array();
	foreach($xml->children() as $traininfo){
		// the simplexml method returns xml object, need (string) to transform into string.
		$schedule = array(
			'Train'    => (string)$traininfo['Train'],
			'CarClass' => (string)$traininfo['CarClass'],
			'DEPTime'  => '',
			'Dest'     => '',
			'Dir'      => (string)$traininfo['LineDir'],
			'flag'     => false
		);
		foreach($traininfo->children() as $timeinfo){
			// if the train stops at the target station
			if($timeinfo['Station'] == $target){
				// if the train departure time is after present time
				if( date('H:i:s', strtotime($timeinfo['DEPTime'])) > date('H:i:s') ){
					$schedule['flag'] = true;
					$schedule['DEPTime'] = (string)$timeinfo['DEPTime'];
				}
			}	
			$schedule['Dest'] = (string)$timeinfo['Station'];
		}
		if($schedule['flag'])  array_push($schedule_array, $schedule);
	}
	// sorting function (brilliant stackoverflow!)
	function sortByOrder($a, $b) {
		return strtotime($a['DEPTime']) - strtotime($b['DEPTime']);
	}
	usort($schedule_array, 'sortByOrder');
	// we only need last six schedules
	$Last_six_array = array_slice($schedule_array, 0, 6);
	header('Content-Type: application/json');
	echo json_encode($Last_six_array);
?>
