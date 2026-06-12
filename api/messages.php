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

function getRequestPayload(): array
{
    $rawBody = file_get_contents('php://input');
    $jsonBody = json_decode($rawBody ?: '', true);

    return is_array($jsonBody) ? $jsonBody : $_POST;
}

function requireAdminPassword(array $payload): void
{
    $config = getAppConfig();
    $savedPassword = (string)($config['admin_password'] ?? '');
    $inputPassword = (string)($payload['admin_password'] ?? '');

    if ($savedPassword === '' || $savedPassword === 'change_this_admin_password') {
        respond([
            'success' => false,
            'message' => '请先在 api/config.php 里设置 admin_password。',
        ], 500);
    }

    if (!hash_equals($savedPassword, $inputPassword)) {
        respond([
            'success' => false,
            'message' => '管理密码不正确。',
        ], 403);
    }
}

try {
    $pdo = getDatabaseConnection();
    ensureMessagesTable($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $statement = $pdo->query(
            'SELECT id, name, content, created_at
             FROM messages
             ORDER BY id DESC
             LIMIT 50'
        );

        respond([
            'success' => true,
            'data' => $statement->fetchAll(),
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payload = getRequestPayload();
        $action = (string)($payload['action'] ?? 'create');

        if ($action === 'delete') {
            requireAdminPassword($payload);

            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                respond([
                    'success' => false,
                    'message' => '留言 ID 不正确。',
                ], 422);
            }

            $statement = $pdo->prepare('DELETE FROM messages WHERE id = :id');
            $statement->execute([':id' => $id]);

            respond([
                'success' => true,
                'message' => '留言已删除。',
            ]);
        }

        $name = trim((string)($payload['name'] ?? ''));
        $content = trim((string)($payload['content'] ?? ''));

        if ($name === '' || $content === '') {
            respond([
                'success' => false,
                'message' => '昵称和留言内容都不能为空。',
            ], 422);
        }

        if (mb_strlen($name, 'UTF-8') > 50) {
            respond([
                'success' => false,
                'message' => '昵称不能超过 50 个字。',
            ], 422);
        }

        if (mb_strlen($content, 'UTF-8') > 500) {
            respond([
                'success' => false,
                'message' => '留言不能超过 500 个字。',
            ], 422);
        }

        $statement = $pdo->prepare(
            'INSERT INTO messages (name, content)
             VALUES (:name, :content)'
        );
        $statement->execute([
            ':name' => $name,
            ':content' => $content,
        ]);

        respond([
            'success' => true,
            'message' => '留言保存成功。',
        ], 201);
    }

    respond([
        'success' => false,
        'message' => '不支持这个请求方法。',
    ], 405);
} catch (Throwable $exception) {
    respond([
        'success' => false,
        'message' => '接口出错，请检查数据库配置。',
        'detail' => $exception->getMessage(),
    ], 500);
}
