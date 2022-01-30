<?php
require 'vendor/autoload.php';

use GeoIp2\Database\Reader;

$reader = new Reader('GeoLite2-Country_20220125/GeoLite2-Country.mmdb');

if(isset($_POST['Read'])){
    if($_POST['Read'] == 'Read'){
        $lines = file(__DIR__."\ipAddress.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($lines as $line){
            try{
                $record = $reader->country($line);
                $filename = "ip/".$record->country->isoCode.".txt";
                if(file_put_contents($filename, $line." ".$record->country->name."\n", FILE_APPEND)){
                    echo("File $filename created/updated<br>");
                };
            }
            catch(\GeoIp2\Exception\AddressNotFoundException $e){
                $filename = "ip/unknown.txt";
                file_put_contents($filename, $line."\n", FILE_APPEND);
            }
        }
    }
    // var_dump($array);
    unset($_POST['Read']);
}

if(isset($_POST['Clear'])){
    if($_POST['Clear'] == 'Clear'){
        $dir = __DIR__."\ip";
        $files1 = scandir($dir);
        foreach($files1 as $key){
            if($key > 0){
                $filename = "ip/".$key;
                unlink($filename);
                print("File ".$filename." deleted <br>");
            }
            else
                continue;
        }
    }
    // var_dump($array);
    unset($_POST['Clear']);
}

$dir = __DIR__."\\network.txt";

function calculate($decInput){
    $binary = "";
    $a = $decInput;

    /* calculate binary code from decimal */
    for($i = 0; $i < 8; $i++){
        switch($i){
            case 0 : {
                $b = $a - 128;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 128;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 1 : {
                $b = $a - 64;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 64;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 2 : {
                $b = $a - 32;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 32;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 3 : {
                $b = $a - 16;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 16;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 4 : {
                $b = $a - 8;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 8;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 5 : {
                $b = $a - 4;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 4;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 6 : {
                $b = $a - 2;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 2;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
            case 7 : {
                $b = $a - 1;
                if($b >= 0){
                    $binary .= 1;
                    $a -= 1;
                }
                else{
                    $binary .= 0;
                }
                break;
            }
        }
    }
    return $binary;
}

function calculateMask($octane, $mask){
    $bits = [
        "0" => "00000000.00000000.00000000.00000000",
        "1" => "10000000.10000000.10000000.10000000",
        "2" => "11000000.00000000.00000000.00000000",
        "3" => "11100000.00000000.00000000.00000000",
        "4" => "11110000.00000000.00000000.00000000",
        "5" => "11111000.00000000.00000000.00000000",
        "6" => "11111100.00000000.00000000.00000000",
        "7" => "11111110.00000000.00000000.00000000",

        "8" => "11111111.00000000.00000000.00000000",
        "9" => "11111111.10000000.00000000.00000000",
        "10"=> "11111111.11000000.00000000.00000000",
        "11"=> "11111111.11100000.00000000.00000000",
        "12"=> "11111111.11110000.00000000.00000000",
        "13"=> "11111111.11111000.00000000.00000000",
        "14"=> "11111111.11111100.00000000.00000000",
        "15"=> "11111111.11111110.00000000.00000000",

        "16"=> "11111111.11111111.00000000.00000000",
        "17"=> "11111111.11111111.10000000.00000000",
        "18"=> "11111111.11111111.11000000.00000000",
        "19"=> "11111111.11111111.11100000.00000000",
        "20"=> "11111111.11111111.11110000.00000000",
        "21"=> "11111111.11111111.11111000.00000000",
        "22"=> "11111111.11111111.11111100.00000000",
        "23"=> "11111111.11111111.11111110.00000000",

        "24"=> "11111111.11111111.11111111.00000000",
        "25"=> "11111111.11111111.11111111.10000000",
        "26"=> "11111111.11111111.11111111.11000000",
        "27"=> "11111111.11111111.11111111.11100000",
        "28"=> "11111111.11111111.11111111.11110000",
        "29"=> "11111111.11111111.11111111.11111000",
        "30"=> "11111111.11111111.11111111.11111100",
        "31"=> "11111111.11111111.11111111.11111110",
        "32"=> "11111111.11111111.11111111.11111111",
    ];
    $octaneEncrypt = $octane & $bits[$mask];
    return $octaneEncrypt;
}

if(isset($_POST['Read'])){
    if($_POST['Read'] == 'Read'){
        
        $lines = file($dir, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ip = "";
        $end = "";
        foreach($lines as $key => $value){
            try{
                $str = $value; // get string from array key value
                list($first, $second) = explode("/", $str); // explode IP from mask
                $arr1 = explode(".", $first); // explode IP from dots
                // echo "Pure: ".$first."<br>";
                foreach($arr1 as $key1 => $value1){
                    switch($key1){
                        case 0 : {
                            $a = $value1;
                            $ip .= bindec(calculateMask(calculate($a), $second)).".";
                            // $ip .= calculateMask(calculate($a), $second).".";
                            break;
                        }
                        case 1 : {
                            $a = $value1;
                            $ip .= bindec(calculateMask(calculate($a), $second)).".";
                            // $ip .= calculateMask(calculate($a), $second).".";
                            break;
                        }
                        case 2 : {
                            $a = $value1;
                            $ip .= bindec(calculateMask(calculate($a), $second)).".";
                            // $ip .= calculateMask(calculate($a), $second).".";
                            break;
                        }
                        default : {
                            $a = $value1;
                            $ip .= bindec(calculateMask(calculate($a), $second));
                            // $ip .= calculateMask(calculate($a), $second);
                        }
                    }
                    // echo $ip;
                    $end .= $ip;
                    $ip = "";
                }
                // echo $end;
                // echo "<br>";
        
                $record = $reader->country($end); // get country by IP
                $filename = "ip/".$record->country->isoCode.".txt"; // get filename (UK.txt)
                file_put_contents($filename, $value." ".$record->country->name."\n", FILE_APPEND);
                echo("File $filename created/updated<br>");

                $end = "";
            }
            catch(\GeoIp2\Exception\AddressNotFoundException $e){
                $filename = "ip/unknown.txt";
                file_put_contents($filename, $line."\n", FILE_APPEND);
            }
            catch(InvalidArgumentException $e){
                $filename = "ip/unknown.txt";
                file_put_contents($filename, $line."\n", FILE_APPEND);
            }
        }
    }
}

if(isset($_POST['Clear'])){
    if($_POST['Clear'] == 'Clear'){
        $dir = __DIR__."\ip";
        $files1 = scandir($dir);
        foreach($files1 as $key){
            if($key > 0){
                $filename = "ip/".$key;
                unlink($filename);
                print("File ".$filename." deleted <br>");
            }
            else
                continue;
        }
    }
}