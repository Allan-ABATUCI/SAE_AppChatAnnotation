### Instructions d'installation et de lancement

#### 1. **Serveur Web**

- Installe un serveur web (Apache/Nginx) ou utilise un outil comme XAMPP.
- Tu peux utilisé le server web de développement de PHP:
  ```bash
  php -S localhost:3000 -t ~/racineprojet  # Met le dossier racine du projet où est index.php
  ```
- Avec XAMPP : place le projet dans le dossier `htdocs`.

#### 2. **Dépendances Composer**

- Installe [Composer](https://getcomposer.org/download/).
- Installe les dépendances avec :
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
  - En étant connecté  à **MySQL/PostgreSQL** :
    ```sql
    \i src/App/Utils/sae.sql
    ```
  - **XAMPP** : Importe le fichier SQL via phpMyAdmin (attention aux conflits si la BDD existe déjà).

#### 4. **Lancer l'application**

- Accède à l'URL dans ton navigateur :
  ```
  http://localhost:3000
  ```

#### 5. **WebSocket**

- Lance le serveur WebSocket pour pouvoir envoyer les messages avec :
  ```bash
  php bin/start.php  # ou ./start.php selon l'emplacement
  ```
#### 6. **Memcached**

- Le server web et websocket partage les données de session par memcached
  -
  - Il y a un script `install.sh` qui permet d'installer et configurer memcached et l'extension php-memcached
  - Faire  `chmod +x install.sh` pour ensuite le lancer `sudo ./install.sh`
  - Si problème regarde le fichier et improvise.

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
