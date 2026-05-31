<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan.']);
    exit;
}

// Invoer ophalen en opschonen
$naam      = htmlspecialchars(trim($_POST['naam']      ?? ''), ENT_QUOTES, 'UTF-8');
$email     = trim($_POST['email']     ?? '');
$telefoon  = htmlspecialchars(trim($_POST['telefoon']  ?? ''), ENT_QUOTES, 'UTF-8');
$onderwerp = htmlspecialchars(trim($_POST['onderwerp'] ?? 'Algemene vraag'), ENT_QUOTES, 'UTF-8');
$bericht   = htmlspecialchars(trim($_POST['bericht']   ?? ''), ENT_QUOTES, 'UTF-8');
$privacy   = isset($_POST['privacy']) && $_POST['privacy'] === '1';

// Validatie
if (empty($naam) || empty($email) || empty($bericht) || empty($onderwerp)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vul alle verplichte velden in.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vul een geldig e-mailadres in.']);
    exit;
}

if (!$privacy) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Accepteer de privacyverklaring om verder te gaan.']);
    exit;
}

// E-mail opbouwen
$to      = 'info@fincars.nl';
$subject = '=?UTF-8?B?' . base64_encode("Nieuw bericht via fincars.nl: $onderwerp") . '?=';

$body  = "Er is een nieuw bericht binnengekomen via fincars.nl\n";
$body .= str_repeat('-', 50) . "\n\n";
$body .= "Naam:        $naam\n";
$body .= "E-mail:      $email\n";
if (!empty($telefoon)) {
    $body .= "Telefoon:    $telefoon\n";
}
$body .= "Onderwerp:   $onderwerp\n\n";
$body .= "Bericht:\n$bericht\n\n";
$body .= str_repeat('-', 50) . "\n";
$body .= "Verstuurd via fincars.nl\n";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: FinCars <info@fincars.nl>\r\n";
$headers .= "Reply-To: $naam <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Bedankt voor uw bericht! Wij nemen zo snel mogelijk contact met u op.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Er is een fout opgetreden bij het verzenden. Bel ons direct op +31 6 10 28 06 53.'
    ]);
}
