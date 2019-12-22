# PHP Mail campaign sender

## How to setup your campaign
1. Duplicate `./sender/config.example.php`, rename to `config.php` and configure it.
1. Duplicate `./sender/pf-2020/` and edit files in this folder.
1. Upload your campaign folder to web server.

## How to send all emails

### With PHP CLI
1. Run `php ./sender/index.php` to send all messages.

### With Docker
1. Install [docker for desktop](https://www.docker.com/products/docker-desktop).
1. Run `./sc.sh` to send all messages.

## How to Open Rate monitoring
- You can monitor the open rate stats after running the campaign.
- Just open remote folder `./campaign/*/stats/open-stats.txt`.
- Be careful with GDPR (mail addresses are visible in your public folder).