#!/bin/bash


if [ ! -z "$WWW" ]; then
    crontab -l | { cat; echo "##===== WWW CRON ======##"; cat /home/php/www.cron; } | crontab -
fi

if [ ! -z "$API" ]; then
    crontab -l | { cat; echo "##===== API CRON ======##"; cat /home/php/api.cron; } | crontab -
fi

if [ ! -z "$ADMIN" ]; then
    crontab -l | { cat; echo "##===== ADMIN CRON ======##"; cat /home/php/admin.cron; } | crontab -
fi

if [ ! -z "$PARTNER" ]; then
    crontab -l | { cat; echo "##===== PARTNER CRON ======##"; cat /home/php/partner.cron; } | crontab -
fi

if [ ! -z "$MEMELUX" ]; then
    crontab -l | { cat; echo "##===== MEMELUX CRON ======##"; cat /home/php/memelux.cron; } | crontab -
fi
