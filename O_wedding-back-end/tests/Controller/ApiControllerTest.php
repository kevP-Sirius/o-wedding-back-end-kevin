<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
 Dans les bonnes pratiques : https://symfony.com/doc/current/best_practices/tests.html

 il est de coutume de faire au moins ce test a minima : tester que toutes mes urls sont accessibles

 en effet , dans les bonnes pratiques je ne suis pas censée avoir de liens morts dans mon application
*/
class ApiControllerTest extends WebTestCase
{

    //FRONT
    /**
     * Cette annoation va permettre de relancer la fonction de test testPageFrontIsSuccessful autant de fois que j'ai de yield de present dans la fonction urlProviderFront
     * 
     * Note: yield est une fonction tres specifique il n'est pas necessaire d'en comprendre le fonctionnement complet. l'application de chez symfo suffit a son utilisation
     * 
     * 
     * @dataProvider urlProviderFront
     */
    // public function testPageFrontIsSuccessful($url)
    // {
    //     $client = self::createClient();
    //     $client->request('GET', $url); // /, /posts etc...

    //     $this->assertTrue($client->getResponse()->isSuccessful());
    // }

    // public function urlProviderFront()
    // {
       
    //     //yield ['/login ']; //redirection donc pas de code 200
    //     //yield ['/logout']; /redirection donc pas de code 200
    //    // yield ['/movie/blackfish']; // note cette url ne peux pas etre utilée tel quelle si les fixtures changent
    // }


    //BACK

    /** 
     * @dataProvider urlProviderBack
     */
    public function testPageBackIsSuccessful($url)
    {
        //pour s'authentifier , je vais avoir besoin non pas du login form
        // mais de HTTP basic qui sera plus rapide cf test/security.yml
        $client = static::createClient();

        $client->request('POST', $url); // /, /posts etc...

        //var_dump($client->getResponse()->getContent()); //redirige si login si pas de credentials
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProviderBack()
    {   
        
        yield ['/api/signup'];
        yield ['/api/signin'];
        yield ['/api/reset_password'];
        yield ['/confirm_reset/user/jwt'];
        yield ['/api/logout'];
        yield ['/api/project/new'];
        yield ['/api/project/show'];
        yield ['/api/account/update_password'];
        yield ['/api/project/edit/name'];
        yield ['/api/project/edit/date'];
        yield ['/api/project/edit/budget'];
        yield ['/api/project/guests/edit'];
        yield ['/api/project/guests/create'];
        yield ['/api/project/guests/remove'];
        yield ['/api/project/newsletter'];
        yield ['/api/project/provider/add'];
        yield ['/api/project/provider/remove'];
        yield ['/api/project/edit/department'];
        yield ['/api/search/provider'];
        yield ['/api/project/guest/information/change/1/jwt/newsletter_status'];
        yield ['/api/alert/change-status'];
        yield ['/api/contact/admin'];
        yield ['/api/project/contents/test'];
        yield ['/api/test/trafic'];
        yield ['/mercure/chat/1'];
        //...
    }
}