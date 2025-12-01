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
      MYSQL_ROOT_PASSWORD: root // a changer une fois votre db creer
      MYSQL_DATABASE: symfony
      MYSQL_USER: symfony
      MYSQL_PASSWORD: symfony
    ports:
      - "3306:3306"
 ```
