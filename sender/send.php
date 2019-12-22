<?php
/**
 * Copyright (c) 2019 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

// Display errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Load configs & email-list
$config = require __DIR__ . '/config.php';
$params = require __DIR__ . '/../campaign/' . $config['current_campaign'] . '/campaign-config.php';
$emails = require __DIR__ . '/../campaign/' . $config['current_campaign'] . '/email-list.php';

// Remove duplicated addresses & setup timer
$uniqueEmails = array_unique($emails);
$startedAt = microtime(true);

// Print PHP settings & email stats
echo "Max execution time: "         . (ini_get('max_execution_time') == 0 ? 'OK' : 'FAILED') . PHP_EOL;
echo "Supported async: "            . (Spatie\Async\Pool::isSupported() ? 'OK' : 'FAILED') . PHP_EOL;
echo "E-mails in queue: "           . count($emails) . PHP_EOL;
echo "Unique e-mails in queue: "    . count($uniqueEmails) . PHP_EOL;

// Create async pool
$pool = Spatie\Async\Pool::create()->concurrency(100)->timeout(30)->sleepTime(50000);

// Send e-mail via SMTP asynchronously
foreach ($uniqueEmails as $email) {

    $pool
        // Add task
        ->add(function () use ($email, $config, $params) {
            $messageFile = __DIR__ . '/../campaign/' . $config['current_campaign'] . '/message.latte';
            $htmlBody = (new Latte\Engine)->renderToString($messageFile,$params + ['receiver' => $email]);

            $message = (new \Nette\Mail\Message)
                ->setFrom($config['sender'])
                ->setSubject($params['subject'])
                ->addTo($email)
                ->setHtmlBody($htmlBody);

            $smtpMailer = new \Nette\Mail\SmtpMailer($config['smtp']);
            $smtpMailer->send($message);
        })

        // Print success
        ->then(function () use ($email, $startedAt) {
            echo "Succeeded: {$email} | Timeout: " . (microtime(true) - $startedAt) . 's' . PHP_EOL;
        })

        // Or catch exceptions
        ->catch(function (\Exception $exception) use ($email, $startedAt) {
            echo "Error: {$email} | Timeout: ". (microtime(true) - $startedAt) . 's' .
                " | Exception: {$exception->getMessage()}" . PHP_EOL;
        })

        // Or print concurrency timeout
        ->timeout(function () use ($email, $startedAt) {
            echo "Concurrency timeout: {$email} | Timeout: ". (microtime(true) - $startedAt) . 's' . PHP_EOL;
        })
    ;
}

$pool->wait();

echo "Execution time: " . (microtime(true) - $startedAt) .'s' . PHP_EOL;