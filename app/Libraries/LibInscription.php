<?php

namespace App\Libraries;

use Config\Database;

/**
 * Port CI3 -> CI4 de LibInscription : hachage password compatible
 * CI3 (hmac + pepper) et insertion webuser.
 */
class LibInscription
{
    /** @var \CodeIgniter\Database\BaseConnection */
    private $db;

    public $data;

    private string $keysha = 'nikajhdgsjgdsdssd';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getPass(string $val): string
    {
        $pwd_peppered = hash_hmac('sha256', $val, $this->keysha);
        $pwd_hashed   = password_hash($pwd_peppered, PASSWORD_DEFAULT);
        return $this->db->escape($pwd_hashed);
    }

    public function postNewUser(): void
    {
        $chaineSQL = 'insert into webuser ';
        $strFields = ' (';
        $strValues = ' values (';
        foreach (service('request')->getPost() ?? [] as $key => $val) {
            if ($key === 'passconf') {
                continue;
            }
            if ($key === 'pass') {
                $pwd_peppered = hash_hmac('sha256', $val, $this->keysha);
                $val          = password_hash($pwd_peppered, PASSWORD_DEFAULT);
            }
            $strFields .= $key . ',';
            $strValues .= $this->db->escape($val) . ',';
        }
        $chaineSQL .= substr($strFields, 0, -1) . ')' . substr($strValues, 0, -1) . ')';
        $this->db->query($chaineSQL);
    }

    public function removeUser($iduser): string
    {
        $id        = $this->db->escape($iduser);
        $chaineSQL = "DELETE FROM webuser WHERE where id = $id";
        return $this->db->query($chaineSQL) ? 'OK' : 'NON';
    }
}
