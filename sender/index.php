<?php
/**
 * Copyright (c) 2019 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Spatie\Async\Pool;

$sendingTime = microtime(true);

$config = require __DIR__ . '/config.php';
$params = require __DIR__ . '/../campaign/' . $config['current_campaign'] . '/campaign-config.php';
$emails = require __DIR__ . '/../campaign/' . $config['current_campaign'] . '/email-list.php';
$uniqueEmails = array_unique($emails);

echo "Max execution time: " . ini_get('max_execution_time'). PHP_EOL;
echo "E-mails: " . count($emails) . PHP_EOL;
echo "Unique e-mails: " . count($uniqueEmails) . PHP_EOL;

$mailer = new SmtpMailer($config['smtp']);
$latte = new Latte\Engine;
$pool = Pool::create();

// Send messages in Loop
foreach ($uniqueEmails as $email)
{
    $pool[] = async(function () use ($latte, $mailer, $config, $params, $sendingTime, $email)
    {
        $path = __DIR__ . '/../campaign/' . $config['current_campaign'] . '/message.latte';
        $htmlBody = $latte->renderToString($path,$params + ['receiver' => $email]);

        $message = (new Message)
            ->setFrom($config['sender'])
            ->addTo($email)
            ->setSubject($params['subject'])
            ->setHtmlBody($htmlBody);

        try {
            $mailer->send($message);
            echo "Message sent to {$email} after: " . (microtime(true) - $sendingTime) .'s' . PHP_EOL;
        } catch (\Exception $exception) {
            echo "Error: {$exception->getMessage()} when sending to {$email} after: "
                . (microtime(true) - $sendingTime) .'s' . PHP_EOL;
        }
    });
}

await($pool);

echo "Execution time: " . (microtime(true) - $sendingTime) .'s' . PHP_EOL;