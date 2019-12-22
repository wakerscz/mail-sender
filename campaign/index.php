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

// Setup no-cache for open-rate monitoring
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

// Handle GET params
$campaignName   = isset($_GET['name']) ? $_GET['name'] : NULL;
$receiver       = isset($_GET['receiver']) ? $_GET['receiver'] : NULL;
$imageName      = isset($_GET['image']) ? $_GET['image'] : NULL;

// Setup paths to campaign folder and image files
$campaignPath   = __DIR__ . '/' . $campaignName;
$imageFilePath  = __DIR__ . '/' . $campaignName . '/images/' . $imageName;

// Render Latte HTML
if ($campaignName && file_exists($campaignPath) && !$imageName) {
    $params = require $campaignPath . '/campaign-config.php';
    (new Latte\Engine)->render($campaignPath . '/message.latte', $params + [
        'receiver' => $receiver
    ]);
}

// Render image
else if ($campaignName && file_exists($campaignPath) && file_exists($imageFilePath)) {
    // If set receiver track open-rate
    if ($receiver) {
        $metrics = [
            (new \DateTime())->format('d.m.Y H:i:s'),
            $receiver,
            $_SERVER['HTTP_USER_AGENT'],
            $_SERVER['REMOTE_ADDR']
        ];

        $statsDir = $campaignPath . '/stats/';

        if (!file_exists($statsDir)) {
            mkdir($statsDir, 0777, TRUE);
        }

        $fp = fopen($statsDir . 'open-rate.txt', 'a+');
        fwrite($fp, implode(' | ', $metrics));
        fclose($fp);
    }

    $fp = fopen($imageFilePath, 'rb');
    header('Content-Type: image/png');
    header('Content-Length: ' . filesize($imageFilePath));
    fpassthru($fp);
}
exit;