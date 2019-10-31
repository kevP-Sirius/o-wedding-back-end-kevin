Nom:POGNON
Prénom:Kévin

# Description du projet

## Projet idee globale

Création d'un site internet de gestion de mariage avec mise en relation avec des profesionnel. Ce site permet dans un premier temps d'organiser son projet de mariage (á l'aide d'outils de gestions et de créations) dans le budget que les futurs mariés se seront fixé, mais aussi en fonction de leur goûts, leur desideratas, leur regions, le nombre d'invité, etc.. puis optionnelement d'etre mis en relation directe avec des prestataires de leur regions répondant á leur criteres.
L'objectif de cette mise en relation est de mettre en place une relation de confiance entre les futurs mariés et les prestataires sous la forme d'une methode agile. Les futurs mariés ainsi que les prestataires pourront suivre via notre plateforme l'avancée de leur projet commun.

## Fonctionnalités et objectifs

- A l'arrivée sur le site : 
    - Creation d'un compte utilisateur pour les futurs mariés + envoi mail confirmation avec lien de redirection. 
    - Creation d'un compte pro pour les prestataires souhaitant beneficier de la mise en relation directe avec leur potentiels clients + envoi mail confirmation avec lien de redirection.

- Une fois connecté :
    - Indication pour se servir du site
    - Création du projet : On entre le nom du projet, la date, le lieu (region du projet), le budget. 
    - Une fois le projet crée :
        - creation de la liste des postes de dépenses du projet par theme et par budget (exemple: budget alliance, budget tenues, bugdet traiteurs,etc), cette liste permettra d'allouer le budget global pour le mariage en fonction de chaque poste de dépenses (dj, traiteur, etc)
        - choix des prestataires
        - Suivi du budget global
        - Générateur de plan de table : Le site donnera la possiblité de faire un plan pdf ou en ligne pour pouvoir l'imprimer ou le partager. (entrer le nom des invités et dû coups quand on rentre le nom on ajoute aussi les préférences ,qui à côté de qui, régimes alimentaires etc... ). Le plan de table se fera sous la forme d'un drag and drop afin de rendre l'experience plus simple et plus facile a visualiser (table ronde ou carré suivant le nombre de personne par table).

- Une fois la liste remplie pour la partie dev il sera question de creer des onglets par theme (onglet dj, onglet robe, onglet lieu du mariage, onglet traiteur, etc en fonction de ce que l'utilisateur a alloué a son budget). L'utilisateur poura choisir parmis la liste des prestataires en fonction de son budget. Son budget global apparaitra dans une partie de l'interface afin de pouvoir suivre au mieux le poste de depenses. Cependant, si l'utilisateur ne souhaite pas faire appel à nos prestataires il peut aussi rentrer lui meme le poste de depense sans faire appel a l'un de nos prestataires, le budget se mettra alors a jour automatiquement.

  
- Autre fonctionnaliteé du site : Grace a la date prevue du mariage (donnée lors de la premiere visite du site), creation d'un outil qui  génère un planning un an en arrière (ex : J-365 réservation de la salle, etc) sous la forme d'un planning avec les dead lines visuelles pour les futurs mariés. Ça génère toutes les étapes de la préparation du mariage  avec la possiblité de pouvoir transformer le projet en PDF.

- Autre fonctionnaliteé du site : Generer une liste d'invitee afin de creer un formulaire de presence ou absence au mariage. Avec possibilite de valider ou pas presence par les futurs mariés eux meme.

- Catégorie DJ : possibilité pour les utilisateurs de rentrer les musiques souhaitées ou non a leur mariage. 
- Categorie traiteur : possibilité pour les utilisateurs de rentrer les eventuels regimes alimentaires et allergies.
- Categorie lieu : possibilité de selectionner les dommaines avec ou sans couchages.

# Typologie d'utilisateur

- Administrateur pour le coté back .
- Utilisateur normal pour le coté front .
- Guest pour le coté back (interface avec un formulaire de renseignement à completer par le Guest)
- Provider dans la V2 avec une interface propre à ce dernier .
  

# Objectifs

- Reussir un MVP 
- Interface facile d'utilisation
- Mise en ligne possible a l'issue de la V1
- Acheter un nom de domaine pour eviter que la partie chiffré de l'IP soit visible. 


# Contraintes

- Contrainte de temps (deadline)
- Site responsive
- Opquast au possible

 Liste des outils :
 
 - React : redux, axios, react router, mercure,keyframe CSS,(semantic ui ou autre)
 - Symfony : Nelmio Cors , FIREBASE-JWT ,bootstrap,jquery ,mercure,swiftmailer,keyframe CSS.



