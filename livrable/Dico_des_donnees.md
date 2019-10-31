# Dictionnaire des données

## USER

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant de l'utilisateur |
|username|VARCHAR(64)|NOT NULL|Le nom de l'utilisateur|
|email|VARCHAR(255)|NOT NULL|L'email de l'utilisateur|
|password|VARCHAR(255)|NOT NULL|Le mot de passe de l'utilisateur|
|created_at|DATETIME|NOT NULL| La date de création de l'utilisateur|
|updated_at|DATETIME|NULL|La date de mise à jour de l'utilisateur|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|is_connect|BOOLEAN|NOT_NULL|Indique si l'utilisateur est déja connecté|
|session_duration|INT|NULL|Indique la durée de la session de l'utilisateur|
|token|VARCHAR(255)|NULL|Permet de vérifier la concordance des appels API|
|project_id|FOREIGN_KEY|NULL|Clef étrangère du projet|


## ROLE

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|
|name|VARCHAR(64)|NOT NULL|nom du role|
|roleString|VARCHAR(255)|NULL|code du role|
|created_at|DATETIME|NOT NULL| La date de création du role|
|updated_at|DATETIME|NULL|La date de mise à jour du role|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|


## PROJECT

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant du projet|
|name|VARCHAR(128)|NOT NULL|Nom du projet|
|deadline|VARCHAR(128)|NOT NULL|Date du mariage|
|forecast_budget|MEDIUMINT|NOT NULL, UNSIGNED|le budget de départ|
|current_budget|MEDIUMINT|NOT NULL, SIGNED|le budget en cours|
|created_at|DATETIME|NOT NULL| La date de création du projet|
|token|VARCHAR(255)|NULL|Permet de vérifier la concordance des appels API|
|updated_at|DATETIME|NULL|La date de mise à jour du projet|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|departement_id|FOREIGN_KEY|NULL|Clef étrangère du département|
|user_id|FOREIGN_KEY|NULL|Clef étrangère du user|
|guest_id|FOREIGN_KEY|NULL|Clef étrangère de l'invité|
|provider_id|FOREIGN_KEY|NULL|Clef étrangère du prestataire|


## GUEST

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant de l'invité|
|lastname|VARCHAR(128)|NOT NULL|Nom de l'invité|
|firstname|VARCHAR(128)|NOT NULL|Prénom de l'invité|
|email|VARCHAR(255)|NOT NULL|L'email de l'invité|
|type|VARCHAR(64)|NULL|qualité de l'invité|
|phone_number|TINYINT|NOT NULL, UNSIGNED|Numéro de téléphone de l'invité|
|is_coming|BOOLEAN|NULL|Indique si l'invité seras présent ou non|
|is_coming_with|INT|NULL|Indique avec combien de personne l'invité viendras|
|vegetarian_meal_number|INT|NULL|Indique avec combien de personne mangerons un repas végétarien|
|meat_meal_number|INT|NULL|Indique avec combien de personne mangerons un repas à base de viande|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|project_id|FOREIGN_KEY|NULL|Clef étrangère du project|
|created_at|DATETIME|NOT NULL|La date de création de l'invité|
|updated_at|DATETIME|NULL|La date de mise à jour de l'invité|

## PROVIDER

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant du prestataire|
|name|VARCHAR(128)|NOT NULL|Nom du prestataire|
|phone_number|TINYINT|NOT NULL, UNSIGNED|Numéro de téléphone du prestataire|
|email|VARCHAR(255)|NOT NULL|L'email du prestataire|
|average_price|MEDIUMINT|NOT NULL, UNSIGNED|le tarif moyen du prestataire|
|description|VARCHAR(255)|NULL|une brève description de ce que fais le prestataire|
|created_at|DATETIME|NOT NULL| La date de création du prestataire|
|updated_at|DATETIME|NULL|La date de mise à jour du prestataire|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|theme_id|FOREIGN_KEY|NULL|Clef étrangère du theme|
|department_id|FOREIGN_KEY|NULL|Clef étrangère du department|
|project_id|FOREIGN_KEY|NULL|Clef étrangère du project|

## DEPARTMENT

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant du département|
|number|TINYINT|NOT NULL, UNSIGNED|numéro de département|
|name|VARCHAR(128)|NOT NULL|Nom du département|
|created_at|DATETIME|NOT NULL| La date de création du département|
|updated_at|DATETIME|NULL|La date de mise à jour du département|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|project_id|FOREIGN_KEY|NULL|Clef étrangère du project|
|provider_id|FOREIGN_KEY|NULL|Clef étrangère du prestataire|

## THEME

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant du théme|
|name|VARCHAR(128)|NOT NULL|Nom du théme|
|created_at|DATETIME|NOT NULL| La date de création du théme|
|updated_at|DATETIME|NULL|La date de mise à jour du théme|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|provider_id|FOREIGN_KEY|NULL|Clef étrangère du prestataire|


## TYPE


|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant du type|
|name|VARCHAR(128)|NOT NULL|Nom du type|
|created_at|DATETIME|NOT NULL| La date de création du type|
|updated_at|DATETIME|NULL|La date de mise à jour du type|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|
|guest_id|FOREIGN_KEY|NULL|Clef étrangère de l'invité|

## CHAT

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|L'identifiant du chat|
|message_id|FOREIGN_KEY|NULL|Clef étrangère des méssage|
|created_at|DATETIME|NOT NULL| La date de création du chat|
|updated_at|DATETIME|NULL|La date de mise à jour du chat|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|


## MESSAGE

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|
|body|VARCHAR(255)|NOT NULL|  corp du message|
|from|VARCHAR(64)|NOT NULL| destinateur du message|
|to|VARCHAR(64)|NOT NULL| destinataire du message|
|chat_id|FOREIGN_KEY|NULL|Clef étrangère du chat|
|created_at|DATETIME|NOT NULL| La date de création du message|
|updated_at|DATETIME|NULL|La date de mise à jour du message|
|is_active|BOOLEAN|NOT_NULL|Indique si il est actif|

