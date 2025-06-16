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
    $this->detecterPHP(); 
    $this->installerPaquets();
    $this->verifierNetcat();
    $this->configurerMemcached();
    $this->demarrerService();
    $this->verifierExtensionPHP(); 
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
    private function detecterPHP() {
    $this->phpVersion = shell_exec('php -r "echo PHP_MAJOR_VERSION;"');
    echo "Version PHP détectée : {$this->phpVersion}\n";
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

    if (file_exists($fichier)) {
        copy($fichier, $fichier . '.backup-' . date('YmdHis'));
    }

    $config = sprintf(
        "-l 127.0.0.1\n-p %d\n-m %d\n-I %dm\n-vv\n",  // Ajout de -vv pour les logs détaillés
        $this->port,
        $this->memoire,
        $this->tailleObjet
    );

    file_put_contents($fichier, $config);
    
    // S'assurer que les permissions sont correctes
    chmod($fichier, 0644);
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

    // Test de base de connexion
    $this->verifierConnexionMemcached();

    // Test plus approfondi
    $this->effectuerTestComplet();
}

private function verifierConnexionMemcached() {
    echo "Vérification de la connexion Memcached...\n";
    
    $output = shell_exec("nc -zv localhost ".$this->port." 2>&1");
    if (strpos($output, 'succeeded') !== false) {
        echo "\033[32mConnexion TCP fonctionnelle\033[0m\n";
    } else {
        echo "\033[31mÉchec de connexion TCP: $output\033[0m\n";
        die("Veuillez vérifier que Memcached est bien démarré");
    }
}

private function effectuerTestComplet() {
    $m = new Memcached();
    $m->setOption(Memcached::OPT_CONNECT_TIMEOUT, 1000);
    $m->setOption(Memcached::OPT_RETRY_TIMEOUT, 1000);
    
    if (!$m->addServer('localhost', $this->port)) {
        die("\033[31mÉchec d'ajout du serveur: ".$m->getResultMessage()."\033[0m\n");
    }

    $m->set('test_key', 'test_value', 10);
    $result = $m->get('test_key');

    if ($result === 'test_value') {
        echo "\033[32mTest PHP réussi\033[0m\n";
    } else {
        echo "\033[31mÉchec du test PHP: ".$m->getResultMessage()."\033[0m\n";
    }

    // Test stats avec solution de repli
    echo "Statistiques serveur: ";
    $this->obtenirStatsServeur();
}

private function obtenirStatsServeur() {
    $commands = [
        "nc -w 1 localhost ".$this->port." 2>/dev/null <<< stats",
        "telnet localhost ".$this->port." 2>/dev/null <<< stats",
        "echo stats | nc -w 1 localhost ".$this->port
    ];

    foreach ($commands as $cmd) {
        $output = shell_exec($cmd);
        if (!empty($output) && strpos($output, 'uptime') !== false) {
            echo "\033[32mOK\033[0m (uptime: ".trim(explode(' ', $output)[2]."s)\n");
            return;
        }
    }
    
    echo "\033[31mÉchec\033[0m (impossible d'obtenir les stats)\n";
}
    private function verifierExtensionPHP() {
    if (!extension_loaded('memcached')) {
        die("\033[31mERREUR: L'extension PHP Memcached n'est pas chargée!\n"
          . "Essayez: sudo phpenmod memcached && sudo systemctl restart apache2/php-fpm\033[0m\n");
    }
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
        if ($this->phpVersion >= 8) {
            $pkgName = "php{$this->phpVersion}-memcached";
        }
        
        if (!$this->paquetInstalle($pkgName)) {
            shell_exec("apt-get install -y $pkgName");
        }
    } else {
        shell_exec('yum install -y php-pecl-memcached');
    }

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