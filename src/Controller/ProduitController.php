<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        // Connexion à la BDD
        $pdo = $this->getDoctrine()->getManager();

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        // Analyse la requete HTTP
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $pdo->persist($produit);    // prepare
            $pdo->flush();              // execute
            $this->addFlash("success", "Produit ajouté");
        }

        $produits = $pdo->getRepository(Produit::class)->findAll();
        /*
            ->findOneBy(['id' => 2])
            ->findBy(['nom' => "Nom de l'élément"])
        */

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form_ajout' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_edit")
     */
    public function produit(Produit $produit=null, Request $request){
        
        if($produit != null){
            // Si produit existe, on l'affiche
            $form = $this->createForm(ProduitType::class, $produit);
            // Analyse la requete HTTP
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($produit);    // prepare
                $pdo->flush();              // execute
                $this->addFlash("success", "Produit mis à jour");
            }

            return $this->render('produit/produit.html.twig', [
                'produit'=> $produit,
                'form_edit'=>$form->createView()
            ]);

        }
        else{
            // Produit n'existe pas, on redirige l'internaute
            $this->addFlash("danger", "Produit introuvable");
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/produit/delete/{id}", name="delete_produit")
     */
    public function delete(Produit $produit=null){
        if($produit != null){
            // On a trouvé un produit, on le supprime
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($produit);
            $pdo->flush();
            $this->addFlash("success", "Produit supprimé");
        }
        else{
            $this->addFlash("danger", "Produit introuvable");
        }

        return $this->redirectToRoute('home');
    }
}
