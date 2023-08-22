<?php
require_once("Host_SDN.php");
require_once("Switch_SDN.php");

class Controller_SDN
{
	public $controller_ip;
	private $FirewallState;
	public $graph;
	public $SwitchList;
	public $InterSwitchLinkList;
	public $DeviceList;
	private $Number_Of_Flux;
	private $Number_Of_ACL_Rules;
	private $Number_Of_FW_Rules;
	private $Number_Of_Switches;
	private $Number_Of_Devices;
	private $Number_Of_InterSwitch_Links;

	// Costruttore
	public function Controller_SDN($ip)
	{
		$this->controller_ip = trim($ip);

		$this->Number_Of_Flux = 0;
		$this->Number_Of_ACL_Rules = 0;
		$this->Number_Of_FW_Rules = 0;
		$this->Number_Of_Devices = 0;
		$this->Number_Of_Switches = 0;
		$this->Number_Of_InterSwitch_Links = 0;
		$this->FirewallState = false;
	}

	// Funzione privata per richiamare una REST API
	private function CallRESTAPI($method, $url, $data = null)
	{
		// Dato un URL, un Metodo {POST, GET o DELETE} e dei dati
		// questa funzione invoca una REST API e restituisce il risultato.

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

		if ($method == "CANCELLA") {
			// Cancella è equivalente a DELETE, ma non è keyword di php
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // curl_setopt($ch, CURLOPT_PUT, true); - for PUT
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);  // DO NOT RETURN HTTP HEADERS
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
			$result = curl_exec($ch);
		}

		if ($method == "PUT") {
			// Cancella è equivalente a DELETE, ma non è keyword di php
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // curl_setopt($ch, CURLOPT_PUT, true); - for PUT
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);  // DO NOT RETURN HTTP HEADERS
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
			$result = curl_exec($ch);
		}

		return $result;
	}

	// Funzioni pubbliche per richiamare REST API Specifiche
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

	public function getNumber_OF_Flux() {
		// Restituisce il numero di regole instalate senza ricalcolare il valore di nuovo.
		if ($this->Number_Of_Flux <= 0) {
			$this->UpdateNumber_OF_Flux();
		}
		return intval($this->Number_Of_Flux);
	}

	public function getNumber_Of_ACL_Rules()
	{
		return intval($this->Number_Of_ACL_Rules);
	}

	public function getNumber_Of_FW_Rules() 
	{
		return $this->Number_Of_FW_Rules;
	}

	public function getFlussiInstallati()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/staticentrypusher/list/all/json");
	}

	public function getACLRulesInstallate()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/acl/rules/json");
	}
	public function getFWRulesInstallate()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/firewall/rules/json");
	}

	public function getNumber_Of_Switch() 
	{
		return $this->Number_Of_Switches;
	}

	public function getNumber_Of_Devices() 
	{
		return $this->Number_Of_Devices;
	}

	public function getNumber_Of_InterSwitch_Links() 
	{
		return $this->Number_Of_InterSwitch_Links;
	}

	private function getFirewallState()
	{
		return $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/firewall/module/status/json");
	}

	// Funzioni di Inserimento
	public function Insert_Flux($flux_details)
	{
		$ret = $this->CallRESTAPI("POST", "http://" . ($this->controller_ip) . ":8080/wm/staticentrypusher/json", $flux_details);
		$this->UpdateNumber_OF_Flux();
		return $ret;
	}

	public function InsertACLRule($rule_details)
	{
		$ret = $this->CallRESTAPI("POST", "http://" . ($this->controller_ip) . ":8080/wm/acl/rules/json", $rule_details);
		$this->Number_Of_ACL_Rules++;
		return $ret;
	}

	public function InsertFWRule($rule_details)
	{
		$ret = $this->CallRESTAPI("POST", "http://" . ($this->controller_ip) . ":8080/wm/firewall/rules/json", $rule_details);
		$this->Number_Of_FW_Rules++;
		return $ret;
	}

	// Funzioni di Aggiornamento
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
				'-',
				//$cleanDeviceList[$i]->ipv6[0],
				$cleanDeviceList[$i]->vlan[0],
				$cleanDeviceList[$i]->attachmentPoint
			);
		}
		$this->DeviceList = $DeviceList_Definitiva;
	}

	public function Update_Graph()
	{
		$SwitchListDim = $this->getNumber_Of_Switch();

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

					$this->graph[$i][$j] = $this->Search_InterSwitch_Link($switch_i, $switch_j);
					$this->graph[$j][$i] =  $this->graph[$i][$j];
					// echo  $switch_i." -- ". $switch_j." ==> ".$graph[$i][$j] . "<br>";
				}
			}
		}
	}

	public function UpdateNumber_OF_Flux()
	{
		// Ricalcola il numero di regole installate e infine lo restituisce.
		$Rules = get_object_vars(json_decode($this->getFlussiInstallati()));
		$NumSwitch = count($Rules);
		$count = 0;

		for ($i = 0; $i < $NumSwitch; $i++) {
			$a = array_pop($Rules);
			$count += count($a);
		}
		$this->Number_Of_Flux = ($count);
		return $count;
	}

	public function UpdateNumber_OF_ACL_Rules()
	{
		$ret = json_decode($this->getACLRulesInstallate());
		$this->Number_Of_ACL_Rules = count($ret);
		return $this->Number_Of_ACL_Rules;
	}

	public function UpdateNumber_OF_FW_Rules()
	{
		$ret = json_decode($this->getFWRulesInstallate());
		$this->Number_Of_FW_Rules = count($ret);
		return $this->Number_Of_FW_Rules;
	}

	private function Update_Number_Of_Switches()
	{
		$this->Number_Of_Switches = count($this->SwitchList);
	}

	private function Update_Number_Of_Devices()
	{
		$this->Number_Of_Devices = count($this->DeviceList);
	}

	private function Update_Number_Of_InterSwitch_Links()
	{
		$this->Number_Of_InterSwitch_Links = count($this->InterSwitchLinkList);
	}

	// Refresh Totale del Controller
	public function Update_Controller()
	{
		// Creazione della SwitchList, ossia la lista degli switch nella rete.
		$this->Update_SwitchList();

		// Creazione della lista dei collegamenti InterSwitch, ossia link che collegano due switch tra loro.
		$this->Update_InterSwitchLinkLIst();

		// Creazione della Lista degli Host collegati al controller
		$this->Update_DeviceList();

		// Creazione della matrice di rappresentazione della topologia della rete.
		$this->Update_Graph();

		$this->UpdateNumber_OF_Flux();

		$this->UpdateNumber_OF_ACL_Rules();

		$this->UpdateNumber_OF_FW_Rules();

		$this->Update_Number_Of_Devices();

		$this->Update_Number_Of_Switches();

		$this->Update_Number_Of_InterSwitch_Links();
	}

	// Funzioni di Eliminazione
	public function DeleteAllFlowRules()
	{
		$ret = $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/staticentrypusher/clear/all/json");
		$this->UpdateNumber_OF_Flux();
		return $ret;
	}

	public function DeleteAllFlowRulesOfSwitch($dpid)
	{
		$ret = $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/staticentrypusher/clear/" . $dpid . "/json");
		$this->UpdateNumber_OF_Flux();
		return $ret;
	}

	public function DeleteSingleFlowRule($data)
	{
		$ret = $this->CallRESTAPI("CANCELLA", "http://" . $this->controller_ip . ":8080/wm/staticentrypusher/json", $data);
		$this->UpdateNumber_OF_Flux();
		return $ret;
	}

	public function DeleteAllACLRules()
	{
		$ret = $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/acl/clear/json");
		$this->Number_Of_ACL_Rules = 0;
		return $ret;
	}

	public function DeleteSingleACLRule($data)
	{
		$ret = $this->CallRESTAPI("CANCELLA", "http://" . $this->controller_ip . ":8080/wm/acl/rules/json", $data);
		$this->Number_Of_ACL_Rules--;
		return $ret;
	}

	public function DeleteSingleFWRule($data)
	{
		$ret = $this->CallRESTAPI("CANCELLA", "http://" . $this->controller_ip . ":8080/wm/firewall/rules/json", $data);
		$this->Number_Of_FW_Rules--;
		return $ret;
	}

	// Altro

	public function ControllerOnline()
	{
		error_reporting(E_ERROR | E_PARSE);
		$ret = null;
		$ret = $this->CallRESTAPI("GET", "http://" . $this->controller_ip . ":8080/wm/core/version/json");
		error_reporting(E_ALL);

		if (is_null($ret) || $ret == false) {
			return 0;
		}

		$ret = get_object_vars(json_decode($ret));

		if ($ret["name"] == "floodlight") {
			return 1;
		} else {
			return 0;
		}
	}
	public function EnableFirewall($state = true)
	{
		if ($state == true) {
			$ret = $this->CallRESTAPI("PUT", "http://" . $this->controller_ip . ":8080/wm/firewall/module/enable/json");
			$this->FirewallState = true;
		} else {
			$ret = $this->CallRESTAPI("PUT", "http://" . $this->controller_ip . ":8080/wm/firewall/module/disable/json");
			$this->FirewallState = false;
		}
	}

	public function ShowFirewallState(){
		if(($this->getFirewallState()) == '{"result" : "firewall disabled"}'){
			return 1;
		}
		return 0;
	}

	private function Search_InterSwitch_Link($dpid1, $dpid2)
	{
		$linkList = $this->InterSwitchLinkList;
		$No_Link = 99999;
		$num = $this->Number_Of_InterSwitch_Links;

		for ($i = 0; $i < $num; $i++) {
			if (
				($linkList[$i]->srg_DPID == $dpid1 && $linkList[$i]->dst_DPID == $dpid2)
				||
				($linkList[$i]->srg_DPID == $dpid2 && $linkList[$i]->dst_DPID == $dpid1)
			) {
				return intval($linkList[$i]->latenza);
			}
		}
		return $No_Link;
	}
}
