<?php
require_once __DIR__ . '/DB/database.php';
foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
    echo "TABLE: {$table}\n";
    foreach ($pdo->query('SHOW COLUMNS FROM `' . str_replace('`', '``', $table) . '`')->fetchAll(PDO::FETCH_ASSOC) as $column) {
        echo $column['Field'] . ' ' . $column['Type'] . ' ' . $column['Null'] . ' ' . $column['Default'] . "\n";
    }
}
