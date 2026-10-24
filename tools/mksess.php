<?php
// cree une session CI3 factice valide (meme format que CI3)
$db = new mysqli('localhost','root','','gestionvilles');
$id = substr(strtolower(bin2hex(random_bytes(20))),0,32);
$parts = [
  'email' => 'admin@test.com',
  'nom' => 'Dallaire Serge',
  'privilege' => 1,
  'user_id' => 1,
  'login_in' => true,
  'site_lang' => 'fr',
];
$data = '';
foreach ($parts as $k => $v) { $data .= $k . '|' . serialize($v); }
$db->query("INSERT INTO ci_sessions (id,ip_address,timestamp,data) VALUES ('$id','127.0.0.1',UNIX_TIMESTAMP(),'" . $db->real_escape_string($data) . "')");
echo $id;
