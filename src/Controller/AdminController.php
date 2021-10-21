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
    #[IsGranted('ROLE_ADMIN')]
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
        return $this->renderForm('admin/pouvoirAdmin.html.twig',['users'=>$users,
            "form"=>$form
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
    public function readCsv(Request $request){
        $row = 1;
        $form = $this->createForm(CsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newFile = $form->get('file')->getData();
            if ($newFile){
                $originalFileName = pathinfo($newFile->getClientOriginalName(), PATHINFO_FILENAME);
                $originalFileName = fopen($newFile,"r");
                if(($handle = fopen($newFile,"r"))!== false){
                    while (($data = fgetcsv($handle,1000,","))!== false){
                        //Rendu des lignes
                        dd($data);
                        //
                        $row++;
                    }
                    fclose($handle);
                }
            }
            return $this->redirectToRoute('admin_utilisateur');
        }
        return $this->render('admin/ajoutCsv.html.twig',['form'=>$form->createView()]);
    }

}
