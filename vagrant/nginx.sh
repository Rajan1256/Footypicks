#!/usr/bin/env bash
sudo aptitude install -q -y -f mc

sudo apt-add-repository ppa:nginx/stable -y
sudo apt-get -y update
sudo apt-get -y install nginx

#cp fastcgi.conf.dpkg-new fastcgi.conf

sudo rm /etc/nginx/sites-available/default
sudo touch /etc/nginx/sites-available/default

sudo cat >> /etc/nginx/sites-available/default <<'EOF'
server {
    charset utf-8;
    client_max_body_size 128M;

    listen 80;

    server_name together.loc;
    root        /var/www/vergo/public;
    index       index.php;

    access_log  /var/log/nginx/access.log;
    error_log   /var/log/nginx/error.log;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi.conf;
        fastcgi_pass unix:/run/php/php7.1-fpm.sock;
    }

    location ~ /\.(ht|svn|git) {
        deny all;
    }
}
EOF

sudo touch /etc/nginx/fastcgi_params
sudo cat >> /usr/share/nginx/html/info.php <<'EOF'
fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  DOCUMENT_ROOT      $document_root;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;
fastcgi_param  HTTPS              $https if_not_empty;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;

# PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;
EOF

sudo service nginx restart