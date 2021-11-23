<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MainController extends AbstractController
{

    private $repoTricks;

    public function __construct(TrickRepository $repoTricks)
    {
        $this->repository = $repoTricks;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        // $tricks = $this->getDoctrine()
        //     ->getRepository(Trick::class)->findAll();
        $tricks = $this->repository->findAll();
        return $this->render('main/homePage.html.twig', [
            'controller_name' => 'MainController',
            'current_menu' => 'home',
            'tricks' => $tricks
        ]);
    }

    /**
     * @ParamConverter("trick", class="Trick")
     * @route("/trick/{slug}", name="trick") 
     */
    public function showTrick(Trick $slug, Request $request)
    {
        $trick= $this->repository->findOneBy(['slug' => $slug]);
        $slug =$trick->getSlug();
        return $this->render('main/trick.html.twig', [
            'trick' => $trick
        ]);
    }
}
