<?php
    foreach ($argv as $arg) {
         $e=explode("=",$arg);
        if(count($e)==2)
            $_REQUEST[$e[0]]=$e[1];
        else   
            $_REQUEST[]=$e[0];
    }

if(count($_REQUEST) == 1 || isset($_REQUEST['-h'])){
	$help = "Usage: \n";
	$help .= " php ".$_REQUEST[0]." -i=alfred-merged.json -o=alias.txt (output to file)\n";
	$help .= " php ".$_REQUEST[0]." -i=alfred-merged.json (output to STD)\n";
	$help .= " php ".$_REQUEST[0]." h (display help)\n";
	if(isset($_REQUEST['-h'])){
		file_put_contents('php://stdout', $help);
		exit(0);
	}else{
		file_put_contents('php://stderr', $help);
		exit(1);
	}
}

if(isset($_REQUEST['-i']) &&  $_REQUEST['-i'] != ""){
	$file_path = $_REQUEST['-i'];
}else{
	file_put_contents('php://stderr', "no input file given \n");
	exit(1);
}

if(!file_exists($file_path)){
	file_put_contents('php://stderr', "file '.$file.' not found \n");
	exit(1);
}

$file_j = file_get_contents($file_path);
if($file_j === false){
	file_put_contents('php://stderr', "could not read file \n");
	exit(1);
}

$file_a = json_decode($file_j);


if(($file_a === NULL)){
	file_put_contents('php://stderr', "could not decode file \n");
	exit(1);
}


$file_contents = array();

foreach ($file_a as $key => $value) {
	//print_r($value);
	if(isset($value->network->mesh_interfaces)){
		foreach ($value->network->mesh_interfaces as $k => $v) {
			if(isset($file_contents[$v])){
				file_put_contents('php://stderr', "dublicate entry for : ".$v." \n");

			}
			$file_contents[$v] =  $v."|".$value->hostname." (mesh ".$k.")\n";
		}
	}
	if(isset($value->network->mac)){
		if(isset($file_contents[$value->network->mac])){
			file_put_contents('php://stderr', "dublicate entry for MAC: ".$value->network->mac." \n");
		}
		$file_contents[$value->network->mac] = $value->network->mac."|".$value->hostname." (ap ".$k.")\n";
	}



}


if(isset($_REQUEST['-o']) &&  $_REQUEST['-o'] != ""){
	$fpc = file_put_contents($_REQUEST['-o'], $file_contents);
	if($fpc === false){
		file_put_contents('php://stderr', "could not write to output file: ".$_REQUEST['-o']." \n");
		exit(1);
	}
}else{
	file_put_contents('php://stdout', $file_contents);
	exit(0);
}

?>
