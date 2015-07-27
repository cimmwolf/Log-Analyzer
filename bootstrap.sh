#!/usr/bin/env bash

### fix phpstorm ssh-bug
KEXALGORITHM='KexAlgorithms curve25519-sha256@libssh.org,ecdh-sha2-nistp256,ecdh-sha2-nistp384,ecdh-sha2-nistp521,diffie-hellman-group-exchange-sha256,diffie-hellman-group14-sha1,diffie-hellman-group-exchange-sha1,diffie-hellman-group1-sha1'
ISSET_KEXALGORITHM=`grep -c "$KEXALGORITHM" /etc/ssh/sshd_config`
if [ "$ISSET_KEXALGORITHM" -eq 0 ]; then
    echo "$KEXALGORITHM" >> /etc/ssh/sshd_config
    sudo service ssh restart
fi

mkdir -p /data/nginx/cache
sed -i '/http {/a proxy_cache_path /data/nginx/cache levels=1:2 keys_zone=thumbs:10m inactive=24h max_size=5G;' /etc/nginx/nginx.conf

if [ -f /vagrant/nginx ]; then
    cp -f /vagrant/nginx /etc/nginx/sites-enabled/default
fi
service nginx restart