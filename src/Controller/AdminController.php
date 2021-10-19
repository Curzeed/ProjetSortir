<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/pouvoirAdmin', name: 'admin_utilisateur')]
    public function pouvoirAdmin(): Response
    {
        $pouvoirAdmin = new participant;
        return $this->render('admin/pouvoirAdmin.html.twig',
            compact('pouvoirAdmin'));
    }
}
