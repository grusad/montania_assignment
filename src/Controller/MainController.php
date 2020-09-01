<?php


namespace App\Controller;


use App\Article;
use App\Product;
use App\Program;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function homepage()
    {
        $program = new Program();

        return $this->render('questions/show.html.twig',
            ['products' => $program->getProducts(),
                'mostExpensiveProducts' => $program->getMostExpensiveProducts(),
                'cheapestProducts' => $program->getCheapestProducts(),
                'numberOfProducts' => $program->getNumberOfProducts()]
        );

    }

}