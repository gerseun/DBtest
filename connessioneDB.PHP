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

    //CREAZIONE ARTICOLO (CILINDRO) - ok 25/11/19
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

    //INSERISCO I COMPONENTI NELLA TABELLA DELL' ARTICOLO - ok 25/11/19
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

    //ricerco ID ARTICOLO - ok 25/11/19
    public function getIDarticolo($art){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessioneID = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzioneID = "SELECT * FROM articolo WHERE cod_art='".$art."';";
        $resultID=mysqli_query($connessioneID,$istruzioneID);
        //controllo se articolo già salvato
        if (mysqli_num_rows($resultID)>0) {
          $rowID = mysqli_fetch_assoc($resultID);
          //articolo gia inserito -> salvo i dati articolo
          $idart=$rowID["id_art"];
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

    //ricerco ID COMPONENTE - ok 25/11/19
    public function getIDcomponente($comp){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessioneID = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzioneID = "SELECT * FROM componente WHERE cod_comp='".$comp."';";
        $resultID=mysqli_query($connessioneID,$istruzioneID);
        //controllo se articolo già salvato
        if (mysqli_num_rows($resultID)>0) {
          $rowID = mysqli_fetch_assoc($resultID);
          //articolo gia inserito -> salvo i dati articolo
          $idcomp=$rowID["id_comp"];
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

    //RICERCA ARTICOLO INSERITO
    public function getArticolo($ricArticolo){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM articolo WHERE cod_art='".$ricArticolo."';";
        $result=mysqli_query($connessione,$istruzione);
        //controllo se articolo già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //salvo l' ID articolo
          $id_articolo = $row["id_art"];
          //articolo gia inserito -> salvo i dati articolo
          $cod_articolo = $row["cod_art"];
          $descrizione = $row["desc_art"];
          $cliente = $row["cli_art"];
          $cod_cliente = $row["cod_cli_art"];
          //creo array dell' articolo
          $arr_Articolo = array("id_art"=>$id_articolo, "cod_art"=>$cod_articolo,"desc_art"=>$descrizione,"cli_art"=>$cliente,"cod_cli_art"=>$cod_cliente);
          //disconnessione
          mysqli_close($connessione);
          //non ancora inserito
          return $arr_Articolo;
        }
        else {
          echo "Error creating table: " . $connessione->error."<br>";
          //disconnessione
          mysqli_close($connessione);
          //non ancora inserito
          return false;
        }
      }
      else{
        //disconnessione
        mysqli_close($connessione);
        //non ancora inserito
        return false;
      }
    }

    //RICERCA I COMPONENTI INSERITI IN UN DATO ID_ARTICOLO  - ok 25/11/19
    public function getCompInArticolo($ric_id_articolo){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //seleziono gli id_componenti dell' articolo ricercato
        $istruzione = "SELECT * FROM articolo_componenti INNER JOIN componente ON componente.ID_comp=articolo_componenti.ID_comp  WHERE articolo_componenti.id_art = '".$ric_id_articolo."';";
        $result=mysqli_query($connessione,$istruzione);
        //variabili array COMPONENTI
        $cont = 0;
        $arr_Componente[] = array();
        //controllo se componente già salvato
        if (mysqli_num_rows($result)>0) {
          //ciclo tutti i componenti
          while($row = mysqli_fetch_assoc($result)) {
            //articolo inserito -> salvo i dati dei componenti
            $arr_Componente[$cont] = array("cod_comp"=>$row["cod_comp"],"desc_comp"=>$row["desc_comp"],"dim_comp"=>$row["dim_comp"],"mat_comp"=>$row["mat_comp"],"qt_comp"=>$row["qt_comp"]);
            $cont= $cont + 1;
          }
          //disconnessione
          mysqli_close($connessione);
          //trasmetto i componenti
          return $arr_Componente;
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

    //SALVO I COMPONENTI SINGOLI AL DI FUORI DELL' ASSIEME  - ok 27/11/19
    public function setCompSingolo($codComponenti){
      //ricevo un array di componenti, devo ciclarli ed inserirli uno alla volta
      if(isset($this->attiva)){
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //variabili per la formattazione delle DATE da salvare
        $dataOra = date("Y-m-d"); //data odierna
        $cont = 0;
        foreach($codComponenti as $componente){
          //ciclo tutti i componenti
          echo "<br>".$cont." -> ";
          $codComp = $componente["cod_comp"];
          $desc = $componente["desc_comp"];
          $dim = $componente["dim_comp"];
          $mat = $componente["mat_comp"];
          //query per inserire il componente nella tabella componenti
          $istruzione = "INSERT INTO `componente` (`cod_comp`, `desc_comp`, `dim_comp`, `mat_comp`, `pos_comp`, `data_comp`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."','0','". $dataOra."');";
          if (mysqli_query($connessione,$istruzione)) {
            echo $codComp." inserito nell' anagrafica <br>";
          }
          else {
            echo "Errore inserimento riga articolo: ".$codComp." - ". $connessione->error."<br>";
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

    //RICERCA COMPONENTE INSERITO - ok 25/11/19
    public function getComponente($ricComponente){
      if(isset($this->attiva)){
        //apro la connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzione = "SELECT * FROM componente WHERE cod_comp='".$ricComponente."';";
        $result=mysqli_query($connessione,$istruzione);
        //controllo se articolo già salvato
        if (mysqli_num_rows($result)>0) {
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $arrayComp[0] = array("cod_comp"=>$row["cod_comp"],"desc_comp"=>$row["desc_comp"],"dim_comp"=>$row["dim_comp"],"mat_comp"=>$row["mat_comp"]);
          mysqli_close($connessione);
          return $arrayComp;
        }
        else {
          mysqli_close($connessione);
          return false;
        }
      }
      else{
        mysqli_close($connessione);
        return false;
      }
    }

    //CREO LA RIGA IMPEGNO  - ok 02/12/19
    public function setImpegno($assImp){
      if(isset($this->attiva)){
        //variabili per la formattazione delle DATE da salvare
        $dataOra = date("Y-m-d"); //data creazione impegno sul gestionale
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //inserisco la riga nuovo impegno
        $istruzione = "INSERT INTO `impegno` (`cod_imp`, `cliente`, `cod_ord_cli`, `data_ord`, `data_comp`)
                       VALUES ('".$assImp["cod_imp"]."','".$assImp["cliente"]."','".$assImp["cod_ord_cli"]."','".$assImp["data_ord"]."','".$dataOra."');";
        if (mysqli_query($connessione,$istruzione)) {
          echo "IMPEGNO INSERITO CORRETTAMENTE: ".$assImp["cod_imp"]."<br>";
          //vado a prendere l' ID riga dell' impegno inserito
          $istruzione = "SELECT id_imp FROM impegno WHERE cod_imp='".$assImp["cod_imp"]."';";
          $result = mysqli_query($connessione,$istruzione);
          $row = mysqli_fetch_assoc($result);
          //articolo gia inserito -> salvo i dati articolo
          $id_impegno = $row["id_imp"];
          //disconnessione
          mysqli_close($connessione);
          return $id_impegno;
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

    //SETTO LE RIGHE_IMP PER LA PRODUZIONE DI ARTICOLI  - ok 03/12/19
    public function setArticoloInImpegno($artAssieme, $idImp){
      if(isset($this->attiva)){
        $varDB = new conn_DB();
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //ciclo tutte le righe articolo
        foreach($artAssieme as $rigaArticolo){
          //cerco l' id articolo
          $id_art = $varDB->getIDarticolo($rigaArticolo["cod_art"]);
          if($id_art){
            //echo $rigaArticolo["cod_art"].": CODICE ARTICOLO INSERITO";
            //articolo già inserito, vado a compilare la tabella "riga_imp"
            $QTart = $rigaArticolo["qt_art"];
            $dataCons = $rigaArticolo["data_cons_art"];
            //riga sql di creazione
            $istruzione = "INSERT INTO `riga_imp` (`id_imp`, `id_art`, `qt_art`, `data_cons_art`) VALUES ('".$idImp."', '".$id_art."', '".$QTart."', '".$dataCons."');";

            if(mysqli_query($connessione,$istruzione)){
              //ok
              echo "<br>".$rigaArticolo["cod_art"].": ARTICOLO IMPEGNATO";
              //set produzione ARTICOLI
              $idRigaImp = $connessione->insert_id;
              $varDB->setProduzione($idRigaImp, $id_art, $rigaArticolo["qt_art"]);
            }
            else{
              //non inserito riga imp
              echo "<br>".$rigaArticolo["cod_art"].": ERRORE CREAZIONE ARTICOLO IN IMPEGNO";
            }

          }
          else{
            echo "<br>".$rigaArticolo["cod_art"].": CODICE ARTICOLO INESISTENTE";
          }
        }
        //disconnessione
        mysqli_close($connessione);
        return true;

      }
      else{
          return false;
      }
    }

    //SETTO LE RIGHE_IMP_COMP PER LA PRODUZIONE DI COMPONENTI SINGOLI DA PRODUZIONE  - ok 03/12/19
    public function setComponenteInImpegno($compAssieme, $idImp){
      if(isset($this->attiva)){
        $varDB = new conn_DB();
        //connessione al DB
        $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        //ciclo tutte le righe articolo
        foreach($compAssieme as $rigaComponente){
          //cerco l' id articolo
          $id_comp = $varDB->getIDcomponente($rigaComponente["cod_comp"]);
          if($id_comp){
            //echo $rigaArticolo["cod_art"].": CODICE ARTICOLO INSERITO";
            //articolo già inserito, vado a compilare la tabella "riga_imp"
            $istruzione = "INSERT INTO `riga_imp_comp` (`id_imp`, `id_comp`, `qt_comp`, `data_cons_comp`, `id_produzione`)
                           VALUES ('".$idImp."','".$id_art."','".$rigaArticolo["qt_art"]."','".$rigaArticolo["data_cons_art"]."', '0');";
               if(mysqli_query($connessione,$istruzione)){
                 //ok
                 echo "<br>".$rigaComponente["cod_comp"].": COMPONENTE IMPEGNATO";
               }
               else{
                 //non inserito riga imp
                 echo "<br>".$rigaComponente["cod_comp"].": ERRORE CREAZIONE COMPONENTE IN IMPEGNO";
               }
             }
             else{
               echo "<br>".$rigaComponente["cod_comp"].": CODICE COMPONENTE INESISTENTE";
             }
        }
        //disconnessione
        mysqli_close($connessione);
        return true;
      }
      else{
          return false;
      }
    }


    //COMPILO LA TABELLA PRODUZIONE PER LA MESSA IN PRODUZIONE  - inizio 03/12/19
    public function setProduzione($IDimp,$IDart,$QTcomp){
      if(isset($this->attiva)){
        echo "<br> INIZIO PRODUZIONE <br>";
        //ricevo dalla tabella articolo_componenti le righe da mettere in produzione
        //connessione al DB
        $connessionePROD = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
        $istruzionePROD = "SELECT * FROM articolo_componenti WHERE id_art = '".$IDart."';";
        $resultPROD = mysqli_query($connessionePROD,$istruzionePROD);
        //ricevo tutte le righe
        while($righePROD = mysqli_fetch_assoc($resultPROD)) {
          //riga COMPONENTE
          $QTcompTemp = $QTcomp * $righePROD["qt_comp"];
          $istruzione = "INSERT INTO riga_dett (id_riga_imp, id_comp, qt_comp, id_produzione) VALUES ('".$IDimp."', '".$righePROD["id_comp"]."', '".$QTcompTemp."', '0');";
          echo $istruzione."<br>";
          $inserimento = mysqli_query($connessionePROD,$istruzione);
        }
        echo "<br> FINE PRODUZIONE <br>";
        //disconnessione
        mysqli_close($connessionePROD);
        return true;
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

    //RICEVO TUTTI I CODICI COMPONENTE  - ok 25/11/19
    public function getCodComponenti(){
      //apro la connessione al DB
      $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
      $istruzione = "SELECT cod_comp FROM componente";
      $result=mysqli_query($connessione,$istruzione);
      //ricevo tutte le righe
      $cont = 0;
      $arr_Componente[] = array();
      while($row = mysqli_fetch_assoc($result)) {
        //salvo l' arrey codici componenti
        $arr_Componente[$cont] = $row["cod_comp"];
        $cont = $cont + 1;
      }
      //se non sono presenti righe, passo casella VUOTA
      if($cont == 0){
        $arr_Componente[0] = "";
      }
      //disconnessione
      mysqli_close($connessione);
      return $arr_Componente;
    }

    //RICEVO TUTTI I CODICI ARTICOLO  - ok 25/11/19
    public function getCodArticoli(){
      //apro la connessione al DB
      $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
      $istruzione = "SELECT cod_art FROM articolo";
      $result=mysqli_query($connessione,$istruzione);
      //ricevo tutte le righe
      $cont = 0;
      $arr_Articolo[] = array();
      while($row = mysqli_fetch_assoc($result)) {
        //salvo l' arrey codici componenti
        $arr_Articolo[$cont] = $row["cod_art"];
        $cont = $cont + 1;
      }
      //se non sono presenti righe, passo casella VUOTA
      if($cont == 0){
        $arr_Articolo[0] = "";
      }
      //disconnessione
      mysqli_close($connessione);
      return $arr_Articolo;
    }

    //RICEVO TUTTI I CODICI ARTICOLO  - ok 02/12/19
    public function getCodImpegni(){
      //apro la connessione al DB
      $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
      $istruzione = "SELECT cod_imp FROM impegno";
      $result=mysqli_query($connessione,$istruzione);
      //ricevo tutte le righe
      $cont = 0;
      $arr_Impegno[] = array();
      while($row = mysqli_fetch_assoc($result)) {
        //salvo l' array codici impegni
        $arr_Impegno[$cont] = $row["cod_imp"];
        $cont = $cont + 1;
      }
      //se non sono presenti righe, passo casella VUOTA
      if($cont == 0){
        $arr_Impegno[0] = "";
      }
      //disconnessione
      mysqli_close($connessione);
      return $arr_Impegno;
    }
  //FINE CLASSE conn_DB
  }

//////////////////////////////////////////////////////////////////////////

//FINE CLASSE -> FUNZIONI DI RICEZIONE DATI
  //apertura pagina inserimento  - ok 25/11/19
  if(isset($_POST['firstCall'])){
    //APERTURA PAGINA INSERIMENTO DATI
    $varDB = new conn_DB();
    //array impegni
    $impegni = $varDB->getCodImpegni();
    //array componenti
    $componenti = $varDB->getCodComponenti();
    //array articoli
    $articoli = $varDB->getCodArticoli();
    //concatenazione array
    $arrCodici = array("list_imp"=>$impegni, "list_art"=>$articoli, "list_comp"=>$componenti);
    $arrRisultato = array("first_call"=>$arrCodici);
    echo json_encode($arrRisultato);
  }

  //ricerca articolo gia inserito  - ok 25/11/19
  elseif(isset($_POST['newArticolo_search_art'])){

    //RICERCO IL CODICE ARTICOLO ED INVIO I DATI ARTICOLO E COMPONENTI
    $varDB = new conn_DB();
    //ricerco articolo
    $ricercaArticolo = $_POST['newArticolo_search_art'];
    $articolo = $varDB->getArticolo($ricercaArticolo);
    //ricerco componenti contenuti nell' articolo
    $ricercaIDart = $articolo["id_art"];
    $componenti = $varDB->getCompInArticolo($ricercaIDart);
    //creo array di risposta
    $art[0] = array("cod_art"=>$articolo["cod_art"], "desc_art"=>$articolo["desc_art"], "cli_art"=>$articolo["cli_art"], "cod_cli_art"=>$articolo["cod_cli_art"]);
    $artComp = array("t_art"=>$art, "t_comp"=>$componenti);
    $risposta = array("newArticolo"=>$artComp);
    //consegno il pacco
    echo json_encode($risposta);
  }

  elseif(isset($_POST['search_art'])){
    //RICERCO IL CODICE ARTICOLO ED INVIO I DATI ARTICOLO E COMPONENTI
    $varDB = new conn_DB();
    //ricerco articolo
    $ricercaArticolo = $_POST['search_art'];
    $articolo = $varDB->getArticolo($ricercaArticolo);
    //ricerco componenti contenuti nell' articolo
    $ricercaIDart = $articolo["id_art"];
    $componenti = $varDB->getCompInArticolo($ricercaIDart);
    //creo array di risposta
    $art[0] = array("cod_art"=>$articolo["cod_art"], "desc_art"=>$articolo["desc_art"], "cli_art"=>$articolo["cli_art"], "cod_cli_art"=>$articolo["cod_cli_art"]);
    $artComp = array("t_art"=>$art, "t_comp"=>$componenti);
    $risposta = array("newArticolo"=>$artComp);
    //consegno il pacco
    echo json_encode($risposta);
  }

  //ricerca componente gia inserito  - ok 25/11/19
  elseif(isset($_POST['newArticolo_search_comp'])){
    //RICERCO IL CODICE COMPONENTE ED INVIO I DATI
    $varDB = new conn_DB();
    //ricerco componenti contenuti nell' articolo
    $ricercaComp = $_POST['newArticolo_search_comp'];
    $componente = $varDB->getComponente($ricercaComp);
    //creo array di risposta
    $art[0] = array("cod_art"=>"", "desc_art"=>"", "cli_art"=>"", "cod_cli_art"=>"");
    $artComp = array("t_art"=>$art, "t_comp"=>$componente);
    $risposta = array("newArticolo"=>$artComp);
    //consegno il pacco
    echo json_encode($risposta);
  }

  //inserimento nuovo articolo  - ok 25/11/19
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

    echo "<br>".$codArticolo."<br>";

    //setto l' articolo
    $varDB->setArticolo($codArticolo,$descrizione,$cliente,$codCliente);
    //vado a salvare le componenti del cilindro
    $varDB->setComponenteInArticolo($codArticolo, $componenti);
  }

  //inserimento nuovo componente  - ok 27/11/19
  elseif (isset($_POST['newComponente'])) {
    //INSERISCO I COMPONENTI SINGOLI
    $varDB = new conn_DB();
    echo "<br>REPORT NEW COMPONENTE:<br>";
    //prendo l' array dei componenti
    $assieme = json_decode($_POST['newComponente'],true);
    $componenti = $assieme["t_comp"];
    $varDB->setCompSingolo($componenti);
  }


  //inserimento nuovo impegno - ok 03/12/19
  elseif (isset($_POST['newImpegno'])) {
    //CREAZIONE NUOVO IMPEGNO
    $varDB = new conn_DB();
    echo "<br>REPORT NEW IMPEGNO:<br>";
    //prendo l' array
    $assieme = json_decode($_POST['newImpegno'],true);
    //separo le componenti principali
    $impegno = $assieme["t_imp"];
    $articoli = $assieme["t_art"];
    $componenti = $assieme["t_comp"];
    //1-> CREO LA RIGA IMPEGNO
    $id_imp = $varDB->setImpegno($impegno[0]);
    //2-> CICLO GLI ARTICOLI-COMPONENTI DA INSERIRE NELL' IMPEGNO
    if($id_imp){
      //se l' impegno è stato inserito correttamente
      //vado ad inserire le righe articolo
      $varDB->setArticoloInImpegno($articoli, $id_imp);

      //vado ad inserire le righe componente
      //$varDB->setComponenteInImpegno($componenti, $id_imp);
    }
  }

  elseif (isset($_POST['impegno'])) {
    //AUTOCOMPILAZIONE TABELLA IMPEGNO
    $varDB = new conn_DB();
    $ricercaImpegno = $_POST['impegno'];
    $varDB->getImpegno($ricercaImpegno);
  }

?>
