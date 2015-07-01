<?php
//	$folder_url = 'http://images.freifunk-aachen.de/stable/factory/';

$folder_url = "https://images.aachen.freifunk.net/stable/factory/";

	$descriptions = array(
		'tp-link-tl-wr841n-nd-v3' => array('description' => 'Version 3','manufacturer' => 'TP-Link', 'group' => 'WR841n/nd'),
		'tp-link-tl-wr841n-nd-v5' => array('description' => 'Version 5','manufacturer' => 'TP-Link', 'group' => 'WR841n/nd'),
		'tp-link-tl-wr841n-nd-v7' => array('description' => 'Version 7','manufacturer' => 'TP-Link', 'group' => 'WR841n/nd'),
		'tp-link-tl-wr841n-nd-v8' => array('description' => 'Version 8','manufacturer' => 'TP-Link', 'group' => 'WR841n/nd'),
		'tp-link-tl-wr841n-nd-v9' => array('description' => 'Version 9','manufacturer' => 'TP-Link', 'group' => 'WR841n/nd'),
		'tp-link-tl-wr841n-nd-v8' => array('description' => 'Version 3','manufacturer' => 'TP-Link', 'group' => 'WR841n/nd'),
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$folder_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$page = curl_exec($ch);
	curl_close($ch);
	$doc = new DOMDocument();
	$doc->loadHTML($page);
	$elements = $doc->getElementsByTagName('a');
	$files = array();
	foreach($elements as $element){
		$found = false;
		foreach($descriptions as $key => $value){
			if( FALSE !== strpos($element->nodeValue,$key)){
				$files[$value['manufacturer']][$value['group']][$element->nodeValue] = $value['description'];
				$found = true;
			}
		}
		if(false == $found){
			echo sprintf("Warning: %s is not in description table. \n",$element->nodeValue);
		}
	}
	echo "\n\n\n";
	$seenmanufacturers = array();
	foreach($files as $manufacturer => $links){
	        if(!in_array($manufacturer,$seenmanufacturers)){
                	$seenmanufacturers[] = $manufacturer;
        	        echo "* ".$manufacturer."\n";
                }
		$seengroups = array();
		foreach($links as $group => $file_details){
			if(!in_array($group,$seengroups)){
				$seengroups[] = $group;
				echo "** ".$group."";
			}
			echo "(";
			foreach($file_details as $file => $name){
				echo " [".$folder_url.$file." ".$name."]";
			}
			echo ")\n";
		}
	}
?>

