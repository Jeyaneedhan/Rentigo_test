<?php

/**
 * Email Helper
 * Manual SMTP implementation for Outlook (No libraries)
 */

function sendResetEmail($to, $code)
{
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
 * Send PM Approval Email
 */
function sendPMApprovalEmail($to, $name)
{
    $subject = "Rentigo - Your Property Manager Application Has Been Approved!";

    $message = "
    <html>
    <head>
    <title>Rentigo PM Approval</title>
    </head>
    <body>
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
        <h2 style='color: #45a9ea; text-align: center;'>Rentigo</h2>
        <p>Hello $name,</p>
        <p>Congratulations! We are pleased to inform you that your Property Manager application has been <strong style='color: #22c55e;'>approved</strong>.</p>
        <div style='background: #f0fdf4; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; border: 1px solid #bbf7d0;'>
            <h3 style='color: #16a34a; margin: 0;'>🎉 Welcome to Rentigo!</h3>
            <p style='color: #166534; margin: 10px 0 0 0;'>You can now log in and start managing properties.</p>
        </div>
        <p>You now have full access to the Property Manager dashboard where you can:</p>
        <ul style='color: #475569;'>
            <li>Manage properties assigned to you</li>
            <li>Handle tenant requests and maintenance</li>
            <li>Conduct property inspections</li>
            <li>And much more!</li>
        </ul>
        <p>If you have any questions, please don't hesitate to contact our support team.</p>
        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
        <p style='font-size: 12px; color: #64748b; text-align: center;'>&copy; " . date('Y') . " Rentigo. All rights reserved.</p>
    </div>
    </body>
    </html>
    ";

    return sendSMTP($to, $subject, $message);
}

/**
 * Send PM Rejection Email
 */
function sendPMRejectionEmail($to, $name)
{
    $subject = "Rentigo - Property Manager Application Update";

    $message = "
    <html>
    <head>
    <title>Rentigo PM Application Update</title>
    </head>
    <body>
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
        <h2 style='color: #45a9ea; text-align: center;'>Rentigo</h2>
        <p>Hello $name,</p>
        <p>Thank you for your interest in becoming a Property Manager with Rentigo.</p>
        <div style='background: #fef2f2; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; border: 1px solid #fecaca;'>
            <h3 style='color: #dc2626; margin: 0;'>Application Not Approved</h3>
            <p style='color: #991b1b; margin: 10px 0 0 0;'>Unfortunately, your application could not be approved at this time.</p>
        </div>
        <p>We encourage you to re-register with the correct approved details and documents. Please ensure that:</p>
        <ul style='color: #475569;'>
            <li>All personal information is accurate and up-to-date</li>
            <li>Your employee ID document is clear and valid</li>
            <li>All required fields are properly filled</li>
        </ul>
        <p>If you believe this was an error or have any questions, please contact our support team for assistance.</p>
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
function sendSMTP($to, $subject, $message)
{
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
    $getResponse = function ($socket) {
        $response = "";
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $response;
    };

    // Helper to receive response and check for expected code
    $checkResponse = function ($socket, $expectedCode) use ($getResponse, &$smtp_error) {
        $response = $getResponse($socket);
        if (substr($response, 0, 3) !== (string)$expectedCode) {
            $smtp_error = "Expected $expectedCode, got $response";
            error_log("SMTP Error: $smtp_error");
            return false;
        }
        return true;
    };

    // Helper to send command
    $sendCommand = function ($socket, $command) {
        fputs($socket, $command . "\r\n");
    };

    if (substr($getResponse($socket), 0, 3) !== "220") {
        $smtp_error = "Initial connection failed";
        return false;
    }

    $sendCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    if (!$checkResponse($socket, 250)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, "STARTTLS");
    if (!$checkResponse($socket, 220)) {
        fclose($socket);
        return false;
    }

    // Switch to TLS
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        $smtp_error = "Failed to enable crypto (TLS)";
        fclose($socket);
        return false;
    }

    usleep(500000); // 0.5 sec delay for STARTTLS sync

    $sendCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    if (!$checkResponse($socket, 250)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, "AUTH LOGIN");
    if (!$checkResponse($socket, 334)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, base64_encode($user));
    if (!$checkResponse($socket, 334)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, base64_encode($pass));
    if (!$checkResponse($socket, 235)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, "MAIL FROM: <$from>");
    if (!$checkResponse($socket, 250)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, "RCPT TO: <$to>");
    if (!$checkResponse($socket, 250)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, "DATA");
    if (!$checkResponse($socket, 354)) {
        fclose($socket);
        return false;
    }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "From: Rentigo <$from>\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Date: " . date("r") . "\r\n";
    $headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . ">\r\n";

    $sendCommand($socket, $headers . "\r\n" . $message . "\r\n.");
    if (!$checkResponse($socket, 250)) {
        fclose($socket);
        return false;
    }

    $sendCommand($socket, "QUIT");
    fclose($socket);

    return true;
}
