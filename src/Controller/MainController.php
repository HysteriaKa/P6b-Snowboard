<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Category;
use App\Form\EditTrickType;
use App\Repository\TrickRepository;
use PhpParser\Node\Expr\Cast\String_;
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
    public function index(Request $request): Response
    {
        $qty = $request->query->get('showTricks');
        if ($qty === NULL) $qty = 15;
        $tricks = $this->repository->showTricks($qty);
        $allTricks = $this->repository->findAll();
        return $this->render('main/homePage.html.twig', [
            'controller_name' => 'MainController',
            'current_menu' => 'home',
            'tricks' => $tricks,
            'allTricks' => $allTricks
        ]);
    }

    /**
     * @route("/trick/{slug}", name="trick") 
     */
    public function showTrick(String $slug, Request $request)
    {
        $trick = $this->repository->findOneBy(['slug' => $slug]);
        $slug = $trick->getSlug();

        return $this->render('main/trick.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @route("/trick/edit/{id}", name="trick_edit") 
     */
    public function editTrick(int $id, Request $request)
    {
        $trick = $this->repository->findOneBy(['id' => $id]);
        $idCategory = $trick->getCategory();
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();       
            $trick->setUpdateAt(new \dateTime());        
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('message', 'This trick has been updated ');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('main/ediTrick.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}
