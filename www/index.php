<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';

use App\ClickhouseExample;

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = null;
$data = [
    'totalVisits' => 0,
    'avgDuration' => 0,
    'bySource' => [],
    'recent' => [],
];

try {
    $clickhouse = new ClickhouseExample();
    $clickhouse->init();
    $clickhouse->seed();
    $data = $clickhouse->dashboard();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>ЛР6 — ClickHouse аналитика</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        body { font-family: Arial, Helvetica, sans-serif; max-width: 1100px; margin: 0 auto; padding: 20px; }
        .box { border: 1px solid #ddd; border-radius: 8px; padding: 12px; margin: 14px 0; background: #fafafa; }
        .error { color: #b00; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .metric { font-size: 28px; font-weight: bold; }
        .label { color: #666; font-size: 14px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <h1>Лабораторная работа №6 — ClickHouse</h1>

    <?php if ($error): ?>
        <div class="box error">
            <strong>Ошибка:</strong> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid">
        <div class="box">
            <div class="label">Всего визитов</div>
            <div class="metric"><?= e($data['totalVisits']) ?></div>
        </div>
        <div class="box">
            <div class="label">Средняя длительность</div>
            <div class="metric"><?= e($data['avgDuration']) ?></div>
        </div>
    </div>

    <div class="box">
        <h2>Аналитика по источникам</h2>
        <table>
            <tr>
                <th>Источник</th>
                <th>Визиты</th>
                <th>Средняя длительность</th>
            </tr>
            <?php foreach ($data['bySource'] as $row): ?>
                <tr>
                    <td><?= e($row['source'] ?? '') ?></td>
                    <td><?= e($row['visits'] ?? '') ?></td>
                    <td><?= e($row['avg_duration'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="box">
        <h2>Последние записи</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Источник</th>
                <th>Страница</th>
                <th>Длительность</th>
            </tr>
            <?php foreach ($data['recent'] as $row): ?>
                <tr>
                    <td><?= e($row['id'] ?? '') ?></td>
                    <td><?= e($row['visit_date'] ?? '') ?></td>
                    <td><?= e($row['source'] ?? '') ?></td>
                    <td><?= e($row['page'] ?? '') ?></td>
                    <td><?= e($row['duration'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>