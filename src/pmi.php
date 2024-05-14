<!DOCTYPE html>
<html>
<head>
    <title>PMI Calculator</title>
    <link rel="stylesheet" type="text/css" href="pmistyles.css">
    <script>
        // When the user clicks on <span> (x), close the modal
        function closeModal() {
            document.getElementById('resultsModal').style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            Lichaamstemperatuur: <input type="number" name="number1" value="<?php echo isset($_POST['number1']) ? $_POST['number1'] : ''; ?>"><br>
            Omgevingstemperatuur: <input type="number" name="number2" value="<?php echo isset($_POST['number2']) ? $_POST['number2'] : ''; ?>"><br>
            Lichaamsgewicht: <input type="number" name="number3" value="<?php echo isset($_POST['number3']) ? $_POST['number3'] : ''; ?>"><br>
            Lichaamsbedekking: 
            <select name="dropdown1">
                <option value="Naakt" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Naakt') ? 'selected' : ''; ?>>Naakt</option>
                <option value="Een of twee dunne lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Een of twee dunne lagen') ? 'selected' : ''; ?>>Een of twee dunne lagen</option>
                <option value="Een of twee dikke lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Een of twee dikke lagen') ? 'selected' : ''; ?>>Een of twee dikke lagen</option>
                <option value="Twee of drie lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Twee of drie lagen') ? 'selected' : ''; ?>>Twee of drie lagen</option>
                <option value="Drie of vier lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Drie of vier lagen') ? 'selected' : ''; ?>>Drie of vier lagen</option>
                <option value="Meer lagen" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Meer lagen') ? 'selected' : ''; ?>>Meer Lagen</option>
                <option value="Licht beddengoed" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Licht beddengoed') ? 'selected' : ''; ?>>Licht beddengoed</option>
                <option value="Zwaar beddengoed" <?php echo (isset($_POST['dropdown1']) && $_POST['dropdown1'] == 'Zwaar beddengoed') ? 'selected' : ''; ?>>Zwaar beddengoed</option>
            </select><br>
            Omgevingsfactoren: 
            <select name="dropdown2">
                <option value="Droog lichaam binnen" <?php echo (isset($_POST['dropdown2']) && $_POST['dropdown2'] == 'Droog lichaam binnen') ? 'selected' : ''; ?>>Droog lichaam, binnen</option>
                <!-- Additional options similarly... -->
            </select><br>
            Date: <input type="date" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>"><br>
            Time: <input type="time" name="time" value="<?php echo isset($_POST['time']) ? $_POST['time'] : ''; ?>"><br>
            <input type="submit" value="Submit">
        </form>

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
                        $date = $_POST['date'];
                        $time = $_POST['time'];

                        if (empty($number1) || empty($number2) || empty($number3) || empty($dropdown1) || empty($dropdown2) || empty($date) || empty($time)){
                            echo "Vul lichaamstemperatuur, omgevingstemperatuur, lichaamsgewicht, lichaamsbedekking, omgevingsfactoren, datum en tijd in.";
                        } else {
                            $command = escapeshellcmd("python calc.py " . 
                                        escapeshellarg($dropdown1) . " " . 
                                        escapeshellarg($dropdown2) . " " . 
                                        escapeshellarg($number1) . " " . 
                                        escapeshellarg($number2) . " " . 
                                        escapeshellarg($number3) . " " . 
                                        escapeshellarg($date) . " " . 
                                        escapeshellarg($time));
                            $output = shell_exec($command);
                            if (!empty($output)) {
                                // Displaying output in modal if not empty
                                echo "<script>document.getElementById('resultsModal').style.display = 'block';</script>";
                    
                                // Processing each line of output
                                $outputLines = explode("\n", $output);
                                echo "<h2>PMI Resultaat</h2>";  // Title for the results
                                foreach ($outputLines as $line) {
                                    if (trim($line)) {  // Check if the line is not empty
                                        // Correcting duplicate labels in the output
                                        $line = str_replace("PMI: PMI:", "PMI:", $line);
                                        // Output each line inside a paragraph
                                        echo "<p>" . htmlspecialchars($line) . "</p>";
                                    }
                                }
                            } else {
                                echo "<p>Geen resultaten om weer te geven.</p>";
                            }
                        }
                    }
                    ?>
                </div>
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
