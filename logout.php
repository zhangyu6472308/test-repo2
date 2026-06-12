<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

session_destroy();
session_start();
flash('已退出登录。');
redirectTo('index.php');
