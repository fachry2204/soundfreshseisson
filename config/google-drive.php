<?php

return [
    'binary' => env('RCLONE_BINARY', 'rclone'),
    'config_path' => env('RCLONE_CONFIG'),
    'remote' => env('RCLONE_REMOTE', 'gdrive'),
    'base_path' => env('RCLONE_BASE_PATH', 'Original Sessions'),
    'timeout' => (int) env('RCLONE_TIMEOUT', 3600),
];
