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
        $incrementTricks =15;
        $start = $request->query->get('showTricks');
        if ($start === NULL) $start = 0;
        else $start = intval($start);
        $tricks = $this->repository->showTricks($start+$incrementTricks);
        $allTricksQty = count($this->repository->findAll());
        return $this->render('main/homePage.html.twig', [
            'controller_name' => 'MainController',
            'current_menu' => 'home',
            'tricks' => $tricks,
            'allTricksQty' => $allTricksQty
        ]);
    }

    /**
     * @route("/tricks", name="app_tricks") 
     */
    public function allTrick()
    {
        $tricks = $this->repository->findAll();
      

        return $this->render('main/allTricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @route("/trick/addTrick", name="app_add_trick") 
     */
    public function addTrick(Request $request)
    {
        $trick = new Trick;
        dd($trick);
        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $trick->setCreatedAt(new \dateTime());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('message', 'You added a new trick !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('main/addTrick.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @route("/trick/{slug}", name="app_trick") 
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
     * @route("/trick/edit/{id}", name="app_trick_edit") 
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
