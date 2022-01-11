<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DocUploader;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{


    /**
     * @route("profil/editProfil/{id}",name="app_editProfil")
     */
    public function editProfil(Request $request, int $id, SluggerInterface $slugger)
    {

        $id = $id;
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            try {
                if ( $form->isValid()) {
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
            } catch (\Throwable $th) {
                // throw $th;
                dd($th);
            }
 
        }

        return $this->render('main/editProfil.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }
}
