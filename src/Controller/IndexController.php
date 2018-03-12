<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    /*

 * @Route("/",name="index")
 * @return Response


    public function index(): Response
    {
        #@debug displaying current locale
        #$locale = $request->getLocale();
        #VarDumper::dump($locale);

        # display page from twig template
        return $this->render('main/index.html.twig', []);
    }
    */
}