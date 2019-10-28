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

    //CREAZIONE ARTICOLO (CILINDRO)
    public function setArticolo($codArt,$desc,$cli,$codCli){
      if(isset($this->attiva)){
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "INSERT INTO `articolo` (`cod_articolo`, `descrizione`, `cliente`, `cod_cliente`, `data`) VALUES ('".$codArt."','".$desc."','".$cli."','".$codCli."',null);";
        if (mysqli_query($connessione,$istruzione)) {
          echo "ARTICOLO INSERITO CORRETTAMENTE <br>";
          //creo la tabella relativa all' articolo
          $istruzione = "CREATE TABLE ".$codArt." ( `cod_componente` VARCHAR(50) NOT NULL , `descrizione` VARCHAR(50) NULL , `dimensione` VARCHAR(50) NULL , `materiale` VARCHAR(50) NULL , PRIMARY KEY (`cod_componente`));";
          if (mysqli_query($connessione,$istruzione)) {
            echo "TABELLA ARTICOLO CREATA CORRETTAMENTE <br>";
          }
          else {
            echo "Error creating table: " . $connessione->error."<br>";
            mysqli_close($connessione);
            return false;
          }
        }
        else {
          echo "ERRORE CONNESSIONE AL DATABASE, VERIFICARE CHE SIA UN NUOVO CILINDRO!<br>";
          mysqli_close($connessione);
          return false;
        }
        //disconnessione
        mysqli_close($connessione);
        return true;
      }
      else{
        mysqli_close($connessione);
        return false;
      }
    }

    //INSERISCO I COMPONENTI NELLA TABELLA DELL' ARTICOLO
    public function setComponenteInArticolo($codArt, $codComponenti){
      if(isset($this->attiva)){
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //ciclo tutti i componenti
        $cont = 0;
        foreach($codComponenti as $componente){
          echo "<br>".$cont."<br>";
          $codComp = $componente["Codice Componente"];
          $desc = $componente["Descrizione"];
          $dim = $componente["Dimensione"];
          $mat = $componente["Materiale"];

          //query per inserire il componente nella tabella cilindro
          $istruzione = "INSERT INTO ".$codArt." (`cod_componente`, `descrizione`, `dimensione`, `materiale`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
          if (mysqli_query($connessione,$istruzione)) {
            echo $codComp." INSERITO CORRETTAMENTE <br>";
          }
          else {
            echo "Error creating table: " . $connessione->error."<br>";
            echo "err 2 -> Componente già inserito nel cilindro - Vedere cod. componente: ".$codComp." <br>";
          }

          //query per inserire il componente nella tabella componenti
          $istruzione = "INSERT INTO `componente` (`cod_componente`, `descrizione`, `dimensione`, `materiale`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
          if (mysqli_query($connessione,$istruzione)) {
            //echo "COMPONENTE INSERITO CORRETTAMENTE <br>";
          }
          else {
            echo "Error creating table: " . $connessione->error."<br>";
            echo "err 3 Componente già salvato - Vedere cod. componente: ".$codComp." <br>";
          }

          $cont = $cont +1;

        }

        //disconnessione
        mysqli_close($connessione);
        return true;
      }
      else{
        return false;
      }
    }

  }

//////////////////////////////////////////////////////////////////////////

//FINE CLASSE -> FUNZIONI DI RICEZIONE DATI
  if (isset($_POST['newAarticolo'])){
    //CREAZIONE NUOVO ARTICOLO - CILINDRO
    $varDB = new conn_DB();
    echo "<br>ingresso<br>";
    $assieme = json_decode($_POST['newAarticolo'],true);
    //setArticolo -> cilindro - componenti
    $articolo = $assieme[0];
    $componenti = $assieme[1];
    //variabili del cilindro per settarlo
    $codArticolo = $articolo["ca"];
    $descrizione = $articolo["desc"];
    $cliente = $articolo["cli"];
    $codCliente = $articolo["cc"];
    //setto l' articolo
    $varDB->setArticolo($codArticolo,$descrizione,$cliente,$codCliente);
    //vado a salvare le componenti del cilindro
    $varDB->setComponenteInArticolo($codArticolo, $componenti);
  }
  elseif(isset($_POST['getArticolo'])){
    //RICERCA SE E' GIA' STATO INSERITO IL CODICE ARTICOLO ED INVIO I DATI

  }

?>
