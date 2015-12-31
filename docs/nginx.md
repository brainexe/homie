# /etc/nginx/sites-enabled/homie.conf

```server {
    server_name homie.example.de;
    root   /www/homie/web/;

    index  index.html;

    # serve pre-generated .gz file
    gzip_static  on;

    location / {
        try_files $uri $uri/ /index.php?$uri&$args;
    }

    # proxy socket server from local port 8081 through /socket/
    location /socket/ {
        proxy_pass http://localhost:8081;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }

    location ~ \.php$ {
        try_files $uri =404;
        include /etc/nginx/fastcgi_params;
        fastcgi_pass    unix:/tmp/php.socket;
        fastcgi_index   index.php;
        fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

