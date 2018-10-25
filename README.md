## Requirements

### MacOS
```bash
~/works $ brew install autoconf  
~/works $ git clone https://github.com/swoole/swoole-src.git  
~/works $ cd ~/swoole-src  
  
~/works/swoole-src $ phpize  
~/works/swoole-src $ ./configure  
~/works/swoole-src $ make -j  
```
We should now have ~/swoole-src/modules/swoole.so
```bash
~/works/swoole-src $ cd /etc  
/etc $ sudo cp php.ini.default php.ini  
/etc $ echo "extension=/Users/$USER/works/swoole-src/modules/swoole.so" | sudo tee -a /etc/php.ini  
/etc $ php -i | grep swoole
```

### Linux
```bash
~/works $ sudo apt-get install php7.1-dev  
~/works $ git clone https://github.com/swoole/swoole-src.git  
~/works $ cd ~/swoole-src  
  
~/works/swoole-src $ phpize  
~/works/swoole-src $ ./configure  
~/works/swoole-src $ make -j  
~/works/swoole-src $ sudo make install
```

### systemd
```bash
$ sudo cp -f /var/www/engine/config/*.service /etc/systemd/system
$ sudo systemctl --system daemon-reload
$ sleep 1
$ sudo systemctl enable woolworks-worker.service
$ sleep 1
$ sudo systemctl restart woolworks-websockd.service
$ sleep 1
```

### rabbitmq users
```bash
$ rabbitmqctl add_user user1 changeme
$ rabbitmqctl set_user_tags user1 administrator,user1
$ rabbitmqctl set_permissions -p / user1 ".*" ".*" ".*"
$ rabbitmqctl add_user user2 changeme
$ rabbitmqctl set_user_tags user2 administrator,user2
$ rabbitmqctl set_permissions -p / user2 ".*" ".*" ".*"
```
