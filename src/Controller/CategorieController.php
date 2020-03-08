<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();

        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $pdo->persist($categorie);
            $pdo->flush();

            $this->addFlash("success", "Catégorie ajoutée");
        }

        $categories = $pdo->getRepository(Categorie::class)->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
            "form_ajout" => $form->createView()
        ]);
    }

    /**
     * @Route("/categorie/{id}", name="une_categorie")
     */
    public function categorie(Categorie $categorie=null, Request $request){

        if($categorie != null){
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($categorie);
                $pdo->flush();

                $this->addFlash("success", "Catégorie mise à jour");
            }

            return $this->render('categorie/categorie.html.twig', [
                'categorie' => $categorie,
                'form_edit' => $form->createView()
            ]);
        }
        else{
            $this->addFlash("danger", "Catégorie introuvable");
            return $this->redirectToRoute('categorie');
        }
    }

    /**
     * @Route("/categorie/delete/{id}", name="delete_categorie")
     */
    public function delete(Categorie $categorie=null){
        if($categorie != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($categorie);
            $pdo->flush();

            $this->addFlash("success", "Catégorie supprimée");
        }
        else{
            $this->addFlash("danger", "Catégorie introuvable");
        }

        return $this->redirectToRoute('categorie');
    }
}
