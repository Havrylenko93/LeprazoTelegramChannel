Project init:
- ``composer install``
- ``cp .env.example .env``

Here is the only Cron entry you need to add to your server:

```* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1```

Check:

``grep -i cron /var/log/syslog``