<?php

class closeclientLP{
  public $filename;
  public function __construct(){
    if(true){
      $date = date('dmYA');
      $filename = $date.'.cclp';
      $filedir = dir.'/classes/closeclientlp/';
      $rel = dirname(__DIR__).'/classes/closeclientlp/';
      $filepath = $rel.$filename;
      $closeClient = 0;
      if(!file_exists($filepath)){
        $lpurl = 'https://api.lunapunto.com/closeclient?clientid='.SLUG;
        $close = file_get_contents($lpurl);
        if($close){
          $closeClient = 1;
        }

        $o = array(
          'closeclient' => $closeClient,
          'timestamp'   => time()
        );

        $r = json_encode($o);

        file_put_contents($rel.$filename, $r);


      }else{
        $rslt = file_get_contents($filepath);
        $rslt = json_decode($rslt);
        $closeClient = $rslt->closeclient;
      }

      if($closeClient){
        exit('LP CLIENT SEAL 0.1');
      }
    }
  }
}

/*
* LP API Client Close Integration
*/
$closeClient = new closeclientLP;
