# Dictionnaire des routes

## Front

| En tant que... | Je souhaite... | afin de... | Routes |
|--|--|--|--|
|visiteur|naviguer dans le site |prendre connaissance du site|"/"|visiter le site|
|visiteur|avoir accès a la page à propos |connaitre les informations qui y serais présente |"/about"|lire les "à propos"|
|visiteur|avoir accès a la page contact |prendre contact avec l'administrateur du site |"/about"|envoyer un message à l'administrateur|
|visiteur|avoir accès a la page mentions légales |prendre connaissance des conditions d'utilisation  |"/legal-mentions"|text de lois et autres conditions d'utilisation |
|visiteur|créer un compte|avoir un espace personel|"/signup"|création de compte|
|visiteur|me connecter à  mon compte|gérer les données de mon projet et de mon compte|"/"|connexion de l'utilisateur à son compte|
|Utilisateur|gérer les données de mon compte|modifier son contenue ou le supprimer|"/account"|gérer les données du comptes|
|Utilisateur|pouvoir créer les données de mon projet|utiliser pleinement les fonctionnalités du site|"/dashboard"|création de projet et de son contenu |
|Utilisateur|pouvoir lire mes données | avoir accès à aux données de mon projet |"/dashboard"|lire le contenu de son projet |
|Utilisateur|pouvoir mettre à jour mes données|pouvoir utiliser les fontionnalitées du site|"/dashboard"|modifier le contenu du projet|
|Utilisateur|pouvoir mettre à jour les données de mes prestataires de mon projet |pouvoir utiliser les fontionnalitées du site|"/dashboard"|modifier le contenu des éléments du projet|
|Utilisateur|pouvoir supprimer mes données|pouvoir utiliser les fontionnalitées du site|"/dashboard"|supprimer le contenu du projet ou le projet|
|Utilisateur|pouvoir remplir ma liste d'invité|pouvoir utiliser les fontionnalitées du site|"/dashboard/guests"|faire sa liste d'invité|
|Utilisateur|pouvoir crée mon plan de table |pouvoir utiliser les fontionnalitées du site|"/dashboard/tables"|faire son plan de table|
|Utilisateur|pouvoir accéder à la liste des prestataires |pouvoir utiliser les fontionnalitées du site|"/dashboard/providers"|listes des prestataires |

## Fonctionnalitées optionnelles

| En tant que... | Je souhaite... | afin de... | Routes |
|--|--|--|--|
|Utilisateur|pouvoir rechercher via une searchbar les prestataires |pouvoir utiliser les fontionnalitées du site|"/dashboard/searchbar"|rechercher un prestataire en fonction de critère|


## Back

| En tant que... | Je souhaite... | afin de... | Routes |Méthode|Controller | Fonction | Data attendu | Description |
|--|--|--|--|--|--|--|--|--|
|visiteur|créer un compte|avoir un espace personel |"/api/signup"|POST|ApiController|signup|username/password/email|création de compte|
|visiteur|me connecter à  mon compte|accéder aux données de mon compte |"/api/signin"|POST|ApiController|signin|username/password|connexion de l'utilisateur à son compte|
|visiteur|en donnant mon email que le site m'envoie un email de redirection vers une page pour changer mon mot de passe |accéder aux données de mon compte |"/api/reset_password"|POST|ApiController|reset_password|email|procédure mot de passe oublié avec envoie de l'email qui redirige vers un formulaire du back pour la modification du mot de passe|
|visiteur|après avoir cliqué sur le lien de redirection acceder au formulaire pour changer de le mot de passe et avoir la confirmation du changement sur cette page |accéder aux données de mon compte |"/api/confirm_reset/user/{jwt}"|GET|ApiController|confirm_reset|jwt dans l'url de redirection envoyer dans le mail|procédure mot de passe oublié avec envoie du mail qui redirige vers un formulaire du back pour la modification du mot de passe et sa confirmation visuel|
|Utilisateur|modifier le mot de passe |mettre à jours les données du mot de passe|"/api/account/update_password"|POST|ApiController|updatePassword|token/username/oldpassword/newpassword|gestion du changement de mot de passe|
|utilisateur|me deconnecter de mon compte|quittez mon tableau de bord|"/api/logout"|POST|ApiController|logout|token/username|deconnexion de l'utilisateur à son compte|gérer la deconnexion|
|Utilisateur|pouvoir créer mon projet|utiliser pleinement les fonctionnalités du site|"/api/project/new"|POST|ApiController|newProject|token/username/name/date/forecast_budget/departement|création de projet et de son contenue|
|Utilisateur|pouvoir lire les données de mon projet | avoir accès à aux données de mon projet |"/api/project/show"|POST|ApiController|showProject|token/username|lire le contenu de son projet |
|Utilisateur|pouvoir mettre à jour le nom de mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/edit/name"|POST|ApiController|updateName|token/username/newName|modifier le contenu du projet|
|Utilisateur|pouvoir mettre à jour la date de mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/edit/date"|POST|ApiController|updateDate|token/username/newDate|modifier le contenu du projet|
|Utilisateur|pouvoir mettre à jour le budget de mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/edit/budget"|POST|ApiController|updateBudget|token/username/newBudget|modifier le contenu du projet|
|Utilisateur|pouvoir mettre à jour les données des invitées de mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/guests/edit"|POST|ApiController|updateGuests|token/id(du guest)/username/data optionel:(/firstname/lastname/phoneNumber/email/type/is_coming)|modifier le contenu du projet|
|Utilisateur|pouvoir ajouter des invitées à mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/guests/create"|POST|ApiController|createGuests|token/username/firstname/lastname/data optionel:(type/phoneNumber/email)|modifier le contenu du projet|
|Utilisateur|pouvoir supprimé des invitées à mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/guests/remove"|POST|ApiController|removeGuests|token/username/id(du guest)|modifier le contenu du projet|
|Utilisateur|pouvoir faire usage de la newsletter |pouvoir utiliser les fontionnalitées du site|"/api/project/newsletter"|POST|ApiController|newsletter|token/username/data optionel:(message)|envoyer un méssage automatique ou personnalisé à mes invitées|
|Utilisateur|pouvoir ajouter un  prestataire à mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/provider/add"|POST|ApiController|addProviders|token/id(du provider)/username|modifier le contenu du projet|
|Utilisateur|pouvoir supprimer un prestataire de mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/provider/remove"|POST|ApiController|removeProvider|token/username|modifier le contenu du projet|
|Utilisateur|pouvoir mettre à jour le Département  de mon projet |pouvoir utiliser les fontionnalitées du site|"/api/project/edit/department"|POST|ApiController|updateDepartment|token/username/newDepartment|modifier le contenu du projet|
|Utilisateur|pouvoir rechercher un prestataire en fonction de son prix de son theme et de son department|pouvoir utiliser les fontionnalitées du site|"/api/search/provider"|POST|ApiController|searchbar|token/username/data optionel:(price/theme/department)| choisir un prestataire pour son projet|
|Utilisateur|mes invitées puissent via une interface de type formulaire à remplir m'indiquer si il seront présent ou absent/ le type de menu(poisson ou viande)/et le nombre de personne venant avec l'invité|pouvoir utiliser les fontionnalitées du site|"/api/project/guest/information/change/{id}/{jwt}/{order}"|GET|ApiController|guestInformation|route en GET data dans l'url attendu|échange d'information guest/user|
|Utilisateur|pouvoir désactiver les alertes email du site concernant les menaces de sécurité en cas de login d'un compte déja connecté|pouvoir utiliser les fontionnalitées du site|"/api/alert/change-status"|POST|ApiController|alertStatus|token/username|permet de changer le status de l'activité de l'alert intrusion|
|Utilisateur|pouvoir utilisé un formulaire de contact|de pouvoir prendre contact les administrateurs du site|"/api/contact/admin"|POST|ApiController|adminContact|message/nom/email|contacter un administrateur pour que celui-ci vous réponde|
|developpeur|avoir une route de test| faire mes verifications|"/api/project/contents/test"|GET|ApiController|adminContact|message/nom/email|route de test pour les jeux de données|
|Administrateur|avoir une interface graphique| pour se connecter au back-end |"/"|GET|DefaultController|index|xxx|interface graphique du point d'entré de la connexion au back-end par l'admininistrateur|
|Administrateur|avoir une interface graphique| pour se connecter au back-end |"/backend/login"|GET/POST|UserController|login|xxx|interface graphique du point d'entré de la connexion au back-end par l'admininistrateur|
|Administrateur|avoir bouton deconnection| pour se deconnecter au back-end |"/backend/logout"|GET|UserController|logout|xxx|sortie de back-end par l'admininistrateur|
|Administrateur|acceder la liste de toutes mes entitées | gérer les différentes entitées |"/backend/list"|GET|DefaultController|list|xxx|gérer les entitées|
|Administrateur|avoir la possibilité d'acceder à la liste de l'entitée project | pour en gérer le contenue back-end |"/backend/project/list"|GET|ProjectController|list|xxx|affiche la liste des projets|
|Administrateur|avoir la possibilité d'acceder aux données d'un projet | pour en gérer le contenue back-end |"/backend/project/{id}"|GET|ProjectController|show|xxx|affiche les données du projet|
|Administrateur|avoir la possibilité de supprimer un projet | pour en gérer le contenue back-end |"/backend/project/delete/{id}"|GET|ProjectController|delete|xxx|suppression|
|Administrateur|avoir la possibilité d'acceder à la liste de l'entitée user | pour en gérer le contenue back-end |"/backend/user/list"|GET|UserController|list|xxx|liste de tout les users|
|Administrateur|avoir la possibilité d'acceder aux données d'un user | pour en gérer le contenue back-end |"/backend/user/{id}"|GET|UserController|show|xxx|affiche les données du user|
|Administrateur|avoir la possibilité créer un user | pour en gérer le contenue back-end |"/backend/user/new"|GET/POST|UserController|new|xxx|création d'un user|
|Administrateur|avoir la possibilité d'acceder à un user admin | modifier le mot de passe |"/backend/user/edit/{id}"|GET/POST|UserController|edit|xxx|modifier le mot de passe|
|Administrateur|avoir la possibilité de supprimer un user  | pour en gérer le contenue back-end |"/backend/user/delete/{id}"|GET|UserController|delete|xxx|supprimer un user|
|Administrateur|avoir la possibilité d'acceder à la liste des providers| pour en gérer le contenue back-end |"/backend/provider/list"|GET|ProviderController|list|xxx|liste de l'entité provider|
|Administrateur|avoir la possibilité d'acceder aux données d'un provider | pour en gérer le contenue back-end |"/backend/provider/{id}"|GET|ProviderController|show|xxx|affiche les données d'un provider|
|Administrateur|avoir la possibilité de créer un provider| pour en gérer le contenue back-end |"/backend/provider/new"|GET/POST|ProviderController|new|xxx||
|Administrateur|avoir la possibilité modifier un provider| pour en gérer le contenue back-end |"/backend/provider/edit/{id}"|GET/POST|ProviderController|edit|xxx|modifier un provider|
|Administrateur|avoir la possibilité de supprimer un provider| pour  gérer le contenue back-end |"/backend/provider/delete/{id}"|GET|ProviderController|delete|xxx|suppression d'un provider|
|Administrateur|avoir la possibilité de lister les département| pour en gérer le contenue back-end |"/backend/department/list"|GET|DepartmentController|list|xxx|lister les départements|
|Administrateur|avoir la possibilité d'acceder aux données d'un depatement | pour en gérer le contenue back-end |"/backend/department/{id}"|GET|DepartmetController|show|xxx|affiche les données d'un département|
|Administrateur|avoir la possibilité de creer un département| pour en gérer le contenue back-end |"/backend/department/new"|GET/POST|DepartmentController|new|xxx|crer un départements|
|Administrateur|avoir la possibilité de modifier un département| pour en gérer le contenue back-end |"/backend/department/edit/{id}"|GET/POST|DepartmentController|edit|xxx|modifier un départements|
|Administrateur|avoir la possibilité de supprimer un département| pour en gérer le contenue back-end |"/backend/department/delete/{id}"|GET|DepartmentController|delete|xxx|supprimer départements|
|Administrateur|avoir la possibilité de lister les thèmes| pour en gérer le contenue back-end |"/backend/theme/list"|GET|ThemeController|list|xxx|lister les thèmes |
|Administrateur|avoir la possibilité d'acceder aux données d'un théme | pour en gérer le contenue back-end |"/backend/theme/{id}"|GET|ThemeController|show|xxx|affiche les données d'un théme|
|Administrateur|la créer un thème | pour en gérer le contenue back-end |"/backend/theme/new"|GET/POST|ThemeController|new|xxx|créer un thème|
|Administrateur|la modifier un thème | pour en gérer le contenue back-end |"/backend/theme/edit/{id}"|GET/POST|ThemeController|edit|xxx|modifier un thème|
|Administrateur|supprimer un thème | pour en gérer le contenue back-end |"/backend/theme/delete/{id}"|GET|ThemeController|delete|xxx|supprimer un thème|
|Administrateur|avoir la possibilité de lister les rôles| pour en gérer le contenue back-end |"/backend/role/list"|GET|RoleController|list|xxx|lister les roles |
|Administrateur|avoir la possibilité d'acceder aux données d'un role | pour en gérer le contenue back-end |"/backend/role/{id}"|GET|RoleController|show|xxx|affiche les données d'un role|
|Administrateur| créer un rôle| pour en gérer le contenue back-end |"/backend/role/new"|GET/POST|RoleController|new|xxx|créer un role|
|Administrateur| modifier un rôle| pour en gérer le contenue back-end |"/backend/role/edit/{id}"|GET/POST|RoleController|edit|xxx|modifier un rôle|
|Administrateur|supprimer un rôle | pour en gérer le contenue back-end |"/backend/role/delete/{id}"|GET|RoleController|delete|xxx|supprimer un rôle|
