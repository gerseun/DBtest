<html>
  <head>
    <title>
    CREAZIONE CILINDRO
    </title>

    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/x-icon" href="https://static.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06c77743096d55480f33.ico">
    <link rel="mask-icon" type="" href="https://static.codepen.io/assets/favicon/logo-pin-8f3771b1072e3c38bd662872f6b673a722f4b3ca2421637d5596661b4e2132cc.svg" color="#111">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <!--CONNESSIONE AL FOGLIO CSS DI STILE-->
    <link href="grafica.css" rel="stylesheet" type="text/css">

    <script>
      window.console = window.console || function(t) {};
    </script>
    <script>
      if (document.location.search.match(/type=embed/gi)) {
        window.parent.postMessage("resize", "*");
      }
    </script>
  </head>

  <body translate="no">
    <?php
      // inclusione del file di classe -> CONNESSIONE DB
      include "config.PHP";
      // istanza della classe
      $varDB = new conn_DB();
    ?>
    <img src="logo.png">
    <br><br>
    <!--
    RITORNO ALLA HOME PAGE
    -->
    <button name="HOME" onclick="window.close()"><H4>HOME PAGE</H4></button>
    <!--
    FUNZIONI GRAFICHE
    -->
    <div class="container">
    <h1>INSERIMENTO CILINDRI O ASSIEMI</h1>
    <div id="table" class="table-editable">
    <span class="table-add glyphicon glyphicon-plus"></span>
    <table class="table"><tbody>
      <tr>
        <th>COD. ARTICOLO</th>
        <th>DESCRIZIONE</th>
        <th>CLIENTE</th>
        <th>COD. CLIENTE</th>
        <th></th>
        <th></th>
      </tr>
      <tr>
        <td contenteditable="true">-</td>
        <td contenteditable="true">-</td>
        <td contenteditable="true">-</td>
        <td contenteditable="true">-</td>
        <td>
          <span class="table-remove glyphicon glyphicon-remove"></span>
        </td>
        <td>
          <span class="table-up glyphicon glyphicon-arrow-up"></span>
          <span class="table-down glyphicon glyphicon-arrow-down"></span>
        </td>
      </tr>
      <tr class="hide">
        <td contenteditable="true">-</td>
        <td contenteditable="true">-</td>
        <td contenteditable="true">-</td>
        <td contenteditable="true">-</td>
        <td>
          <span class="table-remove glyphicon glyphicon-remove"></span>
        </td>
        <td>
          <span class="table-up glyphicon glyphicon-arrow-up"></span>
          <span class="table-down glyphicon glyphicon-arrow-down"></span>
        </td>
      </tr>
    </tbody></table>
  </div>
    <button id="export-btn" class="btn btn-primary">Export Data</button>
    <p id="export"></p>
  </div>
  <script src="https://static.codepen.io/assets/common/stopExecutionOnTimeout-de7e2ef6bfefd24b79a3f68b414b87b8db5b08439cac3f1012092b2290c719cd.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.6.0/underscore.js"></script>
  <script src="js.js"></script>
  <script id="rendered-js">
    var $TABLE = $('#table');
    var $BTN = $('#export-btn');
    var $EXPORT = $('#export');

    $BTN.click(function () {
      var $rows = $TABLE.find('tr:not(:hidden)');
      var headers = [];
      var data = [];

      // Get the headers (add special header logic here)
      $($rows.shift()).find('th:not(:empty)').each(function () {
        headers.push($(this).text().toLowerCase());
      });

      // Turn all existing rows into a loopable array
      $rows.each(function () {
        var $td = $(this).find('td');
        var h = {};

        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
          //selezioni i dati contenuti nella tabella
          document.write($td.eq(i).text());
          h[header] = $td.eq(i).text();
        });

        data.push(h);
      });
    });
  </script>
  <br><br>
  <?php
    /*
    echo "<h2>Dati inseriti:</h2>";
    echo $codArticolo;
    echo "<br>";
    echo $descrizione;
    echo "<br>";
    echo $cliente;
    echo "<br>";
    echo $codCliente;
    echo "<br>";
    echo "<br>";
    //apro la connessione al Database (solo se codArticolo<>"")
    if($codArticolo<>"" and $cliente<>""){
    //funzione di inserimento
    $varDB->setArticolo($codArticolo,$descrizione,$cliente,$codCliente);
    }
    */
  ?>
  <script>

  </script>
  </body>
</html>
