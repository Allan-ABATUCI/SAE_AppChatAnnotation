#!/bin/bash

# Mise à jour des paquets et installation des dépendances
sudo apt update
sudo apt install -y php php-memcached memcached

# Création du fichier de service systemd
sudo tee /etc/systemd/system/memcached.service > /dev/null <<EOF
[Unit]
Description=Memcached
After=network.target

[Service]
User=memcache
ExecStart=/usr/bin/memcached -m 64 -l 127.0.0.1
Restart=always

[Install]
WantedBy=multi-user.target
EOF

# Rechargement des daemons systemd
sudo systemctl daemon-reload

# Arrêt du service memcached existant (s'il y en a un)
sudo systemctl stop memcached > /dev/null 2>&1

# Activation et démarrage du nouveau service
sudo systemctl enable memcached
sudo systemctl start memcached

# Configuration des sessions PHP dans Memcached
echo "Configuration des sessions PHP..."
sudo tee /etc/php/*/apache2/conf.d/20-memcached.ini > /dev/null <<'EOL'
; Configuration pour les sessions Memcached
session.save_handler = memcached
session.save_path = "localhost:11211"
session.gc_maxlifetime = 1440
EOL

# Redémarrage d'Apache
sudo systemctl restart apache2

# Vérification de l'installation
echo "Vérification de l'installation..."
echo "Statut du service memcached :"
sudo systemctl status memcached --no-pager

echo ""
echo "Vérification de l'extension PHP memcached :"
php -m | grep memcached

echo ""
echo "Test de connexion à Memcached :"
php -r "\$m = new Memcached(); \$m->addServer('localhost', 11211); echo 'Connexion Memcached: ' . (\$m->getStats() ? 'OK' : 'Échec') . PHP_EOL;"

echo ""
echo "Vérification de la configuration des sessions :"
php -i | grep -E 'session.save_handler|session.save_path'

echo ""
echo "Installation terminée ! Configuration des sessions activée dans Memcached."