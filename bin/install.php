#!/usr/bin/env php
<?php

/**
 * Installeur amélioré de Memcached + PHP-Memcached
 * Usage : sudo php install.php [--port=11211] [--memory=256] [--object-size=16]
 */

class InstallateurMemcached {
    private $port;
    private $memoire;
    private $tailleObjet;
    private $os;

    public function __construct($options) {
        $this->port = $options['port'] ?? 11211;
        $this->memoire = $options['memory'] ?? 256;
        $this->tailleObjet = $options['object-size'] ?? 16;
    }

    public function lancer() {
        $this->verifierRoot();
        $this->detecterOS();
        $this->installerPaquets();
        $this->verifierNetcat();
        $this->configurerMemcached();
        $this->demarrerService();
        $this->verifierInstallation();
    }
    public function getPort(){
        return $this->port;
    }
    private function verifierRoot() {
        if (posix_getuid() !== 0) {
            die("\033[31mERREUR: Lancez ce script avec sudo\033[0m\n");
        }
    }
    

    private function detecterOS() {
        echo "Détection du système... ";

        if (file_exists('/etc/debian_version')) {
            echo "Debian/Ubuntu\n";
            $this->os = 'debian';
        } elseif (file_exists('/etc/redhat-release')) {
            echo "RHEL/CentOS\n";
            $this->os = 'rhel';
        } else {
            die("\033[31mSystème non supporté\033[0m\n");
        }
    }


   private function installerPaquets() {
        echo "Vérification des paquets...\n";

        if ($this->os === 'debian') {
            $this->installerPaquetsDebian();
        } else {
            $this->installerPaquetsRHEL();
        }

        $this->installerExtensionPHP();
    }

private function paquetInstalle($nom, $debian = true) {
    $cmd = $debian 
        ? "dpkg -s $nom 2>/dev/null | grep -q '^Status: install' && echo 1 || echo 0" 
        : "rpm -q $nom >/dev/null 2>&1 && echo 1 || echo 0";
    return trim(shell_exec($cmd)) === '1';
}


    private function verifierNetcat() {
        $check = shell_exec('command -v nc');
        if (empty($check)) {
            echo "Netcat (nc) n'est pas installé. Installation...\n";
            if ($this->os === 'debian') {
                shell_exec('apt-get install -y netcat');
            } else {
                shell_exec('yum install -y nc');
            }
        }
    }

    private function configurerMemcached() {
        echo "Configuration de Memcached...\n";
        $fichier = '/etc/memcached.conf';

        // Sauvegarde de l’ancienne configuration
        if (file_exists($fichier)) {
            copy($fichier, $fichier . '.backup-' . date('YmdHis'));
        }

        $config = sprintf(
            "-l 127.0.0.1\n-p %d\n-m %d\n-I %dm\n",
            $this->port,
            $this->memoire,
            $this->tailleObjet
        );

        file_put_contents($fichier, $config);
    }

    private function demarrerService() {
        echo "Démarrage du service Memcached...\n";
        shell_exec('systemctl restart memcached');
        shell_exec('systemctl enable memcached');

        if (!$this->serviceActif()) {
            die("\033[31mÉchec du démarrage du service Memcached.\033[0m\n");
        }
    }

    private function serviceActif() {
        $statut = shell_exec('systemctl is-active memcached 2>&1');
        return trim($statut) === 'active';
    }

    private function verifierInstallation() {
        echo "\n\033[32mVérification de l'installation...\033[0m\n";

        $m = new Memcached();
        if (!$m->addServer('localhost', $this->port)) {
            die("\033[31mImpossible d'ajouter le serveur Memcached\033[0m\n");
        }
        $m->set('test', 'OK');

        echo "Test PHP: " . ($m->get('test') === 'OK' ? "\033[32mSUCCÈS\033[0m" : "\033[31mÉCHEC\033[0m") . "\n";

        echo "Test serveur Memcached (netcat): ";
        system("echo stats | nc -N localhost " . $this->port . " | grep uptime");
    }
    private function detecterPHP() {
        $this->phpVersion = shell_exec('php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;"');
        echo "Version PHP détectée : {$this->phpVersion}\n";
    }

    

    private function installerPaquetsDebian() {
        $paquets = ['memcached', 'libmemcached-tools'];
        shell_exec('apt-get update -qq');

        foreach ($paquets as $pkg) {
            if (!$this->paquetInstalle($pkg)) {
                echo "Installation de $pkg...\n";
                shell_exec("apt-get install -y $pkg");
            } else {
                echo "$pkg déjà installé\n";
            }
        }
    }

    private function installerPaquetsRHEL() {
        $paquets = ['memcached'];
        shell_exec('yum install -y epel-release');

        foreach ($paquets as $pkg) {
            if (!$this->paquetInstalle($pkg, false)) {
                echo "Installation de $pkg...\n";
                shell_exec("yum install -y $pkg");
            } else {
                echo "$pkg déjà installé\n";
            }
        }
    }

    private function installerExtensionPHP() {
        echo "Installation de l'extension PHP Memcached...\n";

        if ($this->os === 'debian') {
            $pkgName = 'php-memcached';
            if ((int)$this->phpVersion >= 80) {
                $pkgName = 'php8.0-memcached'; // Adaptez selon votre version
            }
            
            if (!$this->paquetInstalle($pkgName)) {
                shell_exec("apt-get install -y $pkgName");
            }
        } else {
            shell_exec('yum install -y php-pecl-memcached');
        }

        // Redémarrer le service web
        $this->redemarrerServiceWeb();
    }

    private function redemarrerServiceWeb() {
        echo "Redémarrage du service web...\n";
        if (shell_exec('systemctl is-active apache2 2>/dev/null')) {
            shell_exec('systemctl restart apache2');
        } elseif (shell_exec('systemctl is-active httpd 2>/dev/null')) {
            shell_exec('systemctl restart httpd');
        } elseif (shell_exec('systemctl is-active php-fpm 2>/dev/null')) {
            shell_exec('systemctl restart php-fpm');
        } else {
            echo "Aucun service web détecté à redémarrer\n";
        }
    }
}

// === Lancement du script ===
parse_str(implode('&', array_slice($argv, 1)), $options);

echo "\033[34m=== INSTALLATEUR MEMCACHED  ===\033[0m\n";
$installateur = new InstallateurMemcached($options);
$installateur->lancer();

echo "\n\033[32mInstallation terminée!\033[0m\n";
echo "Memcached écoute sur 127.0.0.1:" . $installateur->getPort() . "\n";
?>