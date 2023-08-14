<?php
function CallRESTAPI($method, $url, $data = null)
{
	$url = trim($url);
	$method = trim($method);

	$result = 0;

	if ($method == "POST") {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);

		
		curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => json_encode($data)
		));

		$result = curl_exec($curl);
		curl_close($curl);
	}

	if ($method == "GET") {
		$result = file_get_contents($url);
	}

	return $result;
}

function getTopology($controller_ip)
{
	return CallRESTAPI("POST", "http://" . $controller_ip . ":8080/wm/topology/switchclusters/json");
}

function getSwitchList($controller_ip)
{
	return CallRESTAPI("GET", "http://" . $controller_ip . ":8080/wm/core/controller/switches/json");
}

function getDeviceList($controller_ip)
{
	return CallRESTAPI("GET", "http://" . $controller_ip . ":8080/wm/device/");
}

function getInterSwitchLinkList($controller_ip)
{
	return CallRESTAPI("GET", "http://" . $controller_ip . ":8080/wm/topology/links/json");
}

function getNumber_OF_Flux($controller_ip)
{
	return rand(1, 9999);
}

function Insert_Flux($controller_ip, $flux_details)
{
	return CallRESTAPI("POST", "http://" . ($controller_ip) . ":8080/wm/staticentrypusher/json", $flux_details);
}

$Numero_Di_Flussi = 0;
$Priority = 32768;



// Interswitch link http://192.168.1.30:8080/wm/topology/links/json
// get switches --> http://192.168.1.30:8080/wm/topology/switchclusters/json
// Get all devices in the topology with links --> http://192.168.1.30:8080/wm/device/
