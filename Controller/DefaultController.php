<?php

namespace DanielClements\ColourPickerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DcColourPickBundle:Default:index.html.twig', array('name' => $name));
    }
}
