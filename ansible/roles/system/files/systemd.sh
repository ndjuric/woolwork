#!/bin/sh

sudo cp -f /var/www/engine/config/*.service /etc/systemd/system
sudo systemctl --system daemon-reload
sleep 1
sudo systemctl enable woolworks-worker.service
sleep 1
sudo systemctl enable woolworks-websockd.service
sleep 1
sudo systemctl restart woolworks-worker.service
sleep 1
sudo systemctl restart woolworks-websockd.service