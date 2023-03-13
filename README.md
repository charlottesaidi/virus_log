Virus Log Symfony/Vue Api
========================

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

Accéder à l'URL donnée (<https://localhost:8000> par défaut).

**3** Utilisateurs tests :
#### admins
Email: "127.0.0.1", Clé: "!" 
#### utilisateur lambda
Email: "158.242.1.128", Clé: "!"  

[1]: https://symfony.com/doc/current/best_practices.html
[2]: https://symfony.com/doc/current/setup.html#technical-requirements
[3]: https://symfony.com/download
[4]: https://symfony.com/book
