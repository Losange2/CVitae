# 📄 CVitae
# ***Sur une première VM  ***
## Créer un fichier et insérer le contenue 
### Vous pouvez modifiez les valeurs de DB_NAME, DB_USER et DB_PASS pour changer le nom de la base de données et le nom et mot de passe de l'utilisateur
```
#!/bin/bash
# =============================================================================
#  CVitae - VM1 : Installation de MariaDB
# =============================================================================

set -e

# --- Couleurs ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

log()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
ok()     { echo -e "${GREEN}[OK]${NC}    $1"; }
warn()   { echo -e "${YELLOW}[WARN]${NC}  $1"; }
error()  { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# =============================================================================
#  CONFIGURATION — modifiez ces valeurs avant de lancer le script
# =============================================================================
DB_NAME="CVitae"
DB_USER="monuser"
DB_PASS="monpassword"
# =============================================================================

echo ""
echo "============================================="
echo "   CVitae — Installation MariaDB (VM1)"
echo "============================================="
echo ""

# --- Vérification root ---
if [[ $EUID -ne 0 ]]; then
    error "Ce script doit être exécuté en tant que root (sudo)."
fi

# --- Mise à jour du système ---
log "Mise à jour des paquets..."
apt update -y && apt upgrade -y
ok "Système à jour."

# --- Installation de MariaDB ---
log "Installation de MariaDB..."
apt install -y mariadb-server
ok "MariaDB installé."

# --- Démarrage et activation ---
log "Démarrage et activation de MariaDB..."
systemctl start mariadb
systemctl enable mariadb
ok "MariaDB démarré et activé au boot."

# --- Création de la base et de l'utilisateur ---
log "Création de la base de données '${DB_NAME}' et de l'utilisateur '${DB_USER}'..."

mariadb -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`;
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'%';
FLUSH PRIVILEGES;
SQL

ok "Base de données et utilisateur créés."

# --- Autoriser les connexions distantes ---
log "Configuration de MariaDB pour accepter les connexions distantes..."
CONF_FILE=""

for f in /etc/mysql/mariadb.conf.d/50-server.cnf /etc/mysql/my.cnf /etc/mysql/mariadb.cnf; do
    if [[ -f "$f" ]]; then
        CONF_FILE="$f"
        break
    fi
done

if [[ -z "$CONF_FILE" ]]; then
    warn "Fichier de configuration MariaDB introuvable. Vérifiez manuellement bind-address."
else
    sed -i 's/^bind-address\s*=.*/bind-address = 0.0.0.0/' "$CONF_FILE"
    ok "bind-address mis à 0.0.0.0 dans $CONF_FILE."
fi

# --- Redémarrage ---
log "Redémarrage de MariaDB..."
systemctl restart mariadb
ok "MariaDB redémarré."

# --- Récapitulatif ---
echo ""
echo "============================================="
echo -e "  ${GREEN}Installation terminée avec succès !${NC}"
echo "============================================="
echo ""
echo "  Base de données : ${DB_NAME}"
echo "  Utilisateur     : ${DB_USER}"
echo "  Mot de passe    : ${DB_PASS}"
echo ""
IP=$(hostname -I | awk '{print $1}')
echo "  Adresse IP de cette VM : ${IP}"
echo "  → Notez cette IP pour la configurer dans le .env.local de la VM2."
echo ""
```
# Puis faites ça sur le fichier pour mettre les droits et le lancer
```
sudo chmod +x nom_du_script.sh
sudo ./nom_du_script.sh
```

# ***Sur une deuxième VM***
## Script d'Installation de Docker

### Créer un fichier et mettez le contenu 
# N'oubliez pas de modifiez le DB_USER/PASS/NAME/HOST (pour l'IP faites un ip a sur la VM qui contient votre base de données pour l'obtenir)
```bash
#!/bin/bash
# =============================================================================
#  CVitae - VM2 : Installation Docker + Symfony + CVitae
# =============================================================================

set -e

# --- Couleurs ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

log()   { echo -e "${CYAN}[INFO]${NC}  $1"; }
ok()    { echo -e "${GREEN}[OK]${NC}    $1"; }
warn()  { echo -e "${YELLOW}[WARN]${NC}  $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# =============================================================================
#  CONFIGURATION — modifiez ces valeurs avant de lancer le script
# =============================================================================
DB_USER="application"
DB_PASS="application"
DB_NAME="CVitae"
DB_HOST="IP"        # ← Remplacez par l'IP réelle de votre VM1
WORK_DIR="$HOME/symfony-server"
# =============================================================================

echo ""
echo "============================================="
echo "   CVitae — Installation Docker + Symfony"
echo "                   (VM2)"
echo "============================================="
echo ""

# --- Vérification root ---
if [[ $EUID -ne 0 ]]; then
    error "Ce script doit être exécuté en tant que root (sudo)."
fi

# --- Validation de l'IP ---
if [[ "$DB_HOST" == "IP_DE_LA_VM1" ]]; then
    error "Veuillez renseigner l'adresse IP de la VM1 dans la variable DB_HOST avant de lancer ce script."
fi

# =============================================================================
#  1. Mise à jour du système
# =============================================================================
log "Mise à jour du système..."
apt update -y && apt upgrade -y
apt install -y ca-certificates curl gnupg lsb-release
ok "Système à jour."

# =============================================================================
#  2. Installation de Docker
# =============================================================================
log "Configuration des clés GPG Docker..."
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg \
    | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg
ok "Clés GPG configurées."

log "Ajout du dépôt Docker..."
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/debian \
  $(lsb_release -cs) stable" \
  | tee /etc/apt/sources.list.d/docker.list > /dev/null
ok "Dépôt Docker ajouté."

log "Installation de Docker..."
apt update -y
apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
systemctl start docker
systemctl enable docker
ok "Docker installé et démarré."

# Ajout de l'utilisateur courant au groupe docker
REAL_USER="${SUDO_USER:-$USER}"
usermod -aG docker "$REAL_USER" 2>/dev/null || true
ok "Utilisateur '${REAL_USER}' ajouté au groupe docker."

# --- Test Docker ---
log "Test de l'installation Docker..."
docker run --rm hello-world > /dev/null 2>&1 && ok "Docker fonctionne correctement." \
    || warn "Le test hello-world a échoué. Vérifiez Docker manuellement."

# =============================================================================
#  3. Préparation du dossier de travail
# =============================================================================
log "Création du dossier de travail : ${WORK_DIR}..."
mkdir -p "$WORK_DIR"
cd "$WORK_DIR"
ok "Dossier créé."

# =============================================================================
#  4. Génération des fichiers de configuration
# =============================================================================
log "Génération de docker-compose.yml..."
cat > docker-compose.yml << 'EOF'
version: "3.8"

services:
  php:
    image: php:8.2-fpm
    container_name: symfony-php
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html

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
EOF
ok "docker-compose.yml créé."

log "Génération de nginx.conf..."
cat > nginx.conf << 'EOF'
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
EOF
ok "nginx.conf créé."

log "Génération du Dockerfile..."
cat > Dockerfile << 'EOF'
FROM php:8.3-cli

RUN apt update && apt install -y \
    git unzip zip libzip-dev \
    && docker-php-ext-install zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
EOF
ok "Dockerfile créé."

# =============================================================================
#  5. Démarrage des conteneurs
# =============================================================================
log "Démarrage des conteneurs Docker..."
docker compose up -d
ok "Conteneurs démarrés."

# --- Petite pause pour laisser les conteneurs s'initialiser ---
log "Attente de l'initialisation des conteneurs (10s)..."
sleep 10

# =============================================================================
#  6. Configuration du projet CVitae dans le conteneur PHP
# =============================================================================
log "Installation des outils dans le conteneur PHP..."
docker exec symfony-php bash -c "apt update && apt install -y git unzip curl"
ok "Outils installés dans le conteneur."

log "Installation de Composer dans le conteneur PHP..."
docker exec symfony-php bash -c \
    "curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer"
ok "Composer installé."

log "Clonage du dépôt CVitae..."
docker exec symfony-php bash -c \
    "cd /var/www/html && git clone https://github.com/Losange2/CVitae"
ok "CVitae cloné."

log "Installation des extensions PHP (zip, pdo, pdo_mysql)..."
docker exec symfony-php bash -c "apt update && apt install -y git unzip zip libzip-dev default-mysql-client \
    && docker-php-ext-install zip pdo pdo_mysql"
ok "Extensions PHP installées."

log "Installation des dépendances Composer (sans exécuter les scripts)..."
docker exec symfony-php bash -c \
    "cd /var/www/html/CVitae && composer install --no-interaction --ignore-platform-reqs --no-scripts"
ok "Dépendances Composer installées."

log "Génération du fichier .env.local..."
docker exec symfony-php bash -c "cat > /var/www/html/CVitae/.env.local << EOF
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL=\"mysql://${DB_USER}:${DB_PASS}@${DB_HOST}:3306/${DB_NAME}?serverVersion=10.6\"
EOF"
ok ".env.local configuré."

log "Nettoyage du cache Symfony..."
docker exec symfony-php bash -c "cd /var/www/html/CVitae && php bin/console cache:clear"
ok "Cache nettoyé."

# =============================================================================
#  7. Redémarrage et vérification de pdo_mysql
# =============================================================================
log "Redémarrage du conteneur PHP..."
docker compose restart php
sleep 5

log "Vérification de l'extension pdo_mysql..."
PDO_CHECK=$(docker exec symfony-php php -m | grep pdo_mysql || true)
if [[ "$PDO_CHECK" == "pdo_mysql" ]]; then
    ok "Extension pdo_mysql détectée."
else
    warn "pdo_mysql non détectée. Vérifiez manuellement avec : docker exec symfony-php php -m | grep pdo"
fi

# =============================================================================
#  8. Migration de la base de données et chargement des fixtures
# =============================================================================
log "Création du schéma de base de données..."
docker exec symfony-php bash -c \
    "cd /var/www/html/CVitae && php bin/console doctrine:schema:update --force"
ok "Schéma créé."

log "Chargement des données de test (fixtures)..."
docker exec symfony-php bash -c \
    "cd /var/www/html/CVitae && php bin/console doctrine:fixtures:load --no-interaction"
ok "Fixtures chargées."

log "Installation des assets (importmap)..."
docker exec symfony-php bash -c \
    "cd /var/www/html/CVitae && php bin/console importmap:install"
ok "Assets installés."

# =============================================================================
#  Récapitulatif final
# =============================================================================
IP=$(hostname -I | awk '{print $1}')
echo ""
echo "============================================="
echo -e "  ${GREEN}Installation terminée avec succès !${NC}"
echo "============================================="
echo ""
echo "  Dossier de travail : ${WORK_DIR}"
echo "  VM1 (MariaDB)      : ${DB_HOST}"
echo "  Base de données    : ${DB_NAME}"
echo ""
echo "  → Accédez à l'application via :"
echo -e "    ${CYAN}http://${IP}:8080${NC}"
echo ""
echo "  Commandes utiles :"
echo "    docker compose ps              — état des conteneurs"
echo "    docker compose logs -f         — logs en temps réel"
echo "    docker compose restart php     — redémarrer PHP"
echo ""
```

# Puis faites ça sur le fichier pour mettre les droits et le lancer
```
sudo chmod +x nom_du_script.sh
sudo ./nom_du_script.sh
```

'application (si des problèmes vous arrive faites moi un issues**
