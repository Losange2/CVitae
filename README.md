# 📄 CVitae
# ***Sur une première VM  (recommandé mais le faire sur une seule VM est possible)***
# 1. Installer MariaDB
```
sudo apt update
sudo apt install mariadb-server -y
```

# 2. Démarrer le service MariaDB
```
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

# 3. Se connecter à MariaDB en tant que root
```
sudo mariadb
```
# 4. Dans le shell MariaDB, créer la base et l'utilisateur
```
CREATE DATABASE CVitae;

-- Remplacer 'monuser' et 'monpassword' par ton choix
CREATE USER 'monuser'@'*' IDENTIFIED BY 'monpassword';

GRANT ALL PRIVILEGES ON CVitae.* TO 'monuser'@'%';

FLUSH PRIVILEGES;

EXIT;
```
# ***Sur une deuxième VM***
## 🐳 Installation de Docker

### Mise à jour du système et installation des dépendances
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install ca-certificates curl gnupg lsb-release -y
```

### Configuration des clés GPG
```bash
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg
```

### Ajout du dépôt Docker
```bash
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/debian \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```

### ✅ Vérification que ça marche bien
```bash
cat /etc/apt/sources.list.d/docker.list
```

### Installation de Docker
```bash
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y
sudo systemctl start docker
sudo systemctl enable docker
```

### Test de l'installation
```bash
sudo docker run hello-world
sudo usermod -aG docker $USER
newgrp docker
docker ps
```

**Cela doit montrer si cela a marché :**
```
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
```

---

## 🎵 Installation de Symfony

### Création du dossier de travail
```bash
mkdir ~/symfony-server
cd ~/symfony-server
```

### Configuration Docker Compose
```bash
nano docker-compose.yml
```

**Contenu du fichier `docker-compose.yml` :**
```yaml
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
```

### Configuration Nginx
```bash
nano nginx.conf
```

**Contenu du fichier `nginx.conf` :**
```nginx
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
```

### Configuration du Dockerfile
```bash
nano Dockerfile
```

**Contenu du fichier `Dockerfile` :**
```dockerfile
FROM php:8.3-cli

# Dépendances système
RUN apt update && apt install -y \
    git unzip zip libzip-dev \
    && docker-php-ext-install zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
```

### Vérification des fichiers créés
```bash
ls -l
```

**Cela renvoie (la date va dépendre de quand vous l'avez créé) :**
```
operateur@debian:~/symfony-server$ ls -l
total 12
-rw-r--r-- 1 operateur docker  477 15 déc.  10:11 docker-compose.yml
-rw-r--r-- 1 operateur docker  247 15 déc.  10:32 Dockerfile
-rw-r--r-- 1 operateur docker  502 15 déc.  10:13 nginx.conf
```

### Démarrage des conteneurs
```bash
docker compose up -d
```

---

## ⚙️ Configuration du projet CVitae

### Installation des dépendances dans le conteneur PHP
```bash
sudo docker exec -it symfony-php bash
```
```bash
apt update && apt install -y git unzip curl
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
git clone https://github.com/Losange2/CVitae
```

### Configuration de l'environnement
```bash
cd CVitae
apt update && apt install -y \
    git unzip zip libzip-dev \
    && docker-php-ext-install zip
composer install --no-interaction --ignore-platform-reqs
```

### Création du fichier de configuration `.env.local`
```bash
cat > .env.local << 'EOF'
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL="mysql://monuser:monmotdepasse@ipdelavmserveur:3306/CVitae?serverVersion=10.6"

EOF
```

### Nettoyage du cache
```bash
php bin/console cache:clear
```

### Installation de MySQL et PDO
```bash
apt-get update
apt-get install -y default-mysql-client
docker-php-ext-install pdo pdo_mysql
exit
```

### Redémarrage et vérification
```bash
docker compose restart php
docker compose exec php bash
php -m | grep pdo
```

**Vous devriez voir :**
```
pdo_mysql
```

### Envoie des données test vers la base de données

```
php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load --no-interaction

cd /var/www/html/CVitae
php bin/console importmap:install

```
**Vous pouvez vous connectez a l'ip de votre VM et cela devrait lancer l'application (si des problèmes vous arrive faites moi un issues**
