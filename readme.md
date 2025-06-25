### Instructions d'installation et de lancement (linux)

#### 1. **Serveur Web**

- Installe un outil comme XAMPP.
- Avec XAMPP : place le projet dans le dossier `htdocs`.

#### 2. **Dépendances Composer**

- Installe [Composer](https://getcomposer.org/download/).
- Pour être sur d'avoir les dépendances :
  ```bash
  composer install
  ```
  _→ Exécute cette commande à la racine du projet (où se trouve `composer.json`)._

#### 3. **Base de données**

- Modifie les identifiants dans :
  ```php
  // src/App/Auth/credentials.php
  $dsn = 'mysql:host=localhost;dbname=nomdb';
  $login = 'test';
  $mdp = 'laflemme';
  ```
- Crée la base de données via :
  - **XAMPP** : Importe le fichier SQL via phpMyAdmin (attention aux conflits si la BDD existe déjà).

#### 4. **Lancer l'application**

- Accède à l'URL dans ton navigateur :
  ```
  http://localhost/lenomdudossier
  ```

#### 5. **WebSocket**

- 'chmod +x start.php'
- Lance le serveur WebSocket se trouvant dans /bin/bash pour pouvoir envoyer les messages avec :
  ```bash
  sudo php start.php  
  ```
  

#### 6. **Memcached**

- Le server web et websocket partage les données de session par memcached
  - ⚠️ Important le scipt change des configuration pour mettre un service Memcached qui se redémarre seul
  - Il y a un script `install.sh` qui permet d'installer et configurer memcached et l'extension php-memcached
  - Faire `chmod +x install.sh` pour ensuite le lancer `sudo ./install.sh`
  - Si Xampp :
   - installer avec  `/opt/lampp/bin/pecl install memcached`
   - mettre `extension=memcached.so` dans son php.ini

### Developpement

- **Autoload (PSR-4)** :

  ```php
  // Exemple de classe avec namespace
  namespace App\Controller;
  class MaClasse { ... }
  ```

  📌 _N'oublie pas le `namespace` pour l'autoloader._

- **Structure (PHP-PDS)** : Suis le standard [PHP Package Skeleton](https://github.com/php-pds/skeleton).
- **MVC** : Framework maison basé sur le pattern Modèle-Vue-Contrôleur.
- **WebSocket** : Documentation utile : [Ratchet Push](http://socketo.me/docs/push).
- **Logs** : Consulte les logs PHP (`var/log/`) ou ceux de ton serveur en cas d'erreur.
