#!/bin/sh

if [ -z $(grep "extension=/home/vagrant/swoole-src/modules/swoole.so" "/etc/php/7.1/cli/php.ini") ]; then
    git clone https://github.com/swoole/swoole-src.git
    cd swoole-src
    git checkout 2.x-lts
    phpize
    ./configure
    make -j
    make install
    echo "extension=/home/vagrant/swoole-src/modules/swoole.so" | sudo tee -a /etc/php/7.1/cli/php.ini
fi