<?php
/**
 * Copyright (c) 2019 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$receiver = isset($_GET['receiver']) ? $_GET['receiver'] : NULL;
$imageName = isset($_GET['image']) ? $_GET['image'] : NULL;
$imagePath = 'images/' . $imageName;

if (!$imageName)
{
    $params = require __DIR__ . '/campaign-config.php';

    (new Latte\Engine)
        ->render(__DIR__ . '/message.latte', $params + [
                'receiver' => $receiver
            ]
        );
}
else if (file_exists(__DIR__ . '/' . $imagePath))
{
    if ($receiver)
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = (new \DateTime())->format('d.m.Y H:i:s');

        $dir = __DIR__ . '/stats/';

        if (!file_exists($dir)) {
            mkdir($dir, 0777, TRUE);
        }

        $fp = fopen($dir . 'open-stats.txt', 'a+');
        fwrite($fp, $time .' | ' . $receiver . ' | ' . $ip  . ' | ' . $user_agent . PHP_EOL);
        fclose($fp);
    }

    $fp = fopen($imagePath, 'rb');
    header("Content-Type: image/png");
    header("Content-Length: " . filesize($imagePath));
    fpassthru($fp);
    exit;
}
