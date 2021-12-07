<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Category;
use App\Form\EditTrickType;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MainController extends AbstractController
{


    public function __construct(TrickRepository $repoTricks)
    {
        $this->repository = $repoTricks;

        // if($this->getUser() && !$this->getUser()->getIsVerified()){
        //     $this->addFlash('message','You need to verify your adress. Check your mails.');
        //     return $this->redirectToRoute('app_logout');
        // }
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if ($user && $user->IsVerified() === false) {
            $this->addFlash('message', 'Check your emails to verify your email and enjoy all the fun. ');
        }
        $incrementTricks = 15;
        $start = $request->query->get('showTricks');
        if ($start === NULL) $start = 0;
        else $start = intval($start);
        $tricks = $this->repository->showTricks($start + $incrementTricks);
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
     * @route("/profil/addTrick", name="app_add_trick") 
     */
    public function addTrick(Request $request)
    {
        $trick = new Trick;
        // $category =$this->getDoctrine()->getRepository(Category::class)->findAll();

        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setCreatedAt(new \dateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();
            $this->addFlash('message', 'You added a new trick !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('main/addTrick.html.twig', [
            'form' => $form->createView(),

        ]);
    }
    /**
     * @Route("/profil/deleteTrick/{id}", name="app_trick_delete")
     */
    public function delete(Request $request, int $id): Response
    {
        $trick = $this->repository->findOneBy(['id' => $id]);
       
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('success', 'The trick has been deleted !');
            return $this->redirectToRoute('app_home');
        }

    }
    /**
     * @route("/profil/edit/{id}", name="app_trick_edit") 
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

    /**
     * @route("/trick/{slug}", name="app_trick") 
     */
    public function showTrick(String $slug)
    {
        $trick = $this->repository->findOneBy(['slug' => $slug]);
        $slug = $trick->getSlug();

        return $this->render('main/trick.html.twig', [
            'trick' => $trick
        ]);
    }
}
