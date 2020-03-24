<?php

  include("config.php");
  
  /* connect to mailbox */
  
  if ($mailssl) { 
    $mailconnection = "{" . $mailhost . ":" . $mailport . "/" . $mailtype . "/ssl}" . $mailfolder;
  } else {
    $mailconnection = "{" . $mailhost . ":" . $mailport . "/" . $mailtype . "}" . $mailfolder;
  }
  
  if ($mailbox = imap_open($mailconnection, $mailuser, $mailpass)) {
    $result = imap_search($mailbox, 'UNSEEN FROM "' . $pbxemail . '"');
    $unseen = $result[0] ? $result[0] : 0;
  }
  
  if ($unseen > 0) {
    
    $index = intval(getCounter() / $retry);
    makeCall($phones[$index]);
    addCounter();
    
    if (getCounter() == (count($phones) * $retry)) {
      resetCounter();
    }
    
  } else {
    
    resetCounter();
    
  }
  
  /* make call function */
  
  function makeCall($recipient) {
    
    system('asterisk -rx "channel originate ' . $GLOBALS['trunktype'] . '/' . $GLOBALS['trunk'] . '/' . $recipient . ' Application Dial Local/' . $GLOBALS['extension'] . '@from-internal/n,,S(' . $GLOBALS['time'] . ')"');
    
    writeLog("CALLED $recipient");
    
  }
  
  
  /* reset counter function */
  
  function resetCounter() {
    
    $f = fopen($GLOBALS['countername'],"w");
    fwrite($f,"0");
    fclose($f);
    
  }
  
  
  /* get counter function */
  
  function getCounter() {
    
    $f = fopen($GLOBALS['countername'],"r");
    return intval(fread($f, filesize($GLOBALS['countername'])));
    
  }
  
  
  /* add counter function */
  
  function addCounter() {
    
    $n = getCounter();
    $n = $n + 1;
    
    $f = fopen($GLOBALS['countername'],"w");
    fwrite($f, $n);
    fclose($f);
    
  }
  
  
  /* write log function */
  
  function writeLog($text) {
    
    $f = fopen($GLOBALS['logfile'],"a+");
    fwrite($f, "\n" . date("Y-m-d H:i:s") . "     $text");
    fclose($f);
    
  }
  
?>