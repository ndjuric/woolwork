## MacOS setup

~/works $ brew install autoconf  
~/works $ git clone https://github.com/swoole/swoole-src.git  
~/works $ cd ~/swoole-src  
  
~/works/swoole-src $ phpize  
~/works/swoole-src $ ./configure  
~/works/swoole-src $ make -j  

We should now have ~/swoole-src/modules/swoole.so

~/works/swoole-src $ cd /etc  
/etc $ sudo cp php.ini.default php.ini  
/etc $ echo "extension=/Users/$USER/works/swoole-src/modules/swoole.so" | sudo tee -a /etc/php.ini  
/etc $ php -i | grep swoole




