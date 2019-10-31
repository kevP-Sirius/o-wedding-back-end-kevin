<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="backend_index", methods="GET")
     */
    public function index()
    {
        return $this->render('backend/default/index.html.twig');
    }

    /**
     * @Route("/backend/list", name="backend_list", methods="GET")
     */
    public function list()
    {
        return $this->render('backend/default/list.html.twig');
    }
}
