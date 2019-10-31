<?php

// src/DataFixtures\Faker

namespace App\DataFixtures\Faker;

use \Faker\Provider\Base as BaseProvider;

class ProviderAndThemeAndDepartmentProvider extends BaseProvider{

    protected static $themeName = [
        'Musique', 'Animation' , 'Tailleurs','Traiteur' ,'Alliances' , 'Transport' ,'Salle', 
        
    ];

    protected static $average_price = [
        '950', '575' , '650' ,'750' ,'530', '545' ,'546' ,'568' ,'710' ,'899' ,'799' ,'699' ,'599' ,'499' ,'730','780' ,'450' ,'1500' ,'1200' ,'1300' ,'2700' ,'3800' ,'4500' ,'5999','7900' ,'8500','10850'
        
    ];

    protected static $departmentNumber = [
        '01', '02' ,'03' ,'04' ,'05', '06' ,'07' ,'08' ,'09' ,'10' ,'11' ,'12' ,'13' ,'14' ,'15','16' ,'17' ,'18' ,'19' ,'21' ,'22' ,'23' ,'24' ,'25','26' ,'27','28','29','30', '31' ,'32' ,'33','34' ,'35' ,'36' ,'37' ,'38' ,'39' ,'40' ,'41', '42' ,'43' ,'44' ,'45' ,'46','47' ,'48' ,'49' ,'50' ,'51' ,'52','53','54','55','56','57','58','59','60' ,'61' ,'62','63','64','65','66','67','68','69','70','70' ,'71' ,'72','73','74','75','76','77','78','79','80','81' ,'82','83','84','85','86','87','88','89','90' ,'91' ,'92','93','94','95' ,'2A' ,'2B'
        
    ];

    protected static $departmentName = [
        'Ain' , 'Aisne ' , 'Allier' , 'Alpes-de-Haute-Provence' , 'Hautes-Alpes' ,'Alpes-Maritimes ','Ardèche' ,'Ardennes', 'Ariège', 'Aube','Aude' ,'Aveyron' , 'Bouches-du-Rhône' ,'Calvados','Cantal','Charente','Charente-Maritime','Cher','Corrèze', 'Corse-du-Sud','Haute-Corse','Côte-d\'Or', 'Côtes d\'Armor', 'Creuse','Dordogne',
        'Doubs' ,'Drôme','Eure' ,'Eure-et-Loir' ,'Finistère',
        'Gard' , 'Haute-Garonne' , 'Gers' ,'Gironde','Hérault','Ille-et-Vilaine', 'Indre' ,'Indre-et-Loire' ,'Isère' ,'Jura' , 'Landes','Loir-et-Cher','Loire' ,'Haute-Loire' ,'Loire-Atlantique',	'Loiret','Lot' ,'Lot-et-Garonne','Lozère','Maine-et-Loire', 'Manche' , 'Marne','Haute-Marne' ,'Mayenne','Meurthe-et-Moselle','Meuse',
        'Morbihan','Moselle','Nièvre','Nord','Oise','Orne','Pas-de-Calais' ,'Puy-de-Dôme','Pyrénées-Atlantiques','Hautes-Pyrénées' ,'Pyrénées-Orientales' , 'Bas-Rhin',
        'Haut-Rhin','Rhône','Haute-Saône','Saône-et-Loire' ,
        'Sarthe' ,'Savoie' ,'Haute-Savoie' ,'Paris','Seine-Maritime' , 'Seine-et-Marne' ,'Yvelines','Deux-Sèvres' ,'Somme','Tarn' , 'Tarn-et-Garonne','Var','Vaucluse' , 'Vendée' ,'Vienne' , 'Haute-Vienne', 'Vosges','Yonne','Terr. de Belfort','Essonne','Hauts-de-Seine' , 'Seine-St-Denis','Val-de-Marne' , 'Val-D\'Oise' 
    ];

    protected static $typeName = [
        'maried', 'witness','guest'
    ];
    protected static $roleName = [
        'user' ,'administrator'
        
    ];

    protected static $roleString = [
        'ROLE_USER' , 'ROLE_ADMIN'
        
    ];



    public static function typeName(){
        return static::randomElement(static::$typeName);
    }
    public static function roleString(){
        return static::randomElement(static::$roleString);
    }

    public static function roleName(){
        return static::randomElement(static::$roleName);
    }

    public static function averagePrice(){
        return static::randomElement(static::$average_price);
    }
    public static function departmentNumber(){
        return static::randomElement(static::$departmentNumber);
    }
    public static function departmentName(){
        return static::randomElement(static::$departmentName);
    }

    public static function themeName(){
        return static::randomElement(static::$themeName);
    }


}