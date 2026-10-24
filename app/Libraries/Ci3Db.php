<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Adaptateur $this->db CI3 : conserve les acces directs (query(),
 * escape(), table(), trans_*, ...) et simule la file de clauses du
 * Query Builder CI3 — $this->db->where() sans table accumule la
 * clause jusqu'a l'appel terminal get()/insert()/update()/delete()/
 * count_all_results(), exactement comme CI3.
 */
class Ci3Db
{
    private BaseConnection $conn;

    /** @var array<array{0:string,1:array}> clauses en file (where, select, join...) */
    private array $queue = [];

    /** methodes de clauses QB (snake_case CI3 -> camel CI4) */
    private const CLAUSES = [
        'select' => 'select', 'select_max' => 'selectMax', 'select_min' => 'selectMin',
        'select_avg' => 'selectAvg', 'select_sum' => 'selectSum', 'select_count' => 'selectCount',
        'distinct' => 'distinct', 'from' => 'from', 'join' => 'join',
        'where' => 'where', 'or_where' => 'orWhere', 'where_in' => 'whereIn',
        'or_where_in' => 'orWhereIn', 'where_not_in' => 'whereNotIn',
        'or_where_not_in' => 'orWhereNotIn', 'like' => 'like', 'or_like' => 'orLike',
        'not_like' => 'notLike', 'or_not_like' => 'orNotLike', 'group_by' => 'groupBy',
        'having' => 'having', 'or_having' => 'orHaving', 'order_by' => 'orderBy',
        'limit' => 'limit', 'offset' => 'offset', 'set' => 'set',
        'set_insert_batch' => 'setInsertBatch', 'set_update_batch' => 'setUpdateBatch',
        'group_start' => 'groupStart', 'or_group_start' => 'orGroupStart',
        'not_group_start' => 'notGroupStart', 'or_not_group_start' => 'orNotGroupStart',
        'group_end' => 'groupEnd', 'not_like' => 'notLike',
    ];

    /** methodes terminales QB qui executent la requete (table = arg 1) */
    private const TERMINALS_TABLE_FIRST = [
        'insert' => 'insert', 'update' => 'update', 'delete' => 'delete',
        'replace' => 'replace', 'empty_table' => 'emptyTable', 'truncate' => 'truncate',
        'count_all_results' => 'countAllResults', 'insert_batch' => 'insertBatch',
        'update_batch' => 'updateBatch',
    ];

    public function __construct(?BaseConnection $conn = null)
    {
        $this->conn = $conn ?? Database::connect();
    }

    public function query(string $sql, ...$args)
    {
        try {
            return $this->conn->query($sql, ...$args);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // CI3 avec db_debug=false : retourne false au lieu de
            // lancer ; l'erreur reste visible via ->error() et le log.
            log_message('error', 'Ci3Db::query ' . $e->getMessage());
            return false;
        }
    }

    public function escape($str)
    {
        return $this->conn->escape($str);
    }

    public function escape_str($str)
    {
        return $this->conn->escape($str);
    }

    public function table($table): BaseBuilder
    {
        return $this->conn->table($table);
    }

    public function get(?string $table = null, ?int $limit = null, ?int $offset = null)
    {
        $b = $this->builder($table);
        $this->queue = [];
        try {
            // BaseBuilder::get() : $offset est int non-nullable (defaut 0).
            return $b->get($limit, $offset ?? 0);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Ci3Db::get ' . $e->getMessage());
            return false;
        }
    }

    public function get_where(string $table, array $where = [], ?int $limit = null, ?int $offset = null)
    {
        $b = $this->builder($table);
        $this->queue = [];
        try {
            return $b->getWhere($where, $limit, $offset);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Ci3Db::get_where ' . $e->getMessage());
            return false;
        }
    }

    public function count_all_results(?string $table = null): int
    {
        $b = $this->builder($table);
        $this->queue = [];
        try {
            return $b->countAllResults();
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Ci3Db::count_all_results ' . $e->getMessage());
            return 0;
        }
    }

    public function get_compiled_select(string $table = '', bool $reset = true): string
    {
        $b = $this->builder($table);
        if ($reset) {
            $this->queue = [];
        }
        return $b->getCompiledSelect($reset);
    }

    public function __call(string $m, array $a)
    {
        // clause en file : $db->where(...) sans table
        if (isset(self::CLAUSES[$m])) {
            $this->queue[] = [self::CLAUSES[$m], $a];
            return $this;
        }
        // terminal table-first : $db->insert('t', $data)
        if (isset(self::TERMINALS_TABLE_FIRST[$m])) {
            $table = $a[0] ?? null;
            array_shift($a);
            $b = $this->builder($table);
            $this->queue = [];
            try {
                return $b->{self::TERMINALS_TABLE_FIRST[$m]}(...$a);
            } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
                log_message('error', 'Ci3Db::' . $m . ' ' . $e->getMessage());
                return false;
            }
        }
        // sinon : relais a la connexion (trans_*, insertID,
        // affectedRows, tableExists, getLastQuery, close, ...)
        $map = [
            'insert_id' => 'insertID', 'affected_rows' => 'affectedRows',
            'last_query' => 'getLastQuery', 'table_exists' => 'tableExists',
            'field_exists' => 'fieldExists', 'list_tables' => 'listTables',
            'list_fields' => 'getFieldNames', 'trans_start' => 'transStart',
            'trans_complete' => 'transComplete', 'trans_begin' => 'transBegin',
            'trans_status' => 'transStatus', 'trans_commit' => 'transCommit',
            'trans_rollback' => 'transRollback', 'platform' => 'getPlatform',
            'version' => 'getVersion', 'conn_id' => 'connID',
            'protect_identifiers' => 'protectIdentifiers',
            'escape_identifiers' => 'protectIdentifiers',
            'dbprefix' => 'DBPrefix',
        ];
        if (isset($map[$m])) {
            $m2 = $map[$m];
            return $this->conn->{$m2}(...$a);
        }
        return $this->conn->{$m}(...$a);
    }

    /** Construit un builder sur la table en appliquant la file. */
    private function builder(?string $table): BaseBuilder
    {
        $b = $this->conn->table($table);
        foreach ($this->queue as [$method, $args]) {
            $b->{$method}(...$args);
        }
        return $b;
    }
}
