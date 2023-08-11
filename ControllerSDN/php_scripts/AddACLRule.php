<?php

require_once('../librerie_php/REST_API_Library.php');

$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$src_Mask = intval($_POST["Sorg_Subnet_Mask_1"]) . "." . intval($_POST["Sorg_Subnet_Mask_2"]) . "." . intval($_POST["Sorg_Subnet_Mask_3"]) . "." . intval($_POST["Sorg_Subnet_Mask_4"]);

$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);
$dst_Mask = intval($_POST["Dest_Subnet_Mask_1"]) . "." . intval($_POST["Dest_Subnet_Mask_2"]) . "." . intval($_POST["Dest_Subnet_Mask_3"]) . "." . intval($_POST["Dest_Subnet_Mask_4"]);

$act = $_POST["insertACL_action"];

$url = 'http://192.168.1.30:8080/wm/acl/rules/json';
$data = ["src-ip" => $src_ip . "/32", "dst-ip" => $dst_ip . "/32", "action" => $act];

$res = CallRESTAPI("POST", $url, $data);

print_r($res);

?>
