<!DOCTYPE html>
<html>
<head>
    <title>Gebruikte Variabelen en Berekeningen</title>
    <link rel="stylesheet" type="text/css" href="../styles/pmistyles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            padding: 10px;
            box-sizing: border-box;
            max-width: 100vw;
        }
        .results-container {
            width: 90%;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            margin-bottom: 20px;
        }
        .result-item {
            margin: 8px 0;
            font-size: 16px;
            line-height: 1.5;
        }
        .result-item span {
            font-weight: bold;
            color: #333;
        }
        button, .download-button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        button:hover, .download-button:hover {
            background-color: #45a049;
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin: 10px 0;
        }
        .equation {
            font-family: 'Times New Roman', Times, serif;
            font-size: 18px;
            margin: 15px 0;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .equation span {
            display: block;
            margin-bottom: 5px;
        }
    </style>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script type="text/javascript" id="MathJax-script" async
        src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.2/es5/tex-mml-chtml.js">
    </script>
</head>
<body>
    <div class="container">
        <h1>Gebruikte Variabelen en Berekeningen</h1>
        <div class="results-container">
            <?php
                if (isset($_GET['cover'], $_GET['surfact'], $_GET['t_rectum_c'], $_GET['t_ambient_c'], $_GET['body_wt_kg'], $_GET['date'], $_GET['time'], $_GET['ondergrond'])) {
                    $cover       = $_GET['cover'];
                    $surfact     = $_GET['surfact'];
                    $t_rectum_c  = $_GET['t_rectum_c'];
                    $t_ambient_c = $_GET['t_ambient_c'];
                    $body_wt_kg  = $_GET['body_wt_kg'];
                    $date        = $_GET['date'];
                    $time        = $_GET['time'];
                    $ondergrond  = $_GET['ondergrond'];

                    echo '<div class="result-item"><span>Lichaamstemperatuur: </span>' . htmlspecialchars($t_rectum_c) . ' °C</div>';
                    echo '<div class="result-item"><span>Omgevingstemperatuur: </span>' . htmlspecialchars($t_ambient_c) . ' °C</div>';
                    echo '<div class="result-item"><span>Lichaamsgewicht: </span>' . htmlspecialchars($body_wt_kg) . ' kg</div>';
                    echo '<div class="result-item"><span>Lichaamsbedekking: </span>' . htmlspecialchars($cover) . '</div>';
                    echo '<div class="result-item"><span>Omgevingsfactoren: </span>' . htmlspecialchars($surfact) . '</div>';
                    echo '<div class="result-item"><span>Ondergrond: </span>' . htmlspecialchars($ondergrond) . '</div>';
                    echo '<div class="result-item"><span>Datum en tijd van berekenen: </span>' . htmlspecialchars($date . ' ' . $time) . '</div>';

                    $cmd = 'python3 ' . escapeshellarg(__DIR__ . '/calc.py') . ' ' 
                         . escapeshellarg($cover) . ' ' 
                         . escapeshellarg($surfact) . ' ' 
                         . escapeshellarg($t_rectum_c) . ' ' 
                         . escapeshellarg($t_ambient_c) . ' ' 
                         . escapeshellarg($body_wt_kg) . ' ' 
                         . escapeshellarg($date) . ' ' 
                         . escapeshellarg($time) . ' ' 
                         . escapeshellarg($ondergrond);
                    $output = shell_exec($cmd);

                    if (!empty($output)) {
                        $lines = explode("\n", trim($output));
                        $isError = false;
                        $B = $TR = $TO = $f = $M = $formula = null;

                        foreach ($lines as $line) {
                            if (trim($line) === '') continue;

                            if (strpos($line, 'Error:') !== false) {
                                echo '<p>' . htmlspecialchars($line) . '</p>';
                                $isError = true;
                            }

                            if (!$isError) {
                                if (strpos($line, 'B:') !== false) {
                                    $B = floatval(substr($line, 3));
                                    $B_rounded = round($B, 3);
                                } elseif (strpos($line, 'T_R:') !== false) {
                                    $TR = floatval(substr($line, 5));
                                } elseif (strpos($line, 'T_O:') !== false) {
                                    $TO = floatval(substr($line, 5));
                                } elseif (strpos($line, 'Correctiefactor:') !== false) {
                                    $f = floatval(substr($line, 17));
                                    echo '<div class="result-item"><span>Gebruikte correctiefactor: </span>' . htmlspecialchars($f) . '</div>';
                                } elseif (strpos($line, 'Lichaamsgewicht:') !== false) {
                                    $M = floatval(substr($line, 16));
                                } elseif (strpos($line, 'Formula:') !== false) {
                                    $formula = trim(substr($line, 8));
                                }
                            }
                        }

                        if (!$isError && $B !== null && $TR !== null && $TO !== null && $f !== null && $M !== null && $formula !== null) {
                            echo '<div class="equation">';
                            echo '<span><b>Berekening B:</b></span>';
                            echo '<span>\\( B = 1.2815 \cdot (f \cdot M)^{-0.625} + 0.0284 \\)</span>';
                            echo '<span>\\( B = 1.2815 \cdot (' . htmlspecialchars($f) . ' \cdot ' . htmlspecialchars($M) . ')^{-0.625} + 0.0284 \\)</span>';
                            echo '</div>';

                            echo '<div class="equation">';
                            echo '<span><b>Berekening PMI:</b></span>';
                            if ($formula == "below") {
                                echo '<span>\\( \frac{T_R - T_O}{37.2 - T_O} = 1.25\,e^{B\,t} - 0.25\,e^{5\,B\,t} \\)</span>';
                                echo '<span>\\( \frac{' . htmlspecialchars($TR) . ' - ' . htmlspecialchars($TO) . '}{37.2 - ' . htmlspecialchars($TO) . '} = 1.25\,e^{' . htmlspecialchars($B_rounded) . '\,t} - 0.25\,e^{' . htmlspecialchars(5 * $B_rounded) . '\,t} \\)</span>';
                            } else {
                                echo '<span>\\( \frac{T_R - T_O}{37.2 - T_O} = 1.11\,e^{B\,t} - 0.11\,e^{10\,B\,t} \\)</span>';
                                echo '<span>\\( \frac{' . htmlspecialchars($TR) . ' - ' . htmlspecialchars($TO) . '}{37.2 - ' . htmlspecialchars($TO) . '} = 1.11\,e^{' . htmlspecialchars($B_rounded) . '\,t} - 0.11\,e^{' . htmlspecialchars(10 * $B_rounded) . '\,t} \\)</span>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo "<div class='result-item'>Geen resultaten om weer te geven.</div>";
                    }
                } else {
                    echo "<div class='result-item'>Ontbrekende gegevens om de berekeningen te tonen.</div>";
                }
            ?>
        </div>

        <?php if (isset($output) && !empty($output) && !$isError): ?>
            <?php
                $qs = http_build_query([
                    'cover'       => $cover,
                    'surfact'     => $surfact,
                    't_rectum_c'  => $t_rectum_c,
                    't_ambient_c' => $t_ambient_c,
                    'body_wt_kg'  => $body_wt_kg,
                    'date'        => $date,
                    'time'        => $time,
                    'ondergrond'  => $ondergrond
                ]);
            ?>
            <a
                href="export.php?<?php echo htmlspecialchars($qs); ?>"
                class="download-button"
            >Download PDF</a>

            <div style="margin-top:10px;">
                <input
                    type="email"
                    name="sendTo"
                    id="exportEmail"
                    placeholder="Uw e-mailadres"
                    style="padding:8px; width:200px;"
                >
                &nbsp;
                <button
                    id="btnEmailPdf"
                    data-qs="<?php echo htmlspecialchars($qs); ?>"
                    class="download-button"
                >Opslaan &amp; E-mail PDF</button>
            </div>
        <?php endif; ?>

        <button onclick="window.history.back()">Terug</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof MathJax !== 'undefined') {
            MathJax.typesetPromise();
        }

        const btnEmail = document.getElementById('btnEmailPdf');
        if (btnEmail) {
            btnEmail.addEventListener('click', function(event) {
                event.preventDefault();
                const baseQs = btnEmail.getAttribute('data-qs') || '';
                const emailInput = document.getElementById('exportEmail').value.trim();
                if (!emailInput) {
                    alert('Vul alstublieft uw e-mailadres in om de PDF per e-mail te ontvangen.');
                    return;
                }
                const fullQs = baseQs
                    + '&action=email'
                    + '&sendTo=' + encodeURIComponent(emailInput);
                window.location.href = 'export.php?' + fullQs;
            });
        }
    });
    </script>
</body>
</html>
