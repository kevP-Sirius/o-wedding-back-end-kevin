# Journal de bord "équipe"

## Jour 1 - vendredi 30 août 2019 

- discussion sur les sujets relatifs à la première journée du sprint 1

- Création du doc de projet
- Création du cahier des charges
- Description succinte -> tout le monde est OK sur l'objectif
- Identifier les typologies d'utilisateurs
- Identifier les grands domaines fonctionnels
- Equipe et répartition des rôles
- Réfléchir à un premier jet d'arbo
- Répartition du travail de specs fonctionnelles toujours en cours et a finir sur le jours 2 .
- Contraintes et choix techniques (liste des outils, contraintes : responsive, qualité, bonnes pratiques, conventions, etc...) => non terminé à terminer sur le jours 2 .



## Jour 2 - lundi 2 septembre 2019

### J2 : Coordination - 1er Daily meeting (réunion quotidienne)

Objectifs : 
- Finir le cahier des charges
- Terminer liste des taches Front et back + repartitions
- Voir taches reunion sur Trello
- Commencer journal de bord. 
- Arborescence 
  
  - Cote front : 
  - installer toutes les dependances avant de debuter le dev. 
  - Penser a tester le site sur plusieurs navigateur.
  - Tester le responsive sur plusieurs navigateur.(debut derniere semaine si on a le temps)



- Global :
  - Penser au deploiement back et front assez vite et tot car il faut que chacun puisse verifier si les routes sont ok et le lien avec le back est ok. Car si pas deployé ca peut marcher en local mais peut avoir cpt different en ligne. Tenter de le faire la semaine prochaine une fois que le premier formulaire est ok (login). 
  


### J2 : Travail perso/groupe front/groupe back . 

on a complété ce qu'on avait pas pus terminé le jour 1 =>
- Contraintes et choix techniques (liste des outils, contraintes : responsive, qualité, bonnes pratiques, conventions, etc...) =>terminé 
- Coordination fonctionnelle : Priorisation du Backlog fait / Identifier le MVP fait .

- Coordination technique :
tache front/back organisé et en cours de lancement pour les premiers documents (création MCD/arbo back , finalisation des wireframes/finalisation arborescence du site )


- Remplissage du trello en équipe de 4 .
- En front :
    - Laurine s'occupe des Wireframe au propre , 
    - Guéna s'occupe de l'arborescence du site ,
- En back : 
    - Delphine et Kévin s'occupe du MCD et du premier jet d'architecture du back .
- Installation de setup fonctionnel à faire pour mardi 3 septembre (installer React/Symfony).


## Jour 3 - mardi 03 septembre 2019

- Réunion avec l'équipe référente
- Reprise du cahier des charges suite à la réunion
- Validation des wireframes et arborescence du site
- Création du dico des routes
- Mise à jour des user_stories en .md

- Coté Back :
    - Mise à jour du MCD suite réunion
    - Refonte de l'architecture du site
    - Création du dico des données

- Coté front :
    - Charte css
    - Documentation git
    - Installation react et dépendances

## Jour 4 : mercredi 04 septembre 2019

- Mise au point Git

- Coté front :
    - création formulaire d'inscription

- Coté Back :
    - mise à jour MCD, dico des routes et dico des données
    - création des controllers backend
    - Création des CRUD en cours
    - Création des controllers API
    - création API Connexion et inscription

## Jour 5 : jeudi 05 septembre 2019

- Coté front :
    - création formulaire d'inscription en dynamique
    - création des statiques de home pages et maquete du site  
    - choix des couleurs du site et du css , premier essaie de design global du site 
    - mise en place de redux/midleware pour la gestion du parcours des données du formulaire
- Coté Back :
    - mise à jour MCD, dico des routes et dico des données
    - Création des CRUD du backend en cours 
    - Création des CRUD de l'entité projet coté  API
    - création API Connexion et inscription


## Jour 6 : vendredi 06 septembre 2019

- Coté front :
    - création formulaire de connexion en dynamique
    - mise en place du css/style global du site . 
    - poursuite de la mise en place de redux/midleware pour la gestion du parcours des données des formulaires
- Coté Back :
    - mise à jour MCD, dico des routes et dico des données
    - Finalisation des CRUD du backend en cours et mise en place des user/login et hierarchie
    - Finalisation des CRUD de l'entité provider et guest coté  API

## Jour 7 : lundi 09 septembre 2019

- Coté front :
    - mise en place des pages dynamique de la home de la page inscription et connexion et première mise en place du dashboard.
    - poursuite de la mise en place de redux/midleware pour la gestion du parcours des données des formulaires
- Coté Back :
    - mise à jour MCD, dico des routes et dico des données.
    - première mise en ligne pour le back avec l'api fonctionnel dans ses fonctionnalité de base sur le crud du user/project/guest/provider et mise en place d'un system de newsletter avec désabonnement possible .
    - user/login et hierarchie fonctionnel 
    - poursuite de test front et back pour la correction et la gestion des erreurs de l'API 
   

## Jour 8 : mardi 10 septembre 2019

- Coté front :
    - poursuite de la  mise en place des pages dynamique de la home de la page inscription et connexion finalisation  du dashboard après connexion et test sur l'API mise en ligne réussi.
    - poursuite de la mise en place de redux/midleware pour la gestion du parcours des données des formulaires
- Coté Back :
    - mise à jour MCD, dico des routes et dico des données.
    - poursuite de test front et back pour la correction et la gestion des erreurs de l'API .
    - mise en place d'une route pour effectuer des requetes portant sur la recherche d'un provider par          prix/theme/department réussi .


## Jour 9 : mercredi 11 septembre 2019

- Coté front :
    - poursuite de la  mise en place des pages dynamique de la home de la page inscription et connexion finalisation  du dashboard après connexion .
    - premier essaie de mise en place des données récupérées infructueuse pour l'instant necessite de patcher la liste d'invité afin d'y inclure la qualité de l'invité (simple invité /témoin/conjoint1/conjoint2)
    - poursuite de la mise en place de redux/midleware pour la gestion du parcours des données des formulaires
- Coté Back :
    - mise à jour MCD, dico des routes et dico des données.
    - poursuite de test front et back pour la correction et la gestion des erreurs de l'API .
    - poursuite de la finalisation du back-end 
    - ajout de la propriété Type dans le guest afin de résoudre le problème coté front + mise à jours des doc 
    - nécessité de comprendre le comportement différent de l'api en mode prod et dev car en mode dev celle-ci retourne bien ce qu'il faut en terme de data json mais en prod on a une erreur 500.
    - finalisation de l'interface admin.

## Jour 10 : jeudi 12 septembre 2019

- Côté front :
    - Amélioration du CSS
    - Création de la liste des invités

- Côté Back :
    - Mise en prod de l'interface admin (tout est ok)
    - Mise en ligne de l'interface admin
    - Gestion des bug entre back et front


## Jour 11 : vendredi 13 septembre 2019

- Côté front :
    - Finalisation de la liste des invités

- Côté Back :
    - gestion des types d'invités

## Jour 12 : lundi 16 septembre 2019

- Côté Front :
    - Fonctionnalité "prestataire"
    - bug sur le caroussel

- Côté Back :
    - fixtures pour les providers avec description et photo
    - Mise à jour de l'affichage des données pour la route de test et barre de recherche et affichage du projet à la connexion
    - renvois du nombre de recherche faite par le user

## Jour 13 : mardi 17 septembre 2019

- Côté Front :

    - Liste des prestataires terminé
    - Création du suivi du budget

- Côté back :

    - création de la page contact
    - parametrage du renvoie de certaines données
    - Mise en forme des mails de la messagerie
    - Finalisation de la page a propos
    - Création de la page 404
