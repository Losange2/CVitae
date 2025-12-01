# CVitae

Prenez une nouvelle vm :
- sudo apt update && sudo apt upgrade -y
- sudo apt install ca-certificates curl gnupg lsb-release -y
- sudo install -m 0755 -d /etc/apt/keyrings
- curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
- sudo chmod a+r /etc/apt/keyrings/docker.gpg
- echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/debian \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
Pour vérifier que ça marche bien :
- cat /etc/apt/sources.list.d/docker.list
Si cela renvoie ce qui est mis dans le echo c'est que cela marche
- sudo apt update
- sudo apt install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y
- sudo systemctl start docker
- sudo systemctl enable docker
- sudo docker run hello-world
- sudo usermod -aG docker $USER
- newgrp docker
- docker ps
 Si cela renvoie :
CONTAINER ID  IMAGE  COMMAND  CREATED  STATUS  PORTS  NAMES
c'est que cela marche

Pour installer symphony dans docker :

- mkdir ~/symfony-server
- cd ~/symfony-server
- nano docker-compose.yml

Mettez cela dans le nano :
```
services:
  php:
    image: php:8.2-fpm
    container_name: symfony-php
    working_dir: /var/www/html
    volumes:
      - ./my_project:/var/www/html
    depends_on:
      - db

  nginx:
    image: nginx:latest
    container_name: symfony-nginx
    ports:
      - "8080:80"
    volumes:
      - ./my_project:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php

  db:
    image: mysql:8.0
    container_name: symfony-db
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: symfony
      MYSQL_USER: symfony
      MYSQL_PASSWORD: symfony
    ports:
      - "3307:3306"

 ```
- nano nginx.conf
Copiez également ça :
```
server {
    listen 80;

    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass php:9000;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}

```
- ls -l
Si cela vous retourne ça cela marche (le jour et l'heure changeront en fonction de quand vous le créerez :
total 8
-rw-r--r-- 1 operateur docker 623  1 déc.  11:04 docker-compose.yml
-rw-r--r-- 1 operateur docker 428  1 déc.  11:13 nginx.conf
- sudo docker exec -it symfony-php bash
- apt update && apt install -y git unzip curl
- curl -sS https://getcomposer.org/installer | php
- mv composer.phar /usr/local/bin/composer
- composer create-project symfony/website-skeleton my_project
