<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestmailController extends AbstractController
{
    /**
     * @Route("/testmail", name="testmail")
     */
    public function index()
    {
        return $this->render('testmail/index.html.twig');
    }
}
