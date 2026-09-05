<?php
// index.php (language chooser page)
// Put this at /language/index.php or /language/index.html (rename to .php if you need the PHP logic).
// It links to language.php to perform the change, with return= pointing back to a requested page.
//
// Minimal usage: visit /language/ to see the links.

require_once __DIR__ . '/language.php'; // optional: to read SITE_LANG and $SUPPORTED_LANGS if desired

// Note: language.php will run its detection logic when included. For a standalone chooser UI you can
// omit the include and just link to language.php below.

$supported = [
    'en' => 'English',
    'lt' => 'Lietuvių',
    'pl' => 'Polska'
];

$current = defined('SITE_LANG') ? SITE_LANG : ($_COOKIE['site_lang'] ?? 'en');

// Build a safe return target (prefer HTTP_REFERER)
$return = '/';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $parsed = parse_url($_SERVER['HTTP_REFERER']);
    if (isset($parsed['host']) && $parsed['host'] === $_SERVER['HTTP_HOST']) {
        // use only path+query to avoid open-redirect
        $return = ($parsed['path'] ?? '/') . (!empty($parsed['query']) ? '?' . $parsed['query'] : '');
    }
}

// Simple HTML chooser
$lengui = array(
'title' => array ('en' => 'Choose language', 'lt' => 'Pasirinkti kalbą', 'pl' => 'Wybierz język'),
'header' => array ('en' => 'Choose your needed language', 'lt' => 'Pasirinkti pageidaujamą kalbą', 'pl' => 'Wybierz potrzebny język'),
'lang_atm' => array ('en' => 'Current: ', 'lt' => 'Šiuo metu: ', 'pl' => 'W tej chwili: ')
);
?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($current); ?>">
<head>
  <meta charset="utf-8">
  <title><?php echo $lengui['title'][$current]; ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial; padding: 2rem; }
    .langs { display:flex; gap: 1rem; flex-wrap:wrap; }
    .lang { padding: .5rem 1rem; border:1px solid #ddd; border-radius:6px; text-decoration:none; color:#111; }
    .lang.active { background:#0b78e3;color:#fff;border-color:#0b78e3; }
  </style>
</head>
<body>
  <h1><?php echo $lengui['header'][$current]; ?></h1>
  <p><?php echo $lengui['lang_atm'][$current]; ?><strong><?php echo htmlspecialchars($current); ?></strong></p>
  <div class="langs">
    <?php foreach ($supported as $code => $label): 
      $url = '/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
      $cls = ($code === $current) ? 'lang active' : 'lang';
    ?>
      <a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
    <?php endforeach; ?>
  </div>
</body>
</html>
