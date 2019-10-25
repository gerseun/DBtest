<?php
  class conn_DB{
    public $lavorazioni = array("DA SETTARE","DA TAGLIARE","DA ORDINARE","ORDINATO","MAT ARRIVATO","TAGLIATO","CARICO GREZZO", "CARICO PARZ LAVORATO", "CARICO FINITO","INIZIO PROD ISORELLA","FINE PROD ISORELLA");
    // parametri per la connessione al database
    public $nomehost = "localhost";
    public $nomeuser = "root";
    public $password = "";
    public $dbName = "db_progresso";

    // controllo sulle connessioni attive
    public $attiva = false;

    // funzione per la connessione a MySQL
    public function connetti(){
      if(!$this->attiva){
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        echo "<br>connessione<br>";
      }
      else{
        return true;
      }
    }

    function stampa($var){
      $this->connetti();
      echo $var['test1']['a']." - ".$var['test1']['b']." - ".$var['test1']['c']."<br>";
      echo $var['test2']['a']." - ".$var['test2']['b']." - ".$var['test2']['c']."<br>";
      echo $var['test3']['a']." - ".$var['test3']['b']." - ".$var['test3']['c']."<br>";
      return true;
    }
  }
///////////////////////////////
//FINE CLASSE -> FUNZIONI DI RICEZIONE DATI

  if (isset($_POST['pacchetto'])){
    $varDB = new conn_DB();
    echo "<br>ingresso<br>";
    $arr = json_decode($_POST['pacchetto'],true);
    $varDB->stampa($arr);
  }

?>
