<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';

function respond(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    ensureCommunityTables($pdo);

    $statement = $pdo->query(
        'SELECT p.id, p.title, p.content, p.created_at, u.display_name AS name
         FROM posts p
         JOIN users u ON u.id = p.user_id
         ORDER BY p.id DESC
         LIMIT 50'
    );

    respond([
        'success' => true,
        'data' => $statement->fetchAll(),
    ]);
} catch (Throwable $exception) {
    respond([
        'success' => false,
        'message' => '接口出错，请检查数据库配置。',
        'detail' => $exception->getMessage(),
    ], 500);
}
