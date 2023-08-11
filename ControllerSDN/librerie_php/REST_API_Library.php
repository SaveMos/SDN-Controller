<?php
function CallRESTAPI($method, $url, $data = null)
{
	$url = trim($url); 
	$method = trim($method);

    $curl = curl_init($url);

	$result = 0;

	if($method == "POST"){
    	curl_setopt($curl, CURLOPT_POST, true);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($curl);
    	curl_close($curl);
	}

	if($method == "GET"){
		$result = file_get_contents($url);
	}

    return $result;
}

function getTopology($controller_ip){
    return CallRESTAPI("POST", "http://".$controller_ip.":8080/wm/topology/switchclusters/json");
}

function getSwitchList($controller_ip){
    return CallRESTAPI("GET", "http://".$controller_ip.":8080/wm/core/controller/switches/json");
}

function getDeviceList($controller_ip){
	return CallRESTAPI("GET" , "http://".$controller_ip.":8080/wm/device/");
}

function getInterSwitchLinkList($controller_ip){
	return CallRESTAPI("GET" , "http://".$controller_ip.":8080/wm/topology/links/json");
}

// Interswitch link http://192.168.1.30:8080/wm/topology/links/json
// get switches --> http://192.168.1.30:8080/wm/topology/switchclusters/json
// Get all devices in the topology with links --> http://192.168.1.30:8080/wm/device/
