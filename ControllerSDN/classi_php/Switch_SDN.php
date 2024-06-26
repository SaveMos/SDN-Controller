<?php
class Switch_SDN
{
    public $DPID;
    public $IP_Addr;

    // method declaration
    public function Switch_SDN( $dipd ,  $IP_Addr) {
        $this->DPID = $dipd;
        $this->IP_Addr = $IP_Addr;
    }
    public function GetIPAddress(){
        return substr($this->IP_Addr, 1, strpos($this->IP_Addr, ':') - 1);
    }

    public function Get_Int_DPID(){
        return hexdec($this->DPID);
    }

    public function Print_Switch(){
        echo ("DPID => ".$this->DPID." <br> IP Address => ".$this->GetIPAddress()."<br><br>");
    }

    public function CheckDPID($dpid_in){
        if($this->DPID == $dpid_in){
            return true;
        }else{
            return false;
        }
    }
}

function Comparatore_DPID($s1 , $s2){
    return (($s1->Get_Int_DPID()) > ($s2->Get_Int_DPID()));
}


class CollegamentoInterSwitch{
    public $srg_DPID;
    public $srg_port;
    public $dst_DPID;
    public $dst_port;
    public $bidirezionale;
    public $latenza;

    public function CollegamentoInterSwitch($srg_dpid , $dst_dpid , $srg_port , $dst_port ,  $flag , $lat){
        $this->srg_DPID = trim($srg_dpid);
        $this->dst_DPID = trim($dst_dpid);
        $this->srg_port = intval($srg_port);
        $this->dst_port = intval($dst_port);  
        $this->bidirezionale = $flag;
        $this->latenza = intval($lat);
    }

    
}

function Get_Ports_to_these_Switch($SwitchList , $dpid1 , $dpid2){
    $c = count($SwitchList);
    if($c > 1){
    
        for($i = 0 ; $i < $c ; $i++){
            $t = fixObject($SwitchList[$i]);

            if($t->srg_DPID == $dpid1 && $t->dst_DPID == $dpid2){
                return [$t->srg_port , $t->dst_port];
            }

            if($t->srg_DPID == $dpid2 && $t->dst_DPID == $dpid1){
                return [ $t->dst_port , $t->srg_port];
            }
        }
    
        
    }
    return [-1 , -1];
}
