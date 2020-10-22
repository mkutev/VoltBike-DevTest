<?php
$to      = 'krastev@telus.net';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: sales@voltbike.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
echo "Sent";
?> 
