<?php

namespace App\Controller;

use DateTime;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TopPictureType;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaController extends AbstractController
{
    public function __construct(MediaRepository $repoMedias)
    {
        $this->repository = $repoMedias;
    }
    /**
     * @route("/profil/media/{id}", name="app_onTop") 
     */
    public function changeOnTop(int $id, Request $request)
    {
        $trick = $this->getDoctrine()->getRepository(Trick::class)->findOneBy(['id' => $id]);
        $medias = $this->repository->findBy(['trick' => $trick->getId()]);
        $form = $this->createForm(TopPictureType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render('main/changeTopPicture.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick, 'medias' => $medias
        ]);
    }
    /**
     * @Route("/profil/deleteTopPicture/{id}", name="app_delete_top")
     */
    public function deletePicture(Request $request, int $id): Response
    {
        $media = $this->repository->findOneBy(['id' => $id]);
        $trick = $this->getDoctrine()->getRepository(Trick::class)->findOneBy(['id' => $media->getTrick()]);
        $allMedias = $this->getDoctrine()->getRepository(Media::class)->findBy(['trick' => $trick->getId()]);
        $images = [];
        $entityManager = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('delete' . $media->getId(), $request->request->get('_token'))) {
            if (!empty($allMedias)) {
                foreach ($allMedias as  $value) {
                    if ($value->getType() === "image") {
                        array_push($images, $value);
                    }
                }
                $topImage = $images[0];
                $topImage->setOnTop(true);
                $entityManager->persist($topImage);
            } else {
                $media = new Media;
                $media->setFilename("http-wordpress-604950-1959020-cloudwaysapps-com-wp-content-uploads-2021-05-trucs-astuces-comment-rider-la-poudreuse-en-snowboard-ridestore-magazine-61ea6f3cd831f.jpg");
                $media->setType("image");
                $media->setUploadAt(new DateTime());
                $media->setTrick($trick);
                $entityManager->persist($media);
            }

            $entityManager->remove($media);
            $entityManager->flush();
            if (empty($allMedias)) {
                $media = new Media;
                $media->setFilename("http-wordpress-604950-1959020-cloudwaysapps-com-wp-content-uploads-2021-05-trucs-astuces-comment-rider-la-poudreuse-en-snowboard-ridestore-magazine-61ea6f3cd831f.jpg");
                $media->setType("image");
                $media->setUploadAt(new DateTime());
                $media->setTrick($trick);
                $media->setOnTop(true);
                $entityManager->persist($media);
                $entityManager->flush();
            }
            $this->addFlash('success', 'The picture has been deleted !');
            return $this->redirectToRoute('app_home');
        }
    }
}
