<?php
class Host_SDN
{
    
    public $MAC_Addr;
    public $IPv4_Addr;
    public $IPv6_Addr;
    public $Vlan;

    public $Attachment_List;

    // method declaration
    public function Host_SDN($mac , $ip4 , $ip6 , $vlan, $att_list) {
        $this->MAC_Addr = $mac;
        $this->IPv4_Addr = $ip4; // Array di Indirizzi IPv4

        $this->IPv6_Addr = $ip6;
        $this->Vlan = $vlan;
        
        /*
        echo "MAC List:"; print_r($this->MAC_Addr); echo "<br>";
        echo "IPv4 List:"; print_r($this->IPv4_Addr); echo "<br>";
        echo "IPv6 List:"; print_r($this->IPv6_Addr); echo "<br>";
        echo "Vlan List:"; print_r($this->Vlan); echo "<br>";
        */
        $num_attach = count($att_list);

        $this->Attachment_List = array();

        for($i = 0 ; $i < $num_attach ; $i++){
            $this->Attachment_List[$i] = new Collegamento_Host_Switch($att_list[$i]->switch , $att_list[$i]->port);
        }
        /*
        echo "Switch List:"; print_r($this->Attachment_List); echo "<br>";
        */
    }

  

    public function Get_My_Switch(){
        return ($this->Attachment_List[0]->Switch_DPID);
    }

    public function Get_Int_MAC(){
        return hexdec($this->MAC_Addr);
    } 

    public function Get_My_Switch_Port(){
        return ($this->Attachment_List[0]->Switch_Port);
    }
}

function SearchHostByIPAddr($Host_SDN_array , $ipv4_addr){

    $c = count($Host_SDN_array);

    if($c <= 0){
        return -1;
    }

    for($i = 0 ; $i < $c ; $i++){
        if($Host_SDN_array[$i]->IPv4_Addr == $ipv4_addr){
            return $i;
        }
    }
    return -1;
}


class Collegamento_Host_Switch
{
    public $Switch_DPID;
    public $Switch_Port;

    public function Collegamento_Host_Switch($dpid , $port){
        $this->Switch_DPID = $dpid;
        $this->Switch_Port = intval($port);
    }
}
?>