<?php
//  Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function hello($userName)
    {
        return $this->render(
            'hello.html.twig',
            ['userName' => $userName,]
        );
    }
}
