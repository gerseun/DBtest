<?php
    class conn_DB{
        public $lavorazioni = array("DA SETTARE","DA TAGLIARE","DA ORDINARE","ORDINATO","MAT ARRIVATO","TAGLIATO","CARICO GREZZO", "CARICO PARZ LAVORATO", "CARICO FINITO","INIZIO PROD ISORELLA","FINE PROD ISORELLA");
        // parametri per la connessione al database
        private $nomehost = "localhost";
        private $nomeuser = "root";
        private $password = "";
        private $dbName = "db_progresso";

        // controllo sulle connessioni attive
        private $attiva = false;

        // funzione per la connessione a MySQL
        public function connetti(){
            if(!$this->attiva){
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
            }
            else{
                return true;
            }
        }

        // funzione per la chiusura della connessione
        public function disconnetti(){
            if($this->attiva){
                if(mysqli_close()){
                    $this->attiva = false;
                    return true;
                }
                else{
                    return false;
                }
            }
        }

        //funzione per l'esecuzione delle query
        public function query($sql){
            if(isset($this->attiva)){
                $sql = mysqli_query($sql) or die (mysqli_error());
                return $sql;
            }
            else{
                return false;
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
                        echo "Error creating table: " . $connessione->error;
                    }
                }
                else {
                    echo "ERRORE CONNESSIONE AL DATABASE, VERIFICARE CHE SIA UN NUOVO CILINDRO!";
                    return false;
                }
                //disconnessione
                mysqli_close($connessione);
            }
            else{
                return false;
            }
        }

        //RICERCA SE IL CILINDRO E' STATO INSERITO
        public function isSetArticolo($codArtRicerca){
            if(isset($this->attiva)){
                //connessione al DB
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "SELECT * FROM articolo WHERE cod_articolo='".$codArtRicerca."';";
                $result=mysqli_query($connessione,$istruzione);
                if (mysqli_num_rows($result)>0) {
                    return true;
                }
                else {
                    return false;
                }
                //disconnessione
                mysqli_close($connessione);
            }
            else{
                return false;
            }
        }

        //STAMPO I COMPONENTI INSERITI IN UN DATO CILINDRO
        public function getComponenti($codCil){
            if(isset($this->attiva)){
                //connessione al DB
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "SELECT * FROM ".$codCil.";";
                $result = mysqli_query($connessione, $istruzione);
                if (mysqli_num_rows($result) > 0) {
                    // stampo i risultati
                    echo "<table border='1'><tr><th>DIS. PARTICOLARE</th><th>DESCRIZIONE</th><th>DIMENSIONE</th><th>MATERIALE</th></tr>";
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr><td>".$row["cod_componente"]. "</td><td>" . $row["descrizione"]. "</td><td>" . $row["dimensione"]. "</td><td>" . $row["materiale"]. "</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "0 results";
                }
            }
            else{
                return false;
            }
        }

        //RICERCA SE IL COMPONENTE E' STATO INSERITO
        public function isSetComponente($codCompRicerca, $codCilInser){
            //creo le variabili Componente
            $codComponente = $descrizione = $dimensione = $materiale = "";
            if(isset($this->attiva)){
                //connessione al DB
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "SELECT * FROM componente WHERE cod_componente='".$codCompRicerca."';";
                $result=mysqli_query($connessione,$istruzione);
                if (mysqli_num_rows($result)>0) {
                    //componente gia inserito -> stampo il componente e lo inserisco nel cilindro
                    // stampo i risultati
                    //echo "<h2>Dati trovati:</h2>";
                    //echo "<table border='1'><tr><th>DIS. PARTICOLARE</th><th>DESCRIZIONE</th><th>DIMENSIONE</th><th>MATERIALE</th></tr>";
                    while($row = mysqli_fetch_assoc($result)) {
                        $codComponente = $row["cod_componente"];
                        $descrizione = $row["descrizione"];
                        $dimensione = $row["dimensione"];
                        $materiale = $row["materiale"];
                        //echo "<tr><td>".$row["cod_componente"]. "</td><td>" . $row["descrizione"]. "</td><td>" . $row["dimensione"]. "</td><td>" . $row["materiale"]. "</td></tr>";
                    }
                    //echo "</table>";
                    //inserisco i componenti nella tabella del cilindro
                    $istruzione = "INSERT INTO ".$codCilInser." (`cod_componente`, `descrizione`, `dimensione`, `materiale`) VALUES ('".$codComponente."','".$descrizione."','".$dimensione."','".$materiale."');";
                    if (mysqli_query($connessione, $istruzione)) {
                        echo "New record created successfully";
                        return true;
                    } else {
                        //echo "err 1 Componente già inserito nel cilindro - Vedere cod. componente";
                        return false;
                    }
                }
                else {
                    return false;
                }
                //disconnessione
                mysqli_close($connessione);
            }
            else{
                return false;
            }
        }

        //INSERISCO L' ARTICOLO NELLA TABELLA DEGLI ARTICOLI
        public function setComponenteInArticolo($codComp,$desc,$dim,$mat, $cil){
            if(isset($this->attiva)){
                //connessione al DB
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "INSERT INTO ".$cil." (`cod_componente`, `descrizione`, `dimensione`, `materiale`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
                if (mysqli_query($connessione,$istruzione)) {
                    echo "COMPONENTE INSERITO CORRETTAMENTE";
                }
                else {
                    echo "err 2 Componente già inserito nel cilindro - Vedere cod. componente <br>";
                    return false;
                }
                $istruzione = "INSERT INTO `componente` (`cod_componente`, `descrizione`, `dimensione`, `materiale`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
                if (mysqli_query($connessione,$istruzione)) {
                    //echo "COMPONENTE INSERITO CORRETTAMENTE <br>";
                }
                else {
                    echo "err 3 Componente già inserito nel cilindro - Vedere cod. componente <br>";
                    return false;
                }
                //disconnessione
                mysqli_close($connessione);
            }
            else{
                return false;
            }
        }

        //INSERISCO UN SINGOLO COMPONENTE SENZA CILINDRO
        public function setComponenteSingolo($codComp,$desc,$dim,$mat){
            if(isset($this->attiva)){
                //connessione al DB
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "INSERT INTO `componente` (`cod_componente`, `descrizione`, `dimensione`, `materiale`)VALUES ('".$codComp."','".$desc."','".$dim."','".$mat."');";
                if (mysqli_query($connessione,$istruzione)) {
                    echo "COMPONENTE INSERITO CORRETTAMENTE <br>";
                }
                else {
                    echo "err 3 Componente già inserito - Vedere cod. componente";
                    return false;
                }
            }
            else{
                return false;
            }
        }

        //INSERISCO RIGA IMPEGNO
        public function setImpegno($cod_impegno,$cliente,$data_cons,$ordine){
            if(isset($this->attiva)){
                //connessione al DB
                $dataOra = date("Y-m-d");
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "INSERT INTO `impegno` (`cod_impegno`, `cliente`, `data_consegna`, `ordine`, `stato`, `data_creazione`)
                    VALUES ('".$cod_impegno."','".$cliente."','".$data_cons."','".$ordine."','1','".$dataOra."');";
                if (mysqli_query($connessione,$istruzione)) {
                    echo "IMPEGNO INSERITO CORRETTAMENTE <br>";
                    return true;
                }
                else {
                    echo "err 1: Impegno già inserito - Vedere cod. impegno<br>";
                    //echo "Error: " . $istruzione . "<br>" . $connessione->error;
                    return false;
                }
            }
            else{
                return false;
            }
        }

        //CREO LA MEGA TABELLA
        public function setArticoloPerImpegno($artic,$impeg){
            //variabili
            $index = 0;
            $data = array();
            global $lavorazioni;
            //attivo la connessione
            if(isset($this->attiva)){
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "SELECT * FROM ".$artic;
                $query = mysqli_query($connessione, $istruzione);
                if($query) {
                    //se la query va a buon fine
                    while($row = mysqli_fetch_assoc($query)){
                        //$data[$index][0] = ID autoincrementale
                        $data[$index][1] = $impeg;                  //impegno
                        $data[$index][2] = $artic;                  //cod articolo
                        $data[$index][3] = '1';                     //qt. articolo
                        $data[$index][4] = $row['cod_componente'];  //codice del componente
                        $data[$index][5] = '1';                     //qt. componente
                        $data[$index][6] = $row['descrizione'];     //descrizione del componente
                        $data[$index][7] = $row['dimensione'];      //dimensione del componente
                        $data[$index][8] = $row['materiale'];       //materiale del componente
                        $data[$index][9] = '0';                     //avanzamento del componente
                        //incremento
                        $index++;
                    }
                    if($index==0){
                        echo "COD ARTICOLO INESISTENTE";
                        return false;
                    }
                }
                else{
                    echo "ERRORE 1";
                    return false;
                }
                //inserisco quante righe son state lette -> index to 0
                $x=$index-1;
                for($i=$x;$i>=0;--$i){
                    //mi collego alla MEGATABELLA LAVORAZIONE
                  /*  $istruzione = "INSERT INTO produzione (`cod_impegno`, `cod_articolo`, `qt_articolo`, `cod_componente`,`qt_componente`, `descrizione`, `dimensione`, `materiale`, `stato`)
                                    VALUES ('".$data[$i][1]."','".$data[$i][2]."','".$data[$i][3]."','".$data[$i][4]."','".$data[$i][5]."','".$data[$i][6]."','".$data[$i][7]."','".$data[$i][8]."','".$data[$i][9]."')";
*/
                    if($query = mysqli_query($connessione, $istruzione)){
                    }
                    else{
                        echo "<br> Error: " . $istruzione . "<br>" . $connessione->error."<br>";
                        return false;
                    }
                }
                //stampo tutto l'impegno
                $varDB = new conn_DB();
                $varDB->getImpegno($impeg);
            }
            else{
                return false;
            }
        }

        //STAMPO L'IMPEGNO
        public function getImpegno($imp){
            if(isset($this->attiva)){
                //connessione al DB
                $connessione = mysqli_connect($this->nomehost,$this->nomeuser,$this->password,$this->dbName);
                $istruzione = "SELECT * FROM 'produzione' WHERE cod_impegno = '".$imp."');";
                if($result = mysqli_query($connessione, $istruzione)){
                    if (mysqli_num_rows($result) > 0) {
                        // stampo i risultati
                        echo "<table border='1'><tr><th>CODICE IMPEGNO</th><th>COD ARTICOLO</th><th>QT. ARTICOLO</th><th>CODICE COMPONENTE</th></tr>";
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr><td>".$row["cod_impegno"]. "</td><td>" . $row["cod_articolo"]. "</td><td>" . $row["qt_articolo"]. "</td><td>" . $row["cod_componente"]. "</td></tr>";
                        }
                        echo "</table>";
                    }
                    else {
                        echo "0 results";
                    }
                }
                else{
                    echo "<br> Error: " . $istruzione . "<br>" . $connessione->error."<br>";
                    return false;
                }

            }
            else{
                return false;
            }
            return true;
        }
    }
?>
