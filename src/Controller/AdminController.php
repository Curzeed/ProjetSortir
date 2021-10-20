<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CsvType;
use App\Form\ParticipantType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{

    #[Route('/admin', name: 'admin_utilisateur')]
    public function pouvoirAdmin(ParticipantRepository $pr,UserPasswordHasherInterface $userPasswordHasherInterface, Request $request)
    {

        $users = $pr->findAll();

        $participant = new Participant();
        $participant->setRoles(["ROLE_USER"]);
        $participant->setActif(true);
        $form = $this->createForm(RegistrationFormType::class , $participant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $participant,
                    $form->get('plainPassword')->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();
            return $this->redirectToRoute('admin_utilisateur');
        }
        return $this->render('admin/pouvoirAdmin.html.twig',['users'=>$users,
            "form"=> $form->createView()
            ]);

        }

    /**
     * @Route ("/admin/setActivity/{id}" , name="admin_setActivity")
     * @IsGranted("ROLE_ADMIN")
     */
    public function setActivity(Participant $id,EntityManagerInterface $em ){
        if($id->getActif() == 0){
            $id->setActif(1);
        }else{
            $id->setActif(0);
        }

        $em->flush();

        return $this->redirectToRoute('admin_utilisateur');
    }
    /**
     * @Route ("/admin/delete/{id}" , name="admin_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteUser (Participant $id,EntityManagerInterface $em){
        $em->remove($id);
        $em->flush();

        return $this-> redirectToRoute('admin_utilisateur');
    }
    /**
     * @Route ("/admin/role/{id}" , name="admin_role")
     * @IsGranted("ROLE_ADMIN")
     */
    public function role(Participant $p,EntityManagerInterface $em){

        $tab = $p->getRoles();
        if($tab[0] =="ROLE_ADMIN"){
            $p->setRoles(['ROLE_USER']);
        }else{
            $p->setRoles(['ROLE_ADMIN']);
        }

        $em->persist($p);
        $em->flush();
        return $this->redirectToRoute('admin_utilisateur');
    }
    /**
     * @Route ("/admin/csv" , name="admin_role")
     * @IsGranted("ROLE_ADMIN")
     */
    public function readCsv(Request $request, SluggerInterface $slugger){
        $file = '';
        $form = $this->createForm(CsvType::class,$file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newFile = $form->get('file')->getData();
            if ($newFile){
                $originalFileName = pathinfo($newFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$newFile->guessExtension();

                try{
                    $newFile->move(
                        $this->getParameter('csvDirectory'),
                        $newFilename
                    );
                } catch (FileException $e){
                    //
                }
                $file = $newFilename;
            }
            return $this->redirectToRoute('');
        }
    }
}
