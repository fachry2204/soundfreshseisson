<?php

namespace App\Services\Files;

use RuntimeException;

class ClamAvScanner
{
    public function scan(string $path): string
    {
        if (! config('services.clamav.enabled')) {
            return 'pending';
        }
        $socket = @stream_socket_client('tcp://'.config('services.clamav.host').':'.config('services.clamav.port'), $errorCode, $errorMessage, 10);
        if (! $socket) {
            throw new RuntimeException('Antivirus service tidak tersedia.');
        }
        stream_set_timeout($socket, 30);
        fwrite($socket, "zINSTREAM\0");
        $handle = fopen($path, 'rb');
        while (! feof($handle)) {
            $chunk = fread($handle, 8192);
            fwrite($socket, pack('N', strlen($chunk)).$chunk);
        }
        fclose($handle);
        fwrite($socket, pack('N', 0));
        $response = stream_get_contents($socket);
        fclose($socket);

        return match (true) {
            str_contains($response, 'OK') => 'clean',
            str_contains($response, 'FOUND') => 'infected',
            default => throw new RuntimeException('Antivirus memberikan respons yang tidak dikenali.'),
        };
    }
}
