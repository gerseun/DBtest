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
            echo "TABELLA ARTICOLO CREATA CORRETTAMENTE <br><br>";
          }
          else {
            echo "Error creating table: " . $connessione->error."<br><br>";
          }
        }
        else {
          echo "ERRORE CONNESSIONE AL DATABASE, VERIFICARE CHE SIA UN NUOVO CILINDRO!<br><br>";
        }
        //disconnessione
        mysqli_close($connessione);
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

    //RICERCA ARTICOLO INSERITO CON TUTTI I SUOI COMPONENTI
    public function getArticolo($ricArticolo){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $cod_articolo = $descrizione = $cliente = $cod_cliente = "";
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM articolo WHERE cod_articolo='".$ricArticolo."';";
        $result=mysqli_query($connessione,$istruzione);
        //controllo se articolo già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $cod_articolo = $row["cod_articolo"];
          $descrizione = $row["descrizione"];
          $cliente = $row["cliente"];
          $cod_cliente = $row["cod_cliente"];
          //creo array dell' articolo
          $arr_Articolo = array("ca"=>$cod_articolo,"d"=>$descrizione,"c"=>$cliente,"cc"=>$cod_cliente);

          //prendo la tabella articolo e stampo i componenti già salvati
          $istruzione = "SELECT * FROM ".$ricArticolo.";";
          if($result=mysqli_query($connessione,$istruzione)){
            $i = 0;
            while($row = mysqli_fetch_assoc($result)) {
              //salvo le righe dei componenti inseriti nell' articolo cercato
              //array di componenti
              $arr_Componente[$i] = array("c1"=>$row["cod_componente"],"c2"=>$row["descrizione"],"c3"=>$row["dimensione"],"c4"=>$row["materiale"]);
              $i = $i+1;
            }
          }
          else {
            echo "Error creating table: " . $connessione->error."<br>";
          }
          //concateno gli array e li passo codificati
          $arrRisultato = array("aa"=>$arr_Articolo,"ac"=>$arr_Componente);
          echo json_encode($arrRisultato);
          //disconnessione
          mysqli_close($connessione);
          return true;
        }
        else{
          //disconnessione
          mysqli_close($connessione);
          //non ancora inserito
          return false;
        }
      }
    }

    //SALVO I COMPONENTI SINGOLI AL DI FUORI DELL' ASSIEME
    public function setCompSingolo($codComponenti){
      //ricevo un array di componenti, devo ciclarli ed inserirli uno alla volta
      if(isset($this->attiva)){
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $cont = 0;
        foreach($codComponenti as $componente){
          echo "<br>".$cont." -> ";
          $codComp = $componente["Codice Componente"];
          $desc = $componente["Descrizione"];
          $dim = $componente["Dimensione"];
          $mat = $componente["Materiale"];
          //query per inserire il componente nella tabella componenti
          $istruzione = "INSERT INTO `componente` (`cod_componente`, `descrizione`, `dimensione`, `materiale`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
          if (mysqli_query($connessione,$istruzione)) {
            echo $codComp." - COMPONENTE INSERITO CORRETTAMENTE <br>";
          }
          else {
            echo "<br>Error creating table: " . $connessione->error."<br>";
            echo "err 3 Componente già salvato - Vedere cod. componente: ".$codComp." <br>";
          }
          $cont = $cont +1;
        }
        //disconnessione
        mysqli_close($connessione);
      }
      else{
        //disconnessione
        mysqli_close($connessione);
        return false;
      }
      //fine funzione
      return true;
    }

    //RICERCA COMPONENTE INSERITO
    public function getComponente($ricComponente){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM componente WHERE cod_componente='".$ricComponente."';";
        $result=mysqli_query($connessione,$istruzione);
        //controllo se componente già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $arr_Componente = array("c1"=>$row["cod_componente"],"c2"=>$row["descrizione"],"c3"=>$row["dimensione"],"c4"=>$row["materiale"]);
          //passo l'array codificato
          echo json_encode($arr_Componente);
          //disconnessione
          mysqli_close($connessione);
          return true;
        }
        else {
          //disconnessione
          mysqli_close($connessione);
          //non ancora inserito
          return false;
        }
      }
      else{
        return false;
      }
    }

    //CREO LA RIGA IMPEGNO
    public function setImpegno($assImp){
      if(isset($this->attiva)){
        //variabili per la formattazione delle DATE da salvare
        $dataOra = date("Y-m-d"); //data odierna
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "INSERT INTO `impegno` (`cod_impegno`, `cliente`, `data_consegna`, `ordine`, `stato`, `data_creazione`)
                       VALUES ('".$assImp["i1"]."','".$assImp["i2"]."','".$assImp["i3"]."','".$assImp["i4"]."','0','".$dataOra."');";
        if (mysqli_query($connessione,$istruzione)) {
          echo "IMPEGNO INSERITO CORRETTAMENTE: ".$assImp["i1"]."<br>";
          //disconnessione
          mysqli_close($connessione);
          return true;
        }
        else {
          echo "Error: ".$assImp["i1"]." -> " . $istruzione . "<br>" . $connessione->error;
          //disconnessione
          mysqli_close($connessione);
          return false;
        }
      }
      else{
          return false;
      }
    }

    //COMPILO LA TABELLA PRODUZIONE PER LA MESSA IN PRODUZIONE
    public function setProduzione($imp,$art,$comp){
      $index=0;
      //attivo la connessione
      if(isset($this->attiva)){
          $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
          //CICLO per tutti gli articoli inseriti
          //CONTROLLO SE RIGA VUOTA??
          foreach($art as $articolo){
            $istruzione = "SELECT * FROM ".$articolo["ca"];
            $query = mysqli_query($connessione, $istruzione);
            if($query) {
              //se la query va a buon fine
              while($row = mysqli_fetch_assoc($query)){
                //$data[$index][0] = ID autoincrementale
                $data[$index][1] = $imp["i1"];                //impegno
                $data[$index][2] = $articolo["ca"];           //cod articolo
                $data[$index][3] = $articolo["q"];            //qt. articolo
                $data[$index][4] = $row['cod_componente'];    //codice del componente
                $data[$index][5] = '0';                       //qt. componente
                $data[$index][6] = $row['descrizione'];       //descrizione del componente
                $data[$index][7] = $row['dimensione'];        //dimensione del componente
                $data[$index][8] = $row['materiale'];         //materiale del componente
                $data[$index][9] = '0';                       //avanzamento del componente - 0 messa in produzione
                //incremento
                $index++;
              }
            }
            else{
              //TABELLA ARTICOLO NON TROVATA
              echo "<br> ARTICOLO: ".$articolo["ca"]." - non è stata trovata la tabella. Prego inserire il codice articolo.";
            }
          //fine ciclo articoli
          }
          //CICLO per tutti i componenti inseriti
          //CONTROLLO SE RIGA VUOTA??
          foreach($comp as $componente){
            $istruzione = "SELECT * FROM `componente` WHERE `cod_componente` = ".$componente["c1"];
            $query = mysqli_query($connessione, $istruzione);
            if($query) {
              //se la query va a buon fine
              $row = mysqli_fetch_assoc($query);
              //$data[$index][0] = ID autoincrementale
              $data[$index][1] = $imp["i1"];                //impegno
              $data[$index][2] = "-";                       //cod articolo
              $data[$index][3] = "-";                       //qt. articolo
              $data[$index][4] = $row['cod_componente'];    //codice del componente
              $data[$index][5] = '0';                       //qt. componente
              $data[$index][6] = $row['descrizione'];       //descrizione del componente
              $data[$index][7] = $row['dimensione'];        //dimensione del componente
              $data[$index][8] = $row['materiale'];         //materiale del componente
              $data[$index][9] = '0';                       //avanzamento del componente - 0 messa in produzione
              //incremento
              $index++;
            }
            else{
              //TABELLA ARTICOLO NON TROVATA
              echo "<br> COMPONENTE: ".$componente["c1"]." - non è stata trovata la riga. Prego inserire il codice componente.";
            }
          //fine ciclo componenti
          }
          //VADO A SALVARE LE INDEX RIGHE NELLA TABELLA PRODUZIONE
          //inserisco quante righe son state lette -> index to 0
          $x=$index-1;
          for($i=$x;$i>=0;--$i){
            //mi collego alla MEGATABELLA LAVORAZIONE
            $campi = "(cod_impegno, cod_articolo, qt_articolo, cod_componente, qt_componente, descrizione, dimensione, materiale, stato)";
            $valori = "('".$data[$i][1]."','".$data[$i][2]."','".$data[$i][3]."','".$data[$i][4]."','".$data[$i][5]."','".$data[$i][6]."',";
            $valori = $valori."'".$data[$i][7]."','".$data[$i][8]."','".$data[$i][9]."');";
            $istruzione = "INSERT INTO produzione ".$campi."  VALUES ".$valori;

            if($query = mysqli_query($connessione, $istruzione)){
              echo "<br>PRODUZIONE INSERITA CORRETTAMENTE: ".$data[$i][2]." - ".$data[$i][4];
            }
            else{
              echo "<br> Error: " . $istruzione . "<br>" . $connessione->error."<br>";
            }
          }
          //fine ciclo salvataggio
          return true;
        }
        else{
          return false;
        }
      }


      //RICERCA ARTICOLO INSERITO CON TUTTI I SUOI COMPONENTI
      public function getImpegno($ricImp){
        if(isset($this->attiva)){
          //apro la connessione al DB
          $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
          $istruzione = "SELECT * FROM impegno WHERE cod_impegno='".$ricImp."';";
          $result=mysqli_query($connessione,$istruzione);
          //controllo se impegno già salvato
          if (mysqli_num_rows($result)>0) {
            $row = mysqli_fetch_assoc($result);
            //impegno gia inserito -> salvo i dati dell' impegno
            //creo array dell' impegno
            $arr_Impegno = array("i1"=>$row["cod_impegno"],"i2"=>$row["cliente"],"i3"=>$row["data_consegna"],"i4"=>$row["ordine"]);
            //prendo la tabella produzione e stampo gli articoli collegati all' impegno
            $istruzione = "SELECT DISTINCT cod_articolo FROM prodotti ;";
            if($result=mysqli_query($connessione,$istruzione)){
              $i = 0;
              while($row = mysqli_fetch_assoc($result)) {
                //salvo le righe dei componenti inseriti nell' articolo cercato
                //array di componenti
                $arr_Componente[$i] = array("c1"=>$row["cod_componente"],"c2"=>$row["descrizione"],"c3"=>$row["dimensione"],"c4"=>$row["materiale"]);
                $i = $i+1;
              }
            }
            else {
              echo "Error creating table: " . $connessione->error."<br>";
            }
            //concateno gli array e li passo codificati
            $arrRisultato = array("aa"=>$arr_Articolo,"ac"=>$arr_Componente);
            echo json_encode($arrRisultato);
            //disconnessione
            mysqli_close($connessione);
            return true;
          }
          else{
            //disconnessione
            mysqli_close($connessione);
            //non ancora inserito
            return false;
          }
        }
      }

  //FINE CLASSE conn_DB
  }

//////////////////////////////////////////////////////////////////////////

//FINE CLASSE -> FUNZIONI DI RICEZIONE DATI
  if (isset($_POST['newAarticolo'])){
    //CREAZIONE NUOVO ARTICOLO - CILINDRO
    $varDB = new conn_DB();
    echo "<br>REPORT NEW ARTICOLO:<br>";
    //prendo l' array
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

  elseif(isset($_POST['articolo'])){
    //RICERCA SE E' GIA' STATO INSERITO IL CODICE ARTICOLO ED INVIO I DATI
    $varDB = new conn_DB();
    $ricercaArticolo = $_POST['articolo'];
    $varDB->getArticolo($ricercaArticolo);
  }

  elseif(isset($_POST['componente'])){
    //RICERCA SE E' GIA' STATO INSERITO IL CODICE COMPONENTE ED INVIO I DATI
    $varDB = new conn_DB();
    $ricercaComponente = $_POST['componente'];
    $varDB->getComponente($ricercaComponente);
  }

  elseif (isset($_POST['setCompSingolo'])) {
    //INSERISCO I COMPONENTI SINGOLI
    $varDB = new conn_DB();
    echo "<br>REPORT NEW COMPONENTE:<br>";
    //prendo l' array
    $assieme = json_decode($_POST['setCompSingolo'],true);
    $varDB->setCompSingolo($assieme);
  }

  elseif (isset($_POST['newImpegno'])) {
    //CREAZIONE NUOVO IMPEGNO
    $varDB = new conn_DB();
    echo "<br>REPORT NEW IMPEGNO:<br>";
    //prendo l' array
    $assieme = json_decode($_POST['newImpegno'],true);
    //separo le 3 componenti principali
    $impegno = $assieme["imp"];
    $articoli = $assieme["art"];
    $componenti = $assieme["comp"];
    //1-> CREO LA RIGA IMPEGNO
    $varDB->setImpegno($impegno);
    //2-> COMPILO LA TABELLA PRODUZIONE
    $varDB->setProduzione($impegno,$articoli,$componenti);
  }

  elseif (isset($_POST['impegno'])) {
    //AUTOCOMPILAZIONE TABELLA IMPEGNO
    $varDB = new conn_DB();
    $ricercaImpegno = $_POST['impegno'];
    $varDB->getImpegno($ricercaImpegno);
  }

?>
