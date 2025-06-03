<?php
// export.php

$originalQuery = isset($_SERVER['QUERY_STRING']) 
                ? $_SERVER['QUERY_STRING'] 
                : '';

// 0) Zet hem om naar een HTML‐veilige string zodat je 'm in href en meta kunt hergebruiken.
$escapedQuery = htmlspecialchars($originalQuery, ENT_QUOTES, 'UTF-8');

// 0) Zet foutmeldingen voor notices/deprecated uit, anders kunnen headers niet verzonden worden:
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// 1) Laad FPDF
require __DIR__ . '/../lib/fpdf.php';

// 2) Lees alle GET‐parameters
$cover       = $_GET['cover']       ?? '';
$surfact     = $_GET['surfact']     ?? '';
$t_rectum_c  = $_GET['t_rectum_c']  ?? '';
$t_ambient_c = $_GET['t_ambient_c'] ?? '';
$body_wt_kg  = $_GET['body_wt_kg']  ?? '';
$date        = $_GET['date']        ?? '';
$time        = $_GET['time']        ?? '';
$ondergrond  = $_GET['ondergrond']  ?? '';
$action      = $_GET['action']      ?? '';
$sendTo      = filter_var($_GET['sendTo'] ?? '', FILTER_VALIDATE_EMAIL);

// 2a) Korte validatie: cijfers moeten écht nummers zijn
if (!is_numeric($t_rectum_c) || !is_numeric($t_ambient_c) || !is_numeric($body_wt_kg)) {
    die("Error: Temperature and body weight must be numbers.");
}

// 3) Roep Python-script aan
$cmd = 'python3 ' . escapeshellarg(__DIR__ . '/calc.py') . ' '
     . escapeshellarg($cover)       . ' '
     . escapeshellarg($surfact)     . ' '
     . escapeshellarg($t_rectum_c)  . ' '
     . escapeshellarg($t_ambient_c) . ' '
     . escapeshellarg($body_wt_kg)  . ' '
     . escapeshellarg($date)        . ' '
     . escapeshellarg($time)        . ' '
     . escapeshellarg($ondergrond);

$output = shell_exec($cmd);
if ($output === null) {
    die("Error: could not run calc.py");
}

// 4) Parse de output van Python
$lines   = explode("\n", trim($output));
$results = [];
$values  = [];

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, 'DEBUG:')) {
        continue;
    }
    if (strpos($line, ':') !== false) {
        list($label, $value) = explode(':', $line, 2);
        $label = trim($label);
        $value = trim($value);

        if ($label === 'Formula' || $label === 'best_time1') {
            continue;
        }
        switch ($label) {
            case 'T_R':
                $outLabel      = 'Lichaamstemperatuur';
                $values['TR']  = floatval($value);
                break;
            case 'T_O':
                $outLabel      = 'Omgevingstemperatuur';
                $values['TO']  = floatval($value);
                break;
            default:
                $outLabel = $label;
        }
        $results[$outLabel] = $value;
    }
    if (strpos($line, 'B:') === 0) {
        $values['B'] = floatval(substr($line, 2));
    }
    if (strpos($line, 'Correctiefactor:') === 0) {
        $values['f'] = floatval(substr($line, 17));
    }
    if (strpos($line, 'Lichaamsgewicht:') === 0) {
        $values['M'] = floatval(substr($line, 16));
    }
}

// 5) Zorg dat f, M, TR, TO en Br altijd een default hebben
$f  = $values['f']  ?? 0;
$M  = $values['M']  ?? 0;
$Br = isset($values['B']) ? round($values['B'], 3) : 0;
$TR = $values['TR'] ?? (float)($results['Lichaamstemperatuur'] ?? 0);
$TO = $values['TO'] ?? (float)($results['Omgevingstemperatuur'] ?? 0);

// Formuletekst bouwen
$B_line1 = "B = 1.2815 * (f*M)^(-0.625) + 0.0284";
$B_line2 = "B = 1.2815 * ({$f}*{$M})^(-0.625) + 0.0284";

if ($TR <= 23) {
    $P_line1 = "(Lichaamstemp - Omgevingstemp)/(37.2 - Omgevingstemp) = 1.25 * e^(B*t) - 0.25 * e^(5*B*t)";
    $P_line2 = "({$TR} - {$TO})/(37.2 - {$TO}) = 1.25 * e^({$Br}*t) - 0.25 * e^(" . (5*$Br) . "*t)";
} else {
    $P_line1 = "(Lichaamstemp - Omgevingstemp)/(37.2 - Omgevingstemp) = 1.11 * e^(B*t) - 0.11 * e^(10*B*t)";
    $P_line2 = "({$TR} - {$TO})/(37.2 - {$TO}) = 1.11 * e^({$Br}*t) - 0.11 * e^(" . (10*$Br) . "*t)";
}

// 6) Genereer PDF met FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// Titel
$pdf->SetFont('Helvetica','B',16);
$pdf->Cell(0, 10, 'PMI Report', 0, 1, 'C');
$pdf->Ln(4);

// Print labels en waarden
$pdf->SetFont('Helvetica','',12);
foreach ($results as $lbl => $val) {
    $pdf->SetFont('Helvetica','B',12);
    $pdf->Cell(60, 8, iconv('UTF-8','ISO-8859-1//TRANSLIT', $lbl . ':'), 0, 0);
    $pdf->SetFont('Helvetica','',12);
    $pdf->MultiCell(0, 8, iconv('UTF-8','ISO-8859-1//TRANSLIT', $val), 0, 1);
}
$pdf->Ln(4);

// “Berekening B”
$pdf->SetFont('Helvetica','B',14);
$pdf->Cell(0, 8, 'Berekening B:', 0, 1);
$pdf->SetDrawColor(200,200,200);
$pdf->Rect(15, $pdf->GetY(), 180, 18);
$pdf->Ln(2);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(0, 6, $B_line1, 0, 1);
$pdf->Cell(0, 6, $B_line2, 0, 1);
$pdf->Ln(6);

// “Berekening PMI”
$pdf->SetFont('Helvetica','B',14);
$pdf->Cell(0, 8, 'Berekening PMI:', 0, 1);
$pdf->Rect(15, $pdf->GetY(), 180, 18);
$pdf->Ln(2);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(0, 6, $P_line1, 0, 1);
$pdf->Cell(0, 6, $P_line2, 0, 1);

// Sla PDF op in geheugen
$pdfData = $pdf->Output('', 'S');

// 7) Als het geen email‐verzoek is, direct downloaden
if ($action !== 'email' || !$sendTo) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="PMI-report.pdf"');
    header('Content-Length: ' . strlen($pdfData));
    echo $pdfData;
    exit;
}

// 8) PHPMailer‐code om e-mail te versturen
require __DIR__ . '/../lib/PHPMailer/Exception.php';
require __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// SMTP-instellingen (Gmail)
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'je Gmail adres hier';
$mail->Password   = 'je 16-cijferige Gmail app-wachtwoord hier';
// Let op: gebruik een app-specifiek wachtwoord voor Gmail, niet je normale wachtwoord!
$mail->SMTPSecure = 'tls';
$mail->Port       = 587;

// Afzender en geadresseerde
$mail->setFrom('no-reply@yourdomain.com', 'PMI Calculator');
$mail->addAddress($sendTo);

// E-mailtekst
$mail->isHTML(false);
$mail->Subject = 'Uw PMI-berekening';
$mail->Body    = "Beste gebruiker,\n\nIn de bijlage vindt u uw PMI-berekeningsrapport.\n\nMet vriendelijke groet,\nPMI Calculator";

try {
    $mail->addStringAttachment($pdfData, 'PMI-report.pdf');
    $mail->send();

    // ── Succes‐pagina met dynamische redirect ──
    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
      <meta charset="utf-8">
      <title>Rapport verzonden</title>

      <!-- 5 seconden wachten, daarna terug naar calculations.php met dezelfde query -->
      <meta http-equiv="refresh" content="5;url=calculations.php?<?php echo $escapedQuery; ?>">

      <!-- Externe CSS -->
      <link rel="stylesheet" href="../styles/pmistyles.css">
    </head>
    <body>
      <div class="message-box">
        <h1 class="success">E-mail succesvol verstuurd!</h1>
        <p>Het PMI-rapport is verzonden naar <strong><?php echo htmlspecialchars($sendTo, ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
        <p>Je wordt automatisch teruggestuurd naar de berekeningenpagina in 5 seconden.</p>
        <p>
          <a class="button" href="calculations.php?<?php echo $escapedQuery; ?>">
            Terug naar berekeningen
          </a>
        </p>
      </div>
    </body>
    </html>
    <?php
    exit;

} catch (Exception $e) {
    // ── Foutpagina met dynamische redirect ──
    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
      <meta charset="utf-8">
      <title>Fout bij e-mail verzenden</title>

      <!-- 5 seconden wachten, daarna terug naar calculations.php met dezelfde query -->
      <meta http-equiv="refresh" content="5;url=calculations.php?<?php echo $escapedQuery; ?>">

      <!-- Externe CSS -->
      <link rel="stylesheet" href="../styles/pmistyles.css">
    </head>
    <body>
      <div class="message-box">
        <h1 class="error">Fout bij verzenden!</h1>
        <p><strong>Details:</strong> <?php echo htmlspecialchars($mail->ErrorInfo, ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Je kunt het PDF-bestand ook handmatig downloaden:</p>
        <p>
          <a
            href="data:application/pdf;base64,<?php echo base64_encode($pdfData); ?>"
            download="PMI-report.pdf"
            class="button"
          >
            Download PDF
          </a>
        </p>
        <p>Je wordt automatisch teruggestuurd naar de berekeningenpagina in 5 seconden.</p>
        <p>
          <a class="button" href="calculations.php?<?php echo $escapedQuery; ?>">
            Terug naar berekeningen
          </a>
        </p>
      </div>
    </body>
    </html>
    <?php
    exit;
}
?>