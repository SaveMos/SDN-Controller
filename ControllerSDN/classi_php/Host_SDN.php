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
        $this->IPv4_Addr = $ip4;
        $this->IPv6_Addr = $ip6;
        $this->Vlan = $vlan;

        $num_attach = count($att_list);
        $this->Attachment_List = array();

        for($i = 0 ; $i < $num_attach ; $i++){
            $this->Attachment_List[$i] = new Collegamento_Host_Switch($att_list[$i]->switch , $att_list[$i]->port);
        }   
    }

    public function Get_My_Switch(){
        return ($this->Attachment_List[0]->Switch_DPID);
    }
    public function Get_My_Switch_Port(){
        return ($this->Attachment_List[0]->Switch_Port);
    }
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