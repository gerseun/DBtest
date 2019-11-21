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
        //variabili per la formattazione delle DATE da salvare
        $dataOra = date("Y-m-d"); //data odierna
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //inserisco la riga articolo
        $istruzione = "INSERT INTO `articolo` (`cod_art`, `desc_art`, `cli_art`, `cod_cli_art`, `kit_art`, `data_art`) VALUES ('".$codArt."','".$desc."','".$cli."','".$codCli."','0','". $dataOra."');";
        if (mysqli_query($connessione,$istruzione)) {
          echo "ANAGRAFICA ARTICOLO INSERITA CORRETTAMENTE <br>";
        }
        else {
          echo "Errore nell' inserimento articolo: ".$codArt." - ".$connessione->error."<br><br>";
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
      $varDB = new conn_DB();
      $flag_anag_comp = false;  //false -> nuovo comp
                                //true -> anagrafica già inserita
      //variabili per la formattazione delle DATE da salvare
      $dataOra = date("Y-m-d"); //data odierna
      //prendo l' indice di articolo
      $idArt = $varDB->getIDarticolo($codArt);
      if(isset($this->attiva)){
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //ciclo tutti i componenti
        $cont = 0;
        foreach($codComponenti as $componente){
          echo "<br>".$cont."<br>";
          //salvo variabili del componente
          $codComp = $componente["cod_comp"];
          $desc = $componente["desc_comp"];
          $dim = $componente["dim_comp"];
          $mat = $componente["mat_comp"];
          $qt = $componente["qt_comp"];
          //controllo se l' anagrafica componente esiste già
          $istruzione = "SELECT * FROM componente WHERE cod_comp = '".$codComp."'";
          $result=mysqli_query($connessione,$istruzione);
          $flag_anag_comp = false;
          if (mysqli_num_rows($result)>0) {
            //già presente nell' anagrafica
            $flag_anag_comp = true;
            echo $codComp." già presente nell' anagrafica <br>";
          }
          else {
            //salvo il codice componente nell' anagrafica componente
            $flag_anag_comp = false;
            $istruzione = "INSERT INTO `componente` (`cod_comp`, `desc_comp`, `dim_comp`, `mat_comp`, `pos_comp`, `data_comp`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."','0','". $dataOra."');";
            if (mysqli_query($connessione,$istruzione)) {
              echo $codComp." inserito nell' anagrafica <br>";
            }
            else {
              echo "Errore inserimento riga articolo: ".$codComp." - ". $connessione->error."<br>";
            }
          }
          //prendo l' indice di componente
          $idComp = $varDB->getIDcomponente($codComp);
          //salvo la riga nella tabella articolo_componenti
          $istruzione = "INSERT INTO `articolo_componenti` (`id_art`, `id_comp`, `qt_comp`)VALUES ('".$idArt."','".$idComp."','".$qt."');";
          if (mysqli_query($connessione,$istruzione)) {
            echo $codComp.": inserito nella descrizione articolo ".$codArt."<br>";
          }
          else {
            echo "Errore inserimento descrizione articolo: ".$codComp." - ". $connessione->error."<br>";
          }
          //incremento il contatore
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

    //ricerco ID ARTICOLO
    public function getIDarticolo($art){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessioneID = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM articolo WHERE cod_art='".$art."';";
        $result=mysqli_query($connessioneID,$istruzione);
        //controllo se articolo già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $idart=$row["id_art"];
          mysqli_close($connessioneID);
          return $idart;
        }
        else {
          mysqli_close($connessioneID);
          return false;
        }
      }
      else{
        mysqli_close($connessioneID);
        return false;
      }
    }

    //ricerco ID COMPONENTE
    public function getIDcomponente($comp){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessioneID = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM componente WHERE cod_comp='".$comp."';";
        $result=mysqli_query($connessioneID,$istruzione);
        //controllo se articolo già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $idcomp=$row["id_comp"];
          mysqli_close($connessioneID);
          return $idcomp;
        }
        else {
          mysqli_close($connessioneID);
          return false;
        }
      }
      else{
        mysqli_close($connessioneID);
        return false;
      }
    }

    //RICERCA ARTICOLO INSERITO CON TUTTI I SUOI COMPONENTI
    public function getArticolo($ricArticolo){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $cod_articolo = $descrizione = $cliente = $cod_cliente = "";
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM articolo WHERE cod_art='".$ricArticolo."';";
        $result=mysqli_query($connessione,$istruzione);
        //controllo se articolo già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $cod_articolo = $row["cod_art"];
          $descrizione = $row["desc_art"];
          $cliente = $row["cli_art"];
          $cod_cliente = $row["cod_cli_art"];
          //creo array dell' articolo
          $arr_Articolo[0] = array("cod_art"=>$cod_articolo,"desc_art"=>$descrizione,"cli_art"=>$cliente,"cod_cli_art"=>$cod_cliente);

          //prendo la tabella articolo e stampo i componenti già salvati
          $istruzione = "SELECT * FROM ".$ricArticolo.";";
          if($result=mysqli_query($connessione,$istruzione)){
            $i = 0;
            $arr_Componente[0]=array("cod_comp"=>"","desc_comp"=>"","dim_comp"=>"","mat_comp"=>"");

            while($row = mysqli_fetch_assoc($result)) {
              //salvo le righe dei componenti inseriti nell' articolo cercato
              //array di componenti
              $arr_Componente[$i] = array("cod_comp"=>$row["cod_comp"],"desc_comp"=>$row["desc_comp"],"dim_comp"=>$row["dim_comp"],"mat_comp"=>$row["mat_comp"]);
              $i = $i+1;
            }
          }
          else {
            echo "Error creating table: " . $connessione->error."<br>";
          }
          //concateno gli array e li passo codificati
          $arrRisultato = array("t_art"=>$arr_Articolo,"t_comp"=>$arr_Componente);
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
          $codComp = $componente["cod_comp"];
          $desc = $componente["desc_comp"];
          $dim = $componente["dim_comp"];
          $mat = $componente["mat_comp"];
          //query per inserire il componente nella tabella componenti
          $istruzione = "INSERT INTO `componente` (`cod_comp`, `desc_comp`, `dim_comp`, `mat_comp`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
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
        $istruzione = "SELECT * FROM componente WHERE cod_comp='".$ricComponente."';";
        $result=mysqli_query($connessione,$istruzione);
        //controllo se componente già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $arr_Componente = array("c1"=>$row["cod_comp"],"c2"=>$row["desc_comp"],"c3"=>$row["dim_comp"],"c4"=>$row["mat_comp"]);
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
        //cancello la riga precedente per aggiornare
        $istruzione = "DELETE FROM impegno WHERE cod_imp = ".$assImp["cod_imp"];
        mysqli_query($connessione,$istruzione);
        //inserisco la riga nuovo impegno
        $istruzione = "INSERT INTO `impegno` (`cod_imp`, `cli_imp`, `cons_imp`, `ord_imp`, `stato_imp`, `creazione_imp`)
                       VALUES ('".$assImp["cod_imp"]."','".$assImp["cli_imp"]."','".$assImp["cons_imp"]."','".$assImp["ord_imp"]."','0','".$dataOra."');";
        if (mysqli_query($connessione,$istruzione)) {
          echo "IMPEGNO INSERITO CORRETTAMENTE: ".$assImp["cod_imp"]."<br>";
          //disconnessione
          mysqli_close($connessione);
          return true;
        }
        else {
          echo "Error: ".$assImp["cod_imp"]." -> " . $istruzione . "<br>" . $connessione->error;
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
          //cancello tutte le righe relative all' impegno INSERITO
          $istruzione = "DELETE FROM produzione WHERE imp_prod = ".$imp["cod_imp"];
          $query = mysqli_query($connessione, $istruzione);
          //CICLO per tutti gli articoli inseriti
          //CONTROLLO SE RIGA VUOTA??
          foreach($art as $articolo){
            $istruzione = "SELECT * FROM ".$articolo["cod_art"];
            $query = mysqli_query($connessione, $istruzione);
            if($query) {
              //se la query va a buon fine
              while($row = mysqli_fetch_assoc($query)){
                //$data[$index][0] = ID autoincrementale
                $data[$index][1] = $imp["cod_imp"];                //impegno
                $data[$index][2] = $articolo["cod_art"];           //cod articolo
                $data[$index][3] = $articolo["desc_art"];            //descrizione articolo
                $data[$index][4] = $articolo["qt_art_prod"];            //qt. articolo
                $data[$index][5] = $row['cod_comp'];    //codice del componente
                $data[$index][6] = '0';                       //qt. componente
                $data[$index][7] = $row['desc_comp'];       //descrizione del componente
                $data[$index][8] = $row['dim_comp'];        //dimensione del componente
                $data[$index][9] = $row['mat_comp'];         //materiale del componente
                $data[$index][10] = '0';                      //avanzamento del componente - 0 messa in produzione
                //incremento
                $index++;
              }
            }
            else{
              //TABELLA ARTICOLO NON TROVATA
              echo "<br> ARTICOLO: ".$articolo["cod_art"]." - non è stata trovata la tabella. Prego inserire il codice articolo.";
            }
          //fine ciclo articoli
          }
          //CICLO per tutti i componenti inseriti
          //CONTROLLO SE RIGA VUOTA??
          foreach($comp as $componente){
            $istruzione = "SELECT * FROM `componente` WHERE `cod_comp` = ".$componente["cod_comp"];
            $query = mysqli_query($connessione, $istruzione);
            if($query) {
              //se la query va a buon fine
              $row = mysqli_fetch_assoc($query);
              //$data[$index][0] = ID autoincrementale
              $data[$index][1] = $imp["cod_imp"];                //impegno
              $data[$index][2] = "-";                       //cod articolo
              $data[$index][3] = "-";                       //desc articolo
              $data[$index][4] = "-";                       //qt. articolo
              $data[$index][5] = $row['cod_comp'];    //codice del componente
              $data[$index][6] = $row['desc_comp'];       //descrizione del componente
              $data[$index][7] = $row['dim_comp'];        //dimensione del componente
              $data[$index][8] = $row['mat_comp'];         //materiale del componente
              $data[$index][9] = '0';                       //qt. componente
              $data[$index][10] = '0';                       //avanzamento del componente - 0 messa in produzione
              //incremento
              $index++;
            }
            else{
              //TABELLA ARTICOLO NON TROVATA
              echo "<br> COMPONENTE: ".$componente["cod_comp"]." - non è stata trovata la riga. Prego inserire il codice componente.";
            }
          //fine ciclo componenti
          }
          //VADO A SALVARE LE INDEX RIGHE NELLA TABELLA PRODUZIONE
          //inserisco quante righe son state lette -> index to 0
          $x=$index-1;
          for($i=$x;$i>=0;--$i){
            //mi collego alla MEGATABELLA LAVORAZIONE
            $campi = "(imp_prod, art_prod, desc_art_prod, qt_art_prod, comp_prod, desc_comp_prod, dim_comp_prod, dim_comp_prod, mat_comp_prod, qt_comp_prod, stato_prod)";
            $valori = "('".$data[$i][1]."','".$data[$i][2]."','".$data[$i][3]."','".$data[$i][4]."','".$data[$i][5]."','".$data[$i][6]."',";
            $valori = $valori."'".$data[$i][7]."','".$data[$i][8]."','".$data[$i][9]."','".$data[$i][10]."');";
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
      //sistemare gli indici
      public function getImpegno($ricImp){
        if(isset($this->attiva)){
          $arr_Articolo[0] = array("ca"=>"","d"=>"","q"=>"");
          $arr_Componente[0] = array("c1"=>"","c2"=>"","c3"=>"","c4"=>"","c5"=>"");

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
            $istruzione = "SELECT DISTINCT cod_articolo, desc_articolo, qt_articolo FROM produzione WHERE cod_impegno = '".$ricImp."';";
            if($result=mysqli_query($connessione,$istruzione)){
              $i = 0;
              while($row = mysqli_fetch_assoc($result)) {
                //salvo le righe degli articoli trovati nell' impegno
                if($row["cod_articolo"]!= "-"){
                  //se diversa da "-" allora è un codice cilindro
                  //array di articoli
                  $arr_Articolo[$i] = array("ca"=>$row["cod_articolo"],"d"=>$row["desc_articolo"],"q"=>$row["qt_articolo"]);
                  $i = $i+1;
                }
              }
            }
            else {
              echo "<br>Error creating table: " . $connessione->error."<br>";
            }
            //prendo la tabella produzione e stampo i componenti singoli collegati all' impegno
            $istruzione = "SELECT FROM produzione WHERE cod_impegno = '".$ricImp."' AND cod_articolo = '-' ;";
            if($result=mysqli_query($connessione,$istruzione)){
              $i = 0;
              while($row = mysqli_fetch_assoc($result)) {
                //salvo le righe degli articoli trovati nell' impegno
                $arr_Articolo[$i] = array("c1"=>$row["cod_componente"],"c2"=>$row["descrizione"],"c3"=>$row["dimensione"],"c4"=>$row["materiale"],"c4"=>$row["qt_componente"]);
                $i = $i+1;
              }
            }
          }
          else {
            echo "<br>Error creating table: " . $connessione->error."<br>";
          }

          //concateno gli array e li passo codificati
          $arrRisultato = array("imp"=>$arr_Impegno,"art"=>$arr_Articolo,"comp"=>$arr_Componente);
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

    //RICEVO TUTTI I CODICI COMPONENTE
    public function getCodComponente(){
      //apro la connessione al DB
      $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
      $istruzione = "SELECT cod_comp FROM componente";
      $result=mysqli_query($connessione,$istruzione);
      //ricevo tutte le righe
      $row = mysqli_fetch_assoc($result);
      while($row = mysqli_fetch_assoc($result)) {
        //salvo l' arrey codici componenti
        $arr_Componente = array($row["cod_comp"]);
      }
      //disconnessione
      mysqli_close($connessione);
      return $arr_Componente;
    }

  //FINE CLASSE conn_DB
  }

//////////////////////////////////////////////////////////////////////////

//FINE CLASSE -> FUNZIONI DI RICEZIONE DATI
  if(isset($_POST['first_call'])){
    //APERTURA PAGINA INSERIMENTO DATI
    $varDB = new conn_DB();

  }
  elseif (isset($_POST['newArticolo'])){
    //CREAZIONE NUOVO ARTICOLO - CILINDRO
    $varDB = new conn_DB();
    echo "<br>REPORT NEW ARTICOLO:<br>";
    //prendo l' array
    $assieme = json_decode($_POST['newArticolo'],true);

    //setArticolo -> cilindro - componenti
    $articolo = $assieme["t_art"];
    $componenti = $assieme["t_comp"];
    //variabili del cilindro per settarlo
    $codArticolo = $articolo[0]["cod_art"];
    $descrizione = $articolo[0]["desc_art"];
    $cliente = $articolo[0]["cli_art"];
    $codCliente = $articolo[0]["cod_cli_art"];

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
