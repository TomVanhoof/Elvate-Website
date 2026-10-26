<?php
declare(strict_types=1);

/**
 * elvate.be form handler: audit request + contact message.
 * Receives the POST from the Astro forms and mails it to the address below.
 *
 * With JS (fetch + "Accept: application/json"): replies with JSON.
 * Without JS (plain form POST): redirects (303) to the thanks page.
 *
 * Settings: change only the constants in this block.
 */
const MAIL_TO      = 'info@elvate.be';
const MAIL_FROM    = 'no-reply@elvate.be';   // must be an address on elvate.be (SPF/DKIM)
const MIN_SECONDS  = 3;                      // faster than this = bot
const THANKS_PATHS = ['nl' => '/bedankt/', 'en' => '/en/thanks/'];

mb_internal_encoding('UTF-8');

$wantsJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

function respond(bool $ok, int $status, bool $wantsJson, string $lang = 'nl'): never
{
    if ($wantsJson) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode(['ok' => $ok]);
        exit;
    }
    if ($ok) {
        header('Location: ' . (THANKS_PATHS[$lang] ?? THANKS_PATHS['nl']), true, 303);
        exit;
    }
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    $mail = htmlspecialchars(MAIL_TO, ENT_QUOTES);
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>elvate</title><body style="font-family:system-ui,sans-serif;background:#0C0F11;color:#fff;padding:48px 24px">'
       . '<p>Er ging iets mis / Something went wrong.</p>'
       . '<p><a style="color:#8FD3FF" href="mailto:' . $mail . '">' . $mail . '</a></p>';
    exit;
}

/** Single-line, trimmed, length-capped text (also blocks header injection). */
function clean(mixed $value, int $max): string
{
    $value = is_string($value) ? $value : '';
    $value = preg_replace('/[\r\n\t]+/', ' ', $value) ?? '';
    return mb_substr(trim($value), 0, $max);
}

/** Multi-line text for the mail body. */
function cleanBody(mixed $value, int $max): string
{
    $value = is_string($value) ? $value : '';
    $value = str_replace("\0", '', $value);
    return mb_substr(trim($value), 0, $max);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(false, 405, $wantsJson);
}

// Same-origin only.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    $originHost = parse_url($origin, PHP_URL_HOST);
    $ownHost = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');
    if (!is_string($originHost) || strcasecmp($originHost, (string) $ownHost) !== 0) {
        respond(false, 403, $wantsJson);
    }
}

$lang = ($_POST['lang'] ?? 'nl') === 'en' ? 'en' : 'nl';

// Spam traps. Bots get a silent "success" so they learn nothing.
if (clean($_POST['company'] ?? '', 200) !== '') {
    respond(true, 200, $wantsJson, $lang);
}
$ts = $_POST['ts'] ?? '';
if (is_string($ts) && ctype_digit($ts) && $ts !== '') {
    $elapsed = (microtime(true) * 1000 - (float) $ts) / 1000;
    if ($elapsed < MIN_SECONDS) {
        respond(true, 200, $wantsJson, $lang);
    }
}

$type = ($_POST['form'] ?? '') === 'contact' ? 'contact' : 'audit';
$email = clean($_POST['email'] ?? '', 254);
$url = clean($_POST['url'] ?? '', 500);
$name = clean($_POST['name'] ?? '', 120);
$message = cleanBody($_POST['message'] ?? '', 5000);
$privacy = ($_POST['privacy'] ?? '') !== '';

if (!$privacy || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    respond(false, 422, $wantsJson, $lang);
}

if ($url !== '') {
    if (!preg_match('~^[a-z][a-z0-9+.-]*://~i', $url)) {
        $url = 'https://' . $url;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    $host = (string) parse_url($url, PHP_URL_HOST);
    if (!in_array($scheme, ['http', 'https'], true) || !str_contains($host, '.')) {
        respond(false, 422, $wantsJson, $lang);
    }
}

if ($type === 'audit') {
    if ($url === '') {
        respond(false, 422, $wantsJson, $lang);
    }
    $subject = 'Audit-aanvraag: ' . $url;
    $body = "Nieuwe audit-aanvraag\n\nWebsite: $url\nE-mail: $email\nTaal: $lang\n";
} else {
    if ($name === '' || $message === '') {
        respond(false, 422, $wantsJson, $lang);
    }
    $subject = 'Contactformulier: ' . $name;
    $body = "Nieuw bericht via elvate.be\n\nNaam: $name\nE-mail: $email\n"
          . ($url !== '' ? "Website: $url\n" : '')
          . "Taal: $lang\n\n$message\n";
}

$headers = [
    'From' => 'elvate.be <' . MAIL_FROM . '>',
    'Reply-To' => $email,
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/plain; charset=UTF-8',
    'Content-Transfer-Encoding' => '8bit',
    'X-Mailer' => 'elvate.be contact.php',
];

$sent = mail(
    MAIL_TO,
    mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n"),
    str_replace("\n", "\r\n", $body),
    $headers,
    '-f' . MAIL_FROM
);

respond($sent, $sent ? 200 : 500, $wantsJson, $lang);
