# CVitae

Pour installer docker :
 
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
- sudo apt update
- sudo apt install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y
- sudo systemctl start docker
- sudo systemctl enable docker
- sudo docker run hello-world
- sudo usermod -aG docker $USER
- newgrp docker
- docker ps

Cela doit montrer si cela a marché : 
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES

Pour installer symphony dedans :
 
- mkdir ~/symfony-server
- cd ~/symfony-server
- nano docker-compose.yml
- 
version: "3.8"
 
services:
  php:
    image: php:8.2-fpm
    container_name: symfony-php
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
    depends_on:
      - db
 
  nginx:
    image: nginx:latest
    container_name: symfony-nginx
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
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
      - "3306:3306"
 
- nano nginx.conf
-
server {
    listen 80;
    server_name localhost;

    root /var/www/html/CVitae/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass symfony-php:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $document_root;
        internal;
    }

    location ~ /\. {
        deny all;
    }
}

- nano Dockerfile
-
FROM php:8.3-cli

# Dépendances système
RUN apt update && apt install -y \
    git unzip zip libzip-dev \
    && docker-php-ext-install zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

- ls -l
Cela renvoie (la date va dépendre de quand vous l'avez creez): 
operateur@debian:~/symfony-server$ ls -l
total 16
-rw-r--r-- 1 operateur docker  477 15 déc.  10:11 docker-compose.yml
-rw-r--r-- 1 operateur docker  247 15 déc.  10:32 Dockerfile
-rw-r--r-- 1 operateur docker  502 15 déc.  10:13 nginx.conf

- docker compose up -d
 

- sudo docker exec -it symfony-php bash
- apt update && apt install -y git unzip curl
- curl -sS https://getcomposer.org/installer | php
- mv composer.phar /usr/local/bin/composer
- git clone https://github.com/Losange2/CVitae
- docker run -it --rm \
  -v $(pwd)/CVitae:/var/www/html \
  cvitae bash
- cd CVitae
- apt update && apt install -y \
    git unzip zip libzip-dev \
    && docker-php-ext-install zip
- composer install --no-interaction --ignore-platform-reqs
- cat > .env.local << 'EOF'
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL="mysql://testdocker:testdocker@192.168.56.222:3306/cvitae?serverVersion=8.0"
EOF
- php bin/console cache:clear
- apt-get update
apt-get install -y default-mysql-client
docker-php-ext-install pdo pdo_mysql
- exit
docker compose restart php
- docker compose exec php bash
php -m | grep pdo

et vous devriez voir :
pdo_mysql
