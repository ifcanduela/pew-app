<?php

namespace app\services\debugbar;

use ifcanduela\db\Database;
use DebugBar\DataCollector\PDO\PDOCollector;

class DatabaseCollector extends PDOCollector
{
    protected $db;
    protected $connections = [];

    public function __construct(Database $db)
    {
        $this->connections[] = $db;
    }

    public function getName()
    {
        return "database";
    }

    public function getWidgets()
    {
        return [
            "database" => array(
                "icon" => "database",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "database",
                "default" => "[]"
            ),
            "database:badge" => [
                "map" => "database.nb_statements",
                "default" => "0"
            ],
        ];

        return $widgets;
    }

    public function collect()
    {
        $queries = [];

        foreach ($this->connections as $db) {
            $queries = array_merge($queries, $db->getQueryHistory());
        }

        usort($queries, function ($a, $b) {
            return $a[0] - $b[0];
        });

        $data = [
            "nb_statements" => count($queries),
            "statements" => [],
            // "nb_failed_statements" => 0,
            // "accumulated_duration" => 0,
            // "memory_usage" => 0,
            // "peak_memory_usage" => 0,
        ];

        foreach ($queries as $k => $v) {
            [$timestamp, $sql, $params, $success, $affectedRows] = $v;

            $v = [
                "sql" => $sql,
                "row_count" => $affectedRows,
                // "stmt_id" => random_int(0, 999999),
                "prepared_stmt" => $sql,
                "params" => (object) $params,
                // "duration" => 0,
                // "duration_str" => "0",
                // "memory" => 0,
                // "memory_str" => "0",
                // "end_memory" => 0,
                // "end_memory_str" => "0",
                "is_success" => $success,
                "error_code" => 0,
                "error_message" => "",
            ];

            $data["statements"][] = $v;
        }

        return $data;
    }
}
