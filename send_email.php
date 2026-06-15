<?php
// Lightweight Resend integration for sending transactional emails
// Usage: require 'send_email.php'; send_email($to, $subject, $html, $text = '');

require_once 'config.php';

function send_email($to, $subject, $html, $text = '') {
    global $RESEND_API_KEY, $MAIL_FROM;

    if (empty($RESEND_API_KEY)) {
        // API key not configured — silently skip (or log)
        error_log('Resend API key not configured; email not sent to ' . $to);
        return false;
    }

    $payload = json_encode([
        'from' => $MAIL_FROM,
        'to' => [$to],
        'subject' => $subject,
        'html' => $html,
        'text' => $text
    ]);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $RESEND_API_KEY,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log('Resend request failed: ' . $err);
        return false;
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }

    error_log('Resend returned HTTP ' . $httpCode . ': ' . $response);
    return false;
}
