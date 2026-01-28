<?php
/**
 * Email Helper
 * Manual SMTP implementation for Outlook (No libraries)
 */

function sendResetEmail($to, $code) {
    $subject = "Rentigo - Password Reset Code";
    
    $message = "
    <html>
    <head>
    <title>Rentigo Password Reset</title>
    </head>
    <body>
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
        <h2 style='color: #45a9ea; text-align: center;'>Rentigo</h2>
        <p>Hello,</p>
        <p>You requested to reset your password. Please use the following 6-digit code to proceed:</p>
        <div style='background: #f8fafc; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
            <h1 style='font-size: 32px; letter-spacing: 5px; color: #1e293b; margin: 0;'>$code</h1>
        </div>
        <p>This code will expire in 15 minutes.</p>
        <p>If you did not request this, please ignore this email.</p>
        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
        <p style='font-size: 12px; color: #64748b; text-align: center;'>&copy; " . date('Y') . " Rentigo. All rights reserved.</p>
    </div>
    </body>
    </html>
    ";

    return sendSMTP($to, $subject, $message);
}

/**
 * Basic SMTP client for Outlook
 */
$smtp_error = "";
function sendSMTP($to, $subject, $message) {
    global $smtp_error;
    $host = MAIL_HOST;
    $port = MAIL_PORT;
    $user = MAIL_USER;
    $pass = MAIL_PASS;
    $from = MAIL_FROM;

    $timeout = 15;
    $socket = @stream_socket_client("tcp://$host:$port", $errno, $errstr, $timeout);

    if (!$socket) {
        $smtp_error = "Socket Error: $errstr ($errno)";
        return false;
    }

    // Helper to receive response
    $getResponse = function($socket) {
        $response = "";
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $response;
    };

    // Helper to receive response and check for expected code
    $checkResponse = function($socket, $expectedCode) use ($getResponse, &$smtp_error) {
        $response = $getResponse($socket);
        if (substr($response, 0, 3) !== (string)$expectedCode) {
            $smtp_error = "Expected $expectedCode, got $response";
            error_log("SMTP Error: $smtp_error");
            return false;
        }
        return true;
    };

    // Helper to send command
    $sendCommand = function($socket, $command) {
        fputs($socket, $command . "\r\n");
    };

    if (substr($getResponse($socket), 0, 3) !== "220") {
        $smtp_error = "Initial connection failed";
        return false;
    }

    $sendCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    if (!$checkResponse($socket, 250)) { fclose($socket); return false; }

    $sendCommand($socket, "STARTTLS");
    if (!$checkResponse($socket, 220)) { fclose($socket); return false; }

    // Switch to TLS
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        $smtp_error = "Failed to enable crypto (TLS)";
        fclose($socket);
        return false;
    }

    usleep(500000); // 0.5 sec delay for STARTTLS sync

    $sendCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    if (!$checkResponse($socket, 250)) { fclose($socket); return false; }

    $sendCommand($socket, "AUTH LOGIN");
    if (!$checkResponse($socket, 334)) { fclose($socket); return false; }

    $sendCommand($socket, base64_encode($user));
    if (!$checkResponse($socket, 334)) { fclose($socket); return false; }

    $sendCommand($socket, base64_encode($pass));
    if (!$checkResponse($socket, 235)) { fclose($socket); return false; }

    $sendCommand($socket, "MAIL FROM: <$from>");
    if (!$checkResponse($socket, 250)) { fclose($socket); return false; }

    $sendCommand($socket, "RCPT TO: <$to>");
    if (!$checkResponse($socket, 250)) { fclose($socket); return false; }

    $sendCommand($socket, "DATA");
    if (!$checkResponse($socket, 354)) { fclose($socket); return false; }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "From: Rentigo <$from>\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Date: " . date("r") . "\r\n";
    $headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . ">\r\n";

    $sendCommand($socket, $headers . "\r\n" . $message . "\r\n.");
    if (!$checkResponse($socket, 250)) { fclose($socket); return false; }

    $sendCommand($socket, "QUIT");
    fclose($socket);

    return true;
}
