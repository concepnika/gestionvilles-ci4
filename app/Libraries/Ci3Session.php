<?php

namespace App\Libraries;

use Config\Database;

/**
 * Pont vers les sessions CodeIgniter 3.
 *
 * L'application CI3 est configuree avec sess_driver = 'database' :
 * la session est une ligne dans gestionvilles.ci_sessions et le
 * cookie 'ci_session' ne contient que l'id de session. Cette classe
 * relit cette ligne et decode les donnees (format
 * session.serialize_handler = php) pour identifier l'usager connecte
 * dans l'application CI3 — aucun re-login necessaire tant que les
 * deux apps partagent le meme hote.
 */
class Ci3Session
{
    private const COOKIE_NAME = 'ci_session';
    private const TABLE = 'ci_sessions';
    private const MAX_AGE = 7200; // sess_expiration de la config CI3

    private static ?array $data = null;
    private static bool $loaded = false;
    private static string $sessionId = '';
    private static string $ipAddress = '';

    /**
     * Donnees de session CI3 decodees (email, nom, privilege, ...) ou
     * null si le cookie est absent / session expiree.
     */
    public function user(): ?array
    {
        $this->load();
        return self::$data;
    }

    /**
     * Lit une cle de la session CI3 (equivalent userdata()/magic getter).
     */
    public function get(string $key, $default = null)
    {
        $this->load();
        return self::$data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $this->load();
        return self::$data !== null && array_key_exists($key, self::$data);
    }

    public function isLoggedIn(): bool
    {
        $this->load();
        return self::$data !== null
            && ((self::$data['email'] ?? '') !== '' || (self::$data['login_in'] ?? false) === true);
    }

    /**
     * Cree une session CI3 : insere la ligne ci_sessions et pose le
     * cookie 'ci_session' sur l'hote courant. Necessaire quand l'app
     * CI4 vit sur son propre vhost (le cookie pose par CI3 n'y est
     * jamais envoye) et qu'elle authentifie l'usager elle-meme.
     */
    public function create(array $data): void
    {
        self::$loaded = true;
        // Format d'id CI3 : 32 caracteres [0-9a-v] (sid_bits=5) —
        // un id 40-hex serait rejete par CI3 lors des appels proxies.
        $alphabet = '0123456789abcdefghijklmnopqrstuv';
        self::$sessionId = '';
        for ($i = 0; $i < 32; $i++) {
            self::$sessionId .= $alphabet[random_int(0, 31)];
        }
        // IP vue par CI3 via le proxy interne (sess_match_ip = true) :
        // toutes les requetes proxies partent de 127.0.0.1.
        self::$ipAddress = '127.0.0.1';
        self::$data = $data;
        // CI3 stocke toujours session_id dans les userdata
        // (co_gestion/login : set_userdata('session_id', session_id())).
        self::$data['session_id'] ??= self::$sessionId;

        Database::connect()->table(self::TABLE)->insert([
            'id' => self::$sessionId,
            'ip_address' => self::$ipAddress,
            'timestamp' => time(),
            'data' => self::encode($data),
        ]);

        setcookie(self::COOKIE_NAME, self::$sessionId, [
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Ecrit une cle dans la session CI3 (equivalent set_userdata).
     * Persiste dans la colonne data de ci_sessions.
     */
    public function set(string $key, $value): void
    {
        $this->load();
        if (self::$data === null || self::$sessionId === '') {
            return; // pas de session CI3 active : rien a ecrire
        }
        self::$data[$key] = $value;
        $this->save();
    }

    /**
     * Retire une ou plusieurs cles (equivalent unset_userdata).
     */
    public function remove($keys): void
    {
        $this->load();
        if (self::$data === null || self::$sessionId === '') {
            return;
        }
        foreach ((array) $keys as $k) {
            unset(self::$data[$k]);
        }
        $this->save();
    }

    /**
     * Detruit la session CI3 (equivalent sess_destroy) : vide les
     * donnees et supprime la ligne pour forcer une nouvelle session.
     */
    public function destroy(): void
    {
        $this->load();
        if (self::$sessionId === '') {
            return;
        }
        Database::connect()->table(self::TABLE)
            ->where('id', self::$sessionId)
            ->delete();
        self::$data = null;
        self::$sessionId = '';
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
    }

    private function load(): void
    {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;

        $sessionId = $_COOKIE[self::COOKIE_NAME] ?? '';
        if ($sessionId === '' || ! preg_match('/^[0-9a-zA-Z,\-_]+$/', $sessionId)) {
            return;
        }
        self::$sessionId = $sessionId;
        self::$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

        $row = Database::connect()
            ->table(self::TABLE)
            ->select('data')
            ->where('id', $sessionId)
            ->where('timestamp >', time() - self::MAX_AGE)
            ->get()
            ->getRow();

        if (! $row) {
            return;
        }

        self::$data = $this->decode((string) $row->data);
    }

    /**
     * Re-ecrit la ligne ci_sessions (format php : nom|serialize(valeur);)
     * et rafraichit le timestamp comme le fait CI3 a chaque hit.
     */
    private function save(): void
    {
        Database::connect()->table(self::TABLE)
            ->where('id', self::$sessionId)
            ->update([
                'data' => self::encode(self::$data),
                'timestamp' => time(),
            ]);
    }

    /**
     * Encode au format de serialisation de session PHP (handler 'php') :
     * nom|valeur_serialisee;nom2|valeur2;...
     */
    private static function encode(array $data): string
    {
        $raw = '';
        foreach ($data as $k => $v) {
            $raw .= $k . '|' . serialize($v);
        }
        return $raw;
    }

    /**
     * Decode le format de serialisation de session PHP (handler 'php') :
     * nom|valeur_serialisee;nom2|valeur2;...
     */
    private function decode(string $raw): array
    {
        $out = [];
        $offset = 0;
        $len = strlen($raw);

        while ($offset < $len) {
            $pos = strpos($raw, '|', $offset);
            if ($pos === false) {
                break;
            }
            $name = substr($raw, $offset, $pos - $offset);
            $offset = $pos + 1;
            // PHP 8 : unserialize() rejette les octets en trop — il
            // faut la longueur exacte de la valeur serialisee.
            $vlen = self::valueLength($raw, $offset);
            if ($vlen === false) {
                break; // donnee corrompue : on garde ce qui a ete decode
            }
            $out[$name] = unserialize(substr($raw, $offset, $vlen));
            $offset += $vlen;
        }

        return $out;
    }

    /**
     * Longueur de la valeur serialisee a $offset (format PHP :
     * b:0; i:12; d:1.5; N; s:L:".."; a:N:{..}; O:L:"cls":N:{..}).
     */
    private static function valueLength(string $raw, int $offset): int|false
    {
        $c = $raw[$offset] ?? '';
        switch ($c) {
            case 'b':
            case 'i':
            case 'd':
            case 'N':
                $end = strpos($raw, ';', $offset);
                return $end === false ? false : $end - $offset + 1;
            case 's':
                if (! preg_match('/\As:(\d+):"/', substr($raw, $offset), $m)) {
                    return false;
                }
                return strlen($m[0]) + (int) $m[1] + 2; // "…";
            case 'a':
            case 'O':
                if (! preg_match('/\A(?:a|O:\d+:"[^"]*"):\d+:\{/', substr($raw, $offset), $m)) {
                    return false;
                }
                $depth = 1;
                $j = $offset + strlen($m[0]);
                $len = strlen($raw);
                while ($j < $len && $depth > 0) {
                    $ch = $raw[$j];
                    if ($ch === '{') {
                        $depth++;
                    } elseif ($ch === '}') {
                        $depth--;
                    } elseif ($ch === 's' && ($raw[$j + 1] ?? '') === ':'
                        && preg_match('/\As:(\d+):"/', substr($raw, $j), $sm)) {
                        // sauter la chaine d'un coup (peut contenir { })
                        $j += strlen($sm[0]) + (int) $sm[1] + 1;
                    }
                    $j++;
                }
                return $depth === 0 ? $j - $offset : false;
            default:
                return false;
        }
    }
}
