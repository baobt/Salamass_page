<?php

/**
 * /api/content
 *   GET – return all content entries (public)
 *   PUT – replace all content entries (public in original; add requireAuth() if needed)
 *
 * Equivalent to: GET /api/content  &  PUT /api/content  in server.js
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

handleCors();

$method = $_SERVER['REQUEST_METHOD'];

// ─── GET /api/content ─────────────────────────────────────────────────────────

if ($method === 'GET') {
    header('Cache-Control: no-store');

    $rows = dbQuery("
        SELECT language, content_key, content_value
        FROM content_entries
        ORDER BY language, content_key
    ");

    jsonResponse(['success' => true, 'data' => unflattenContent($rows)]);
}

// ─── PUT /api/content ─────────────────────────────────────────────────────────

if ($method === 'PUT') {
    $body = requestBody(); // [lang => [key => value], ...]

    withTransaction(function (PDO $pdo) use ($body) {
        $pdo->exec('DELETE FROM content_entries');

        $stmt = $pdo->prepare(
            'INSERT INTO content_entries (language, content_key, content_value) VALUES (?, ?, ?)'
        );

        foreach ($body as $lang => $fields) {
            foreach ($fields as $key => $value) {
                $stmt->execute([$lang, $key, $value]);
            }
        }
    });

    jsonResponse(['success' => true]);
}

jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
