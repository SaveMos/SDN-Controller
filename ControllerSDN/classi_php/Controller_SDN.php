<?php

require_once("Host_SDN.php");
require_once("Switch_SDN.php");

class Controller_SDN
{
	public $controller_ip;

	private $Number_Of_Flux;

	public $graph;
	public $SwitchList;
	public $InterSwitchLinkList;
	public $DeviceList;

	public function Controller_SDN($ip)
	{
		$this->controller_ip = trim($ip);
		$this->UpdateNumber_OF_Flux();
	}
	private function CallRESTAPI($method, $url, $data = null)
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

	public function getTopology()
	{
		return $this->CallRESTAPI("POST", "http://" . $this->controller_ip . ":8080/wm/topology/switchclusters/json");
	}

	public function getSwitchList()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/core/controller/switches/json");
	}

	public function getDeviceList()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/device/");
	}

	public function getInterSwitchLinkList()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/topology/links/json");
	}

	private function UpdateNumber_OF_Flux()
	{
		$Rules = get_object_vars(json_decode($this->getFlussiInstallati()));
		$NumSwitch = count($Rules);
		$count = 0;

		for ($i = 0; $i < $NumSwitch; $i++) {
			$a = array_pop($Rules);
			$count += count($a);
		}
		$this->Number_Of_Flux = $count;
	}

	public function getNumber_OF_Flux()
	{
		if ($this->Number_Of_Flux <= 0) {
			$this->UpdateNumber_OF_Flux();
		}
		return intval($this->Number_Of_Flux);
	}

	public function getFlussiInstallati()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/staticentrypusher/list/all/json");
	}

	public function Insert_Flux($flux_details)
	{
		$ret = $this->CallRESTAPI("POST", "http://" . ($this->controller_ip) . ":8080/wm/staticentrypusher/json", $flux_details);
		$this->UpdateNumber_OF_Flux();
	}

	public function Update_SwitchList()
	{
		$SwitchList = json_decode($this->getSwitchList());
		$SwitchListDim = count($SwitchList);

		$SwitchList_Definitive = array();
		for ($i = 0; $i < $SwitchListDim; $i++) {
			$SwitchList_Definitive[$i] = new Switch_SDN($SwitchList[$i]->switchDPID, $SwitchList[$i]->inetAddress);
		}

		// Ordinamento della Switch list in senso crescente in base al DPID.
		usort($SwitchList_Definitive, 'Comparatore_DPID');

		$this->SwitchList = $SwitchList_Definitive;
	}

	public function Update_InterSwitchLinkLIst()
	{
		$InterSwitchLinkList = $this->getInterSwitchLinkList();
		$InterSwitchLinkList = str_replace('-', '_', $InterSwitchLinkList);
		// Nel json i nomi degli attributi contenevano il '-' il quale mandava in confusione il sistema
		// lo rimpiazzo con il '_'
		$InterSwitchLinkList = json_decode($InterSwitchLinkList);

		$Num_of_InterSwitchLinkList = count($InterSwitchLinkList);

		$InterSwitchLinkList_Definitive = array();

		for ($i = 0; $i < $Num_of_InterSwitchLinkList; $i++) {
			$InterSwitchLinkList_Definitive[$i] = new CollegamentoInterSwitch(
				$InterSwitchLinkList[$i]->src_switch,
				$InterSwitchLinkList[$i]->dst_switch,
				$InterSwitchLinkList[$i]->src_port,
				$InterSwitchLinkList[$i]->dst_port,
				false,
				$InterSwitchLinkList[$i]->latency
			);

			if ($InterSwitchLinkList[$i]->direction == "bidirectional") {
				$InterSwitchLinkList_Definitive[$i]->bidirezionale = true;
			}
		}
		$this->InterSwitchLinkList = $InterSwitchLinkList_Definitive;
	}

	public function Update_DeviceList()
	{
		$DeviceList = get_object_vars(json_decode($this->getDeviceList()));
		$Num_Element = count($DeviceList['devices']);
		$cleanDeviceList = array();
		$cleanDeviceList_Dim = 0;

		for ($i = 0; $i < $Num_Element; $i++) {
			if (
				count($DeviceList['devices'][$i]->mac) == 0
				||  count($DeviceList['devices'][$i]->ipv4) == 0
				||  count($DeviceList['devices'][$i]->attachmentPoint) == 0
			) {
				continue;  // Ignoro gli Host con attributi "Anomali"
			}
			$cleanDeviceList[$cleanDeviceList_Dim] = $DeviceList['devices'][$i];
			$cleanDeviceList_Dim++;
		}

		$DeviceList_Definitiva = array();

		// Creazione della Device List, ossia la Lista degli Host.
		for ($i = 0; $i < $cleanDeviceList_Dim; $i++) {
			$DeviceList_Definitiva[$i] = new Host_SDN(
				$cleanDeviceList[$i]->mac[0],
				$cleanDeviceList[$i]->ipv4[0],
				//$cleanDeviceList[$i]->ipv6[0],
				'-',
				$cleanDeviceList[$i]->vlan[0],
				$cleanDeviceList[$i]->attachmentPoint
			);
		}
		$this->DeviceList = $DeviceList_Definitiva;
	}

	public function Update_Graph()
	{
		$SwitchListDim = count($this->SwitchList);

		$this->graph = array();
		for ($i = 0; $i < $SwitchListDim; $i++) {
			$this->graph[$i] = array();
		}

		for ($i = 0; $i < $SwitchListDim; $i++) {
			for ($j = 0; $j < $SwitchListDim; $j++) {

				if ($i > $j) {
					continue;
				}
				if ($i == $j) {
					$this->graph[$i][$j] = 0;
				} else {
					$switch_i = $this->SwitchList[$i]->DPID;
					$switch_j = $this->SwitchList[$j]->DPID;

					$this->graph[$i][$j] = Search_InterSwitch_Link($switch_i, $switch_j, $this->InterSwitchLinkList);
					$this->graph[$j][$i] =  $this->graph[$i][$j];
					// echo  $switch_i." -- ". $switch_j." ==> ".$graph[$i][$j] . "<br>";
				}
			}
		}
	}

	public function Update_Controller(){
		// Creazione della SwitchList, ossia la lista degli switch nella rete.
		$this->Update_SwitchList();
   
		// Creazione della lista dei collegamenti InterSwitch, ossia link che collegano due switch tra loro.
		$this->Update_InterSwitchLinkLIst();
		
		$this->Update_DeviceList();
		// Creazione della matrice di rappresentazione della topologia della rete.
		$this->Update_Graph();
	}
}


function Search_InterSwitch_Link($s1, $s2, $linkList)
{
    $No_Link = 99999;

    $num = count($linkList);
    for ($i = 0; $i < $num; $i++) {
        if (
            ($linkList[$i]->srg_DPID == $s1 && $linkList[$i]->dst_DPID == $s2)
            ||
            ($linkList[$i]->srg_DPID == $s2 && $linkList[$i]->dst_DPID == $s1)
        ) {
            return $linkList[$i]->latenza;
        }
    }
    return $No_Link;
}



// Interswitch link http://192.168.1.30:8080/wm/topology/links/json
// get switches --> http://192.168.1.30:8080/wm/topology/switchclusters/json
// Get all devices in the topology with links --> http://192.168.1.30:8080/wm/device/
