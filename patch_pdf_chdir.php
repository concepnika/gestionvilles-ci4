<?php
// Injecte chdir(ci3.webroot) avant chaque require des libs PDF legacy
// dans Gestion.php — les chemins relatifs ./assets/ et ./upload/ du
// code legacy se resolvent alors contre la racine de l'app CI3.
$f = __DIR__ . '/app/Controllers/Gestion.php';
$code = file_get_contents($f);
$old = "require_once rtrim((string) env('ci3.webroot'), '/\\\\') . '/application/controllers/pdf/";
$new = "chdir(rtrim((string) env('ci3.webroot'), '/\\\\')); require_once rtrim((string) env('ci3.webroot'), '/\\\\') . '/application/controllers/pdf/";
$n = 0;
$code = str_replace($old, $new, $code, $n);
file_put_contents($f, $code);
echo "$n chdir injectes\n";
