<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\UserType;
use App\Entity\Category;
use App\Form\EditTrickType;
use App\Services\DocUploader;
use App\Services\MediaUploader;
use App\Repository\TrickRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TrickController extends AbstractController
{


    public function __construct(TrickRepository $repoTricks)
    {
        $this->repository = $repoTricks;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if ($user && $user->IsVerified() === false) {
            $this->addFlash('success', 'Check your emails to verify your email and enjoy all the fun. ');
        }
        $incrementTricks = 15;
        $start = $request->query->get('showTricks');
        if ($start === NULL) $start = 0;
        else $start = intval($start);
        $completedTricks = [];
        $tricks = $this->repository->showTricks($start + $incrementTricks);
        foreach ($tricks as $key => $value) {
            $medias = $this->getDoctrine()->getRepository(Media::class)->findBy(['trick' => $value->getId()]);
            if (count($medias) === 0) {
                $url = "https://ridestoremagazine.imgix.net/http%3A%2F%2Fwordpress-604950-1959020.cloudwaysapps.com%2Fwp-content%2Fuploads%2F2021%2F04%2Ftrick-tip-how-to-carve-on-a-snowboard-ridestore-magazine.jpg?ixlib=gatsbySourceUrl-1.6.9&auto=format%2Ccompress&crop=faces%2Centropy&fit=crop&w=689&h=689&s=868b5103de49cfc6e17654a07f04dd4e";
            }
            else {$url = "/uploads/".$medias[0]->getFilename();}
            $trickResume = [
                "id" => $value->getId(),
                "name" =>$value->getName(),
                "slug"=>$value->getSlug(),
                "media" => $url
            ];
            array_push($completedTricks, $trickResume);
        }
        $allTricksQty = count($this->repository->findAll());
        // $media = $this->getDoctrine()->getRepository(Media::class)->findAll();
        return $this->render('main/homePage.html.twig', [
            'controller_name' => 'MainController',
            'current_menu' => 'home',
            'tricks' => $completedTricks,
            'allTricksQty' => $allTricksQty,
            // 'medias' => $media
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
    public function addTrick(Request $request, SluggerInterface $slugger)
    {
        $trick = new Trick;

        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $files = $form->get('media')->getData();

            $trick->setCreatedAt(new \dateTime());
            if ($files) {
                foreach ($files as $file) {

                    $newFile = new MediaUploader('uploads', $slugger, $file);
                    $solution = $newFile->add();
                    $media = new Media;
                    $media->setFilename($solution[0]);
                    $media->setType($solution[1]);
                    $media->setUploadAt(new DateTime());
                    $media->setTrick($trick);
                    $em->persist($media);
                }
            } else {
                $media = new Media;
                $media->setFilename("https://ridestoremagazine.imgix.net/http%3A%2F%2Fwordpress-604950-1959020.cloudwaysapps.com%2Fwp-content%2Fuploads%2F2021%2F04%2Ftrick-tip-how-to-carve-on-a-snowboard-ridestore-magazine.jpg?ixlib=gatsbySourceUrl-1.6.9&auto=format%2Ccompress&crop=faces%2Centropy&fit=crop&w=689&h=689&s=868b5103de49cfc6e17654a07f04dd4e");
                $media->setType("default");
            }
            $em->persist($trick);
            $em->flush();
            $this->addFlash('success', 'You added a new trick !');
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
    public function editTrick(int $id, Request $request, SluggerInterface $slugger)
    {

        $trick = $this->repository->findOneBy(['id' => $id]);
        $idCategory = $trick->getCategory();
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $files = $form->get('media')->getData();
            if ($files) {
                foreach ($files as $file) {

                    $newFile = new MediaUploader('uploads', $slugger, $file);
                    $solution = $newFile->add();
                    $media = new Media;
                    $media->setFilename($solution[0]);
                    $media->setType($solution[1]);
                    $media->setUploadAt(new DateTime());
                    $media->setTrick($trick);
                    $em->persist($media);
                }
            }
            // $media->setUploadAt(new DateTime());
            // $media->setTrick($trick);
            // $em->persist($media);


            $trick->setUpdateAt(new \dateTime());
            $em->flush();
            $this->addFlash('success', 'This trick has been updated ');
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
        $medias = $this->getDoctrine()->getRepository(Media::class)->findBy(['trick' => $trick->getId()]);
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id'=>$trick->getCategory()]);
        return $this->render('main/trick.html.twig', [
            'trick' => $trick,
            'medias'=>$medias,
            'category'=>$category
        ]);
    }

    /**
     * @route("profil/editProfil/{id}",name="app_editProfil")
     */
    public function editProfil(Request $request, int $id, SluggerInterface $slugger)
    {

        $id = $id;
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $id]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $avatarfile = $form->get('avatar')->getData();
            if ($avatarfile) {
                $originalFilename = pathinfo($avatarfile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarfile->guessExtension();

                try {
                    $avatarfile->move(
                        $this->getParameter('media_directory'),
                        $newFilename
                    );
                    $em = $this->getDoctrine()->getManager();
                    $user->setAvatar($newFilename);
                    $em->flush();
                } catch (FileException $e) {
                    $this->addFlash('error', 'Something went wrong');
                }
            }
            $em = $this->getDoctrine()->getManager();
            $user->setAvatar($newFilename);
            $em->flush();
            $this->addFlash('success', 'You did great !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('main/editProfil.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }
}
