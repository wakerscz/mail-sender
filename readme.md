# PHP Mail campaign sender

## How to setup your campaign
1. Edit `./sender/config.php` to setup your SMTP & current campaign.
1. Duplicate `./sender/pf-2020/` and edit files in your folder.
1. Upload campaign folder into your web server.

## How to send all emails

### With PHP CLI
1. Run `php ./sender/index.php`.

### With Docker
1. Install [docker for desktop](https://www.docker.com/products/docker-desktop).
1. Run `./sc.sh` to send all messages.

## Open Rate stats
- You can monitor the open rate stats after running the campaign.
- Just open remote folder `./campaign/*/stats/open-stats.txt'.
- Be careful with GDPR (mail addresses are visible in your public folder).