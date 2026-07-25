<?php
namespace App;

use Exception;

class MailService
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromAddress;
    private string $fromName;
    private int $timeout;

    public function __construct()
    {
        $this->host = trim((string) MAIL_HOST);
        $this->port = (int) MAIL_PORT;
        $this->username = trim((string) MAIL_USERNAME);
        $this->password = trim((string) MAIL_PASSWORD);
        $this->encryption = strtolower(trim((string) MAIL_ENCRYPTION));
        $this->fromAddress = trim((string) MAIL_FROM_ADDRESS);
        $this->fromName = trim((string) MAIL_FROM_NAME);
        $this->timeout = max(5, (int) MAIL_TIMEOUT);

        if ($this->fromAddress === '' && $this->username !== '') {
            $this->fromAddress = $this->username;
        }
    }

    public function sendPasswordResetCode(string $toEmail, string $toName, string $code): void
    {
        $safeName = trim($toName) !== '' ? trim($toName) : 'usuario';
        $minutes = max(1, (int) ceil(PASSWORD_RESET_CODE_TTL / 60));
        $subject = 'Codigo de verificacion para restablecer tu contraseña';
        $htmlBody = '<div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">'
            . '<h2 style="margin-bottom: 12px; color: #0b6b2a;">PROJUMI</h2>'
            . '<p>Hola ' . htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8') . '.</p>'
            . '<p>Recibimos una solicitud para restablecer tu contraseña.</p>'
            . '<p style="margin: 24px 0;">'
            . '<span style="display: inline-block; padding: 12px 20px; font-size: 24px; letter-spacing: 6px; background: #f3f4f6; border-radius: 8px; font-weight: bold;">'
            . htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
            . '</span>'
            . '</p>'
            . '<p>Este codigo vence en ' . $minutes . ' minuto(s).</p>'
            . '<p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>'
            . '</div>';

        $textBody = "PROJUMI\n\n"
            . "Hola {$safeName}.\n\n"
            . "Recibimos una solicitud para restablecer tu contraseña.\n"
            . "Codigo de verificacion: {$code}\n"
            . "Este codigo vence en {$minutes} minuto(s).\n\n"
            . "Si no solicitaste este cambio, puedes ignorar este mensaje.\n";

        $this->send($toEmail, $subject, $htmlBody, $toName, $textBody);
    }

    public function send(string $toEmail, string $subject, string $htmlBody, string $toName = '', string $textBody = ''): void
    {
        $this->validateConfiguration();

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El correo destinatario no es valido.');
        }

        $socket = $this->openConnection();

        try {
            $this->expectResponse($socket, [220]);
            $this->sendCommand($socket, 'EHLO ' . $this->getClientHostname(), [250]);

            if (in_array($this->encryption, ['tls', 'starttls'], true)) {
                $this->sendCommand($socket, 'STARTTLS', [220]);

                $cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if ($cryptoEnabled !== true) {
                    throw new Exception('No se pudo establecer el canal seguro TLS con el servidor SMTP.');
                }

                $this->sendCommand($socket, 'EHLO ' . $this->getClientHostname(), [250]);
            }

            $this->authenticate($socket);
            $this->sendCommand($socket, 'MAIL FROM:<' . $this->fromAddress . '>', [250]);
            $this->sendCommand($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
            $this->sendCommand($socket, 'DATA', [354]);

            $message = $this->buildMessage($toEmail, $toName, $subject, $htmlBody, $textBody);
            $this->sendCommand($socket, $message . "\r\n.", [250]);

            try {
                $this->sendCommand($socket, 'QUIT', [221]);
            } catch (Exception $exception) {
                // El mensaje ya fue procesado; ignoramos errores al cerrar.
            }
        } finally {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }

    private function validateConfiguration(): void
    {
        if ($this->host === '') {
            throw new Exception('Configura MAIL_HOST en el archivo .env.');
        }

        if ($this->port <= 0) {
            throw new Exception('Configura MAIL_PORT con un valor valido en el archivo .env.');
        }

        if ($this->username === '' || $this->password === '') {
            throw new Exception('Configura MAIL_USERNAME y MAIL_PASSWORD en el archivo .env.');
        }

        if (!filter_var($this->fromAddress, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Configura MAIL_FROM_ADDRESS con un correo valido en el archivo .env.');
        }
    }

    private function openConnection()
    {
        $transport = $this->host . ':' . $this->port;
        if ($this->encryption === 'ssl') {
            $transport = 'ssl://' . $transport;
        }

        $socket = @stream_socket_client($transport, $errorCode, $errorMessage, $this->timeout, STREAM_CLIENT_CONNECT);
        if ($socket === false) {
            throw new Exception('No se pudo conectar al servidor SMTP: ' . $errorMessage . ' (' . $errorCode . ').');
        }

        stream_set_timeout($socket, $this->timeout);

        return $socket;
    }

    private function authenticate($socket): void
    {
        $this->sendCommand($socket, 'AUTH LOGIN', [334]);
        $this->sendCommand($socket, base64_encode($this->username), [334]);
        $this->sendCommand($socket, base64_encode($this->password), [235]);
    }

    private function buildMessage(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody): string
    {
        $boundary = 'b1_' . bin2hex(random_bytes(12));
        $textBody = trim($textBody) !== '' ? $textBody : strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));
        $textBody = $this->normalizeLineBreaks($textBody);
        $htmlBody = $this->normalizeLineBreaks($htmlBody);

        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . $this->formatAddress($this->fromAddress, $this->fromName),
            'To: ' . $this->formatAddress($toEmail, $toName),
            'Subject: ' . $this->encodeHeader($subject),
            'MIME-Version: 1.0',
            'Message-ID: ' . $this->buildMessageId(),
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $body = [];
        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/plain; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $textBody;
        $body[] = '--' . $boundary;
        $body[] = 'Content-Type: text/html; charset=UTF-8';
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $htmlBody;
        $body[] = '--' . $boundary . '--';
        $body[] = '';

        $message = implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);

        return $this->dotStuff($message);
    }

    private function sendCommand($socket, string $command, array $expectedCodes): string
    {
        $payload = $command . "\r\n";
        $length = strlen($payload);
        $offset = 0;

        while ($offset < $length) {
            $written = fwrite($socket, substr($payload, $offset));
            if ($written === false || $written === 0) {
                throw new Exception('No se pudo enviar el comando SMTP.');
            }
            $offset += $written;
        }

        return $this->expectResponse($socket, $expectedCodes);
    }

    private function expectResponse($socket, array $expectedCodes): string
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;

            if (preg_match('/^\d{3}\s/', $line) === 1) {
                break;
            }
        }

        if ($response === '') {
            throw new Exception('El servidor SMTP no envio ninguna respuesta.');
        }

        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new Exception('Respuesta SMTP inesperada: ' . trim($response));
        }

        return $response;
    }

    private function formatAddress(string $email, string $name = ''): string
    {
        $email = trim($email);
        $name = trim($name);

        if ($name === '') {
            return '<' . $email . '>';
        }

        return $this->encodeHeader($name) . ' <' . $email . '>';
    }

    private function encodeHeader(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function buildMessageId(): string
    {
        $host = preg_replace('/[^A-Za-z0-9\.\-]/', '', $this->host);
        if ($host === '') {
            $host = 'localhost';
        }

        return '<' . bin2hex(random_bytes(16)) . '@' . $host . '>';
    }

    private function dotStuff(string $message): string
    {
        return preg_replace('/(^|\r\n)\./', '$1..', $message) ?? $message;
    }

    private function normalizeLineBreaks(string $content): string
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        return str_replace("\n", "\r\n", $content);
    }

    private function getClientHostname(): string
    {
        $hostname = gethostname();
        if (!is_string($hostname) || trim($hostname) === '') {
            return 'localhost';
        }

        return preg_replace('/[^A-Za-z0-9\.\-]/', '', $hostname) ?: 'localhost';
    }
}
