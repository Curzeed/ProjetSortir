<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    #[Route('/admin', name: 'admin_utilisateur')]
    public function pouvoirAdmin(ParticipantRepository $pr,)
    {
        $users = $pr->findAll();
        return $this->render('admin/pouvoirAdmin.html.twig',
            compact('users'));
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
}
