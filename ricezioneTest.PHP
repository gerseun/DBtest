<?php

  if (isset($_POST['pacchetto'])){
    echo "ingresso <br>";
    $arr = json_decode($_POST['pacchetto'],true);
    stampa($arr);
    }

  function stampa($var){
    echo $var['test1']['a']." - ".$var['test1']['b']." - ".$var['test1']['c']."<br>";
    echo $var['test2']['a']." - ".$var['test2']['b']." - ".$var['test2']['c']."<br>";
    echo $var['test3']['a']." - ".$var['test3']['b']." - ".$var['test3']['c']."<br>";
    return true;
  }

?>
