<?php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Email test function
function sendTestEmail($to, $subject, $message) {
    $headers = array(
        'From' => $_ENV['SMTP_FROM'],
        'To' => $to,
        'Subject' => $subject
    );

    $smtp = array(
        'host' => $_ENV['SMTP_HOST'],
        'port' => $_ENV['SMTP_PORT'],
        'username' => $_ENV['SMTP_USER'],
        'password' => $_ENV['SMTP_PASS'],
        'auth' => true,
        'secure' => 'tls'
    );

    try {
        $transport = new Swift_SmtpTransport(
            $smtp['host'],
            $smtp['port'],
            $smtp['secure']
        );
        
        $transport->setUsername($smtp['username']);
        $transport->setPassword($smtp['password']);
        
        $mailer = new Swift_Mailer($transport);
        
        $message = (new Swift_Message($headers['Subject']))
            ->setFrom([$headers['From'] => 'MSWD Livelihood System'])
            ->setTo([$to])
            ->setBody($message, 'text/html');
        
        $result = $mailer->send($message);
        
        if ($result) {
            echo "Email sent successfully!";
        } else {
            echo "Failed to send email.";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Test sending email
echo "Testing email configuration...\n";
sendTestEmail(
    $_ENV['SMTP_USER'], // Sending to the same email for testing
    "Test Email from MSWD Livelihood System",
    "<h1>Email Configuration Test</h1>
    <p>If you're seeing this, the email configuration is working!</p>
    <p>Date: " . date('Y-m-d H:i:s') . "</p>"
);
