<?php
require 'vendor/autoload.php';

use GeoIp2\Database\Reader;

$reader = new Reader('GeoLite2-Country_20220125/GeoLite2-Country.mmdb');

if(isset($_POST['Read'])){
    if($_POST['Read'] == 'Read'){
        $lines = file("D:\programs\openserver\domains\ip\ipAddress.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($lines as $line){
            try{
                $record = $reader->country($line);
                $filename = "ip/".$record->country->isoCode.".txt";
                if (!file_exists($filename)) {
                        $fp = fopen($filename, "w");
                        fwrite($fp, $line." ".$record->country->name);
                        print("File ".$filename." created <br>");
                        fclose($fp);
                }
            }
            catch(\GeoIp2\Exception\AddressNotFoundException $e){
                $filename = "ip/unknown.txt";
                file_put_contents($filename, $line."\n", FILE_APPEND);
                // $fp = fopen($filename, "w");
                // print("File ".$filename." created<br>");
                // fclose($fp);
            }
        }
    }
    // var_dump($array);
    unset($_POST['Read']);
}

if(isset($_POST['Clear'])){
    if($_POST['Clear'] == 'Clear'){
        $dir = "D:\Programs\OpenServer\domains\ip\ip";
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