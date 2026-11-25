<?php

function database_url(): string
{
    $path = dirname(__DIR__) . '/.env';
    if (!is_readable($path)) {
        throw new RuntimeException('Database settings were not found.');
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
        if (str_starts_with($line, 'DATABASE_URL=')) {
            return trim(substr($line, strlen('DATABASE_URL=')), "\"'");
        }
    }

    throw new RuntimeException('Database settings were not found.');
}

function db_rows(string $query, array $params = []): array
{
    $databaseUrl = database_url();
    $parts = parse_url($databaseUrl);
    $host = preg_replace('/^[^.]+\./', 'api.', $parts['host']);

    $body = json_encode([
        'query' => $query,
        'params' => $params,
    ]);

    $handle = curl_init("https://{$host}/sql");
    curl_setopt_array($handle, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Neon-Connection-String: ' . $databaseUrl,
        ],
    ]);

    $response = curl_exec($handle);
    $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    $error = curl_error($handle);
    curl_close($handle);

    if ($response === false || $status >= 400) {
        throw new RuntimeException('The database could not be reached.');
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded) || !isset($decoded['rows'])) {
        throw new RuntimeException('The database returned an unexpected response.');
    }

    return $decoded['rows'];
}
