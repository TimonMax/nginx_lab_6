<?php

namespace App;

use App\Helpers\ClientFactory;

class ClickhouseExample
{
    private $client;

    public function __construct()
    {
        $this->client = ClientFactory::make(CLICKHOUSE_BASE_URI);
    }

    # выполнить SQL и вернуть текст
    private function query(string $sql): string
    {
        $response = $this->client->post('', [
            'body' => $sql,
            'headers' => [
                'Content-Type' => 'text/plain; charset=utf-8',
            ],
        ]);

        $body = trim((string)$response->getBody());

        if ($body !== '' && (str_contains($body, 'DB::Exception') || str_contains($body, 'Code:'))) {
            throw new \RuntimeException($body);
        }

        return $body;
    }

    # превратить JSONEachRow в массив
    private function rows(string $sql): array
    {
        $text = trim($this->query($sql . ' FORMAT JSONEachRow'));

        if ($text === '') {
            return [];
        }

        $result = [];
        foreach (preg_split('/\R/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $row = json_decode($line, true);
            if (is_array($row)) {
                $result[] = $row;
            }
        }

        return $result;
    }

    # cоздаёт базу и таблицу, если их нет
    public function init(): void
    {
        $this->query("CREATE DATABASE IF NOT EXISTS " . CLICKHOUSE_DB);

        $this->query(
            "CREATE TABLE IF NOT EXISTS " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE . " (
                id UInt32,
                visit_date Date,
                source String,
                page String,
                duration UInt32
            )
            ENGINE = MergeTree()
            ORDER BY (visit_date, source, id)"
        );
    }

    # Заполнение таблицы, если она пустая (По сути, просто для демонстрации)
    public function seed(): void
    {
        $count = (int)$this->query("SELECT count() FROM " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE);

        if ($count > 0) {
            return;
        }

        $this->query(
            "INSERT INTO " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE . " (id, visit_date, source, page, duration) VALUES
            (1, '2026-03-01', 'Google', '/home', 120),
            (2, '2026-03-01', 'Steam', '/store', 95),
            (3, '2026-03-02', 'Google', '/blog', 140),
            (4, '2026-03-02', 'Telegram', '/promo', 60),
            (5, '2026-03-03', 'Yandex', '/home', 110),
            (6, '2026-03-03', 'Google', '/checkout', 180),
            (7, '2026-03-04', 'Steam', '/charts', 75),
            (8, '2026-03-04', 'Referral', '/pricing', 105),
            (9, '2026-03-05', 'Google', '/catalog', 130),
            (10,'2026-03-05', 'Telegram', '/checkout', 210)"
        );
    }

    # Сбор всех данных в массив
    public function dashboard(): array
    {
        return [
            'totalVisits' => (int)$this->query("SELECT count() FROM " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE),
            'avgDuration' => (float)$this->query("SELECT round(avg(duration), 2) FROM " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE),
            'bySource' => $this->rows(
                "SELECT
                    source,
                    count() AS visits,
                    round(avg(duration), 2) AS avg_duration
                 FROM " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE . "
                 GROUP BY source
                 ORDER BY visits DESC"
            ),
            'recent' => $this->rows(
                "SELECT
                    id,
                    visit_date,
                    source,
                    page,
                    duration
                 FROM " . CLICKHOUSE_DB . "." . CLICKHOUSE_TABLE . "
                 ORDER BY visit_date DESC, id DESC
                 LIMIT 10"
            ),
        ];
    }
}