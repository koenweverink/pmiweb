<!DOCTYPE html>
<html>
<head>
  <title>PMI Calculator</title>
  <link rel="stylesheet" type="text/css" href="../styles/pmistyles.css">
  <style>
    /* A relatively positioned container to keep the form centered */
    .container {
      position: relative;
      margin: 0 auto;
      width: 600px;  /* Adjust width as needed for your form */
    }
    /* Centered heading */
    h1 {
      text-align: center;
    }
    /* Original form styling remains unchanged */
    .form-container {
      /* (Your original styling of the form can go here) */
    }
    /* The warning block is absolutely positioned relative to the container.
       Its width and right offset can be adjusted as needed. */
    .warning-block {
      position: absolute;
      top: 0;        /* Position as desired (for example, align with the top of the form) */
      right: -320px; /* Place it to the right of the container; adjust based on its width */
      width: 300px;  /* Fixed width for the warning text */
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      padding: 10px;
    }
  </style>
  <script>
    function closeModal() {
      document.getElementById('resultsModal').style.display = "none";
    }
    window.onclick = function(event) {
      var modal = document.getElementById('resultsModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</head>
<body>
  <div class="container">
    <h1>PMI Calculator</h1>

    <!-- Original form container (centered) -->
    <div class="form-container">
      <form action="" method="post">
        Lichaamstemperatuur:
        <input type="number" name="number1" value="<?php echo isset($_POST['number1']) ? $_POST['number1'] : ''; ?>"><br>
        Omgevingstemperatuur:
        <input type="number" name="number2" value="<?php echo isset($_POST['number2']) ? $_POST['number2'] : ''; ?>"><br>
        Lichaamsgewicht:
        <input type="number" name="number3" value="<?php echo isset($_POST['number3']) ? $_POST['number3'] : ''; ?>"><br>
        Lichaamsbedekking:
        <select name="dropdown1">
          <option value="Naakt" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Naakt') ? 'selected' : ''; ?>>Naakt</option>
          <option value="1-2 dunne lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == '1-2 dunne lagen') ? 'selected' : ''; ?>>1-2 dunne lagen</option>
          <option value="1-2 dikkere lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == '1-2 dikkere lagen') ? 'selected' : ''; ?>>1-2 dikkere lagen</option>
          <option value="2-3 dunne lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == '2-3 dunne lagen') ? 'selected' : ''; ?>>2-3 dunne lagen</option>
          <option value="3-4 dunne lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == '3-4 dunne lagen') ? 'selected' : ''; ?>>3-4 dunne lagen</option>
          <option value="Meerdere dunne/dikkere lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Meerdere dunne/dikkere lagen') ? 'selected' : ''; ?>>Meerdere dunne/dikkere lagen</option>
          <option value="Dik beddengoed" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Dik beddengoed') ? 'selected' : ''; ?>>Dik beddengoed</option>
          <option value="Dik beddengoed plus kleding" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Dik beddengoed plus kleding') ? 'selected' : ''; ?>>Dik beddengoed plus kleding</option>
          <option value="Zeer veel dikke lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Zeer veel dikke lagen') ? 'selected' : ''; ?>>Zeer veel dikke lagen</option>
        </select><br>
        Omgevingsfactoren:
        <select name="dropdown2">
          <option value="Droge kleding en/of bedekking, stilstaande lucht" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Droge kleding en/of bedekking, stilstaande lucht') ? 'selected' : ''; ?>>Droge kleding en/of bedekking, stilstaande lucht</option>
          <option value="Droge kleding en/of bedekking, bewegende lucht" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Droge kleding en/of bedekking, bewegende lucht') ? 'selected' : ''; ?>>Droge kleding en/of bedekking, bewegende lucht</option>
          <option value="Natte kleding en/of bedekking, nat lichaamsoppervlak, stilstaande lucht" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Natte kleding en/of bedekking, nat lichaamsoppervlak, stilstaande lucht') ? 'selected' : ''; ?>>Natte kleding en/of bedekking, nat lichaamsoppervlak, stilstaande lucht</option>
          <option value="Natte kleding en/of bedekking, nat lichaamsoppervlak, bewegende lucht" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Natte kleding en/of bedekking, nat lichaamsoppervlak, bewegende lucht') ? 'selected' : ''; ?>>Natte kleding en/of bedekking, nat lichaamsoppervlak, bewegende lucht</option>
          <option value="Stilstaand water" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Stilstaand water') ? 'selected' : ''; ?>>Stilstaand water</option>
          <option value="Stromend water" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Stromend water') ? 'selected' : ''; ?>>Stromend water</option>
        </select><br>
        Ondergrond:
        <select name="ondergrond">
          <option value="Willekeurig: Vloer binnenshuis, grasveld, droge aarde, asfalt" <?php echo (isset($_POST['ondergrond']) && $_POST['ondergrond'] == 'Willekeurig: Vloer binnenshuis, grasveld, droge aarde, asfalt') ? 'selected' : ''; ?>>Willekeurig: Vloer binnenshuis, grasveld, droge aarde, asfalt</option>
          <option value="Zware vulling" <?php echo (isset($_POST['ondergrond']) && $_POST['ondergrond'] == 'Zware vulling') ? 'selected' : ''; ?>>Zware vulling</option>
          <option value="Matras, dik tapijt of vloerkleed" <?php echo (isset($_POST['ondergrond']) && $_POST['ondergrond'] == 'Matras, dik tapijt of vloerkleed') ? 'selected' : ''; ?>>Matras, dik tapijt of vloerkleed</option>
          <option value="Beton, steen, tegelvloer" <?php echo (isset($_POST['ondergrond']) && $_POST['ondergrond'] == 'Beton, steen, tegelvloer') ? 'selected' : ''; ?>>Beton, steen, tegelvloer</option>
        </select><br>
        Datum:
        <input type="date" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>"><br>
        Tijd:
        <input type="time" name="time" value="<?php echo isset($_POST['time']) ? $_POST['time'] : ''; ?>"><br>
        <input type="submit" value="Submit">
      </form>
    </div>

    <!-- Warning text block outside the form container, positioned to the right -->
    <div class="warning-block">
      <h3>Het model kan niet worden toegepast bij de volgende omstandigheden:</h3>
      <ul>
        <li>Vindplaats stoffelijk overschot is niet de plaats van overlijden</li>
        <li>Er is een sterke warmtebron aanwezig nabij het lichaam (verwarming, direct zonlicht)</li>
        <li>Ongewone afkoelingsomstandigheden (bijv. bluswater).</li>
        <li>Het lichaam ligt op een vloer die wordt verwarmd door middel van vloerverwarming</li>
        <li>Verdenking op hypothermie of (maligne) hyperthermie bij overlijden</li>
        <li>De Henssge correctiefactor voor het lichaamsgewicht kan niet worden ingeschat</li>
        <li>Sterke verandering van de omgevingstemperatuur (bijvoorbeeld ramen open gezet, extreme weersveranderingen in korte tijd)</li>
      </ul>
    </div>
  </div>

  <!-- Modal for displaying results -->
  <div id="resultsModal" class="modal">
      <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <div class="modal-results">
              <?php
              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  $number1 = $_POST['number1'];
                  $number2 = $_POST['number2'];
                  $number3 = $_POST['number3'];
                  $dropdown1 = $_POST['dropdown1'];
                  $dropdown2 = $_POST['dropdown2'];
                  $ondergrond = $_POST['ondergrond'];
                  $date = $_POST['date'];
                  $time = $_POST['time'];

                  if (empty($number1) || empty($number2) || empty($number3) 
                      || empty($dropdown1) || empty($dropdown2) 
                      || empty($date) || empty($time) || empty($ondergrond)) {
                      echo "Vul alle velden in.";
                  } else {
                      $command = escapeshellcmd("python calc.py " . 
                                  escapeshellarg($dropdown1) . " " . 
                                  escapeshellarg($dropdown2) . " " . 
                                  escapeshellarg($number1) . " " . 
                                  escapeshellarg($number2) . " " . 
                                  escapeshellarg($number3) . " " . 
                                  escapeshellarg($date) . " " . 
                                  escapeshellarg($time) . " " . 
                                  escapeshellarg($ondergrond));
                      $output = shell_exec($command);
                      if (!empty($output)) {
                          echo "<script>document.getElementById('resultsModal').style.display = 'block';</script>";
                          echo "<div class='modal-results'>" . htmlspecialchars($output) . "</div>";
                      } else {
                          echo "<p>Geen resultaten om weer te geven.</p>";
                      }
                  }
              }
              ?>
          </div>
      </div>
  </div>

  <?php
  if (isset($output) && !empty($output)) {
      echo "<script>document.getElementById('resultsModal').style.display = 'block';</script>";
  }
  ?>
</body>
</html>
