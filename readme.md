# PHP Mail - campaign sender
The easiest way how to send e-mail campaign with **Async PHP** and **tracking open-rate**.

## How to setup your campaign
1. Duplicate `./sender/config.example.php`, rename to `config.php` and configure it.
1. Duplicate `./sender/vzorova-kampan/` and edit files in this folder.
1. Upload your campaign folder to web server.

## How to send tons of emails

### With PHP CLI
1. Run `php ./sender/send.php` to send all messages.

### With Docker
1. Install [docker for desktop](https://www.docker.com/products/docker-desktop).
1. Run `./send.sh` to send all messages.

## How to Open Rate monitoring
- You can monitor the open rate stats after running the campaign.
- Just open remote folder `./campaign/*/stats/open-rate.txt`.
- Be careful with GDPR (mail addresses are visible in your public folder).