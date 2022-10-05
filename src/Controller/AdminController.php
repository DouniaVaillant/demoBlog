<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/articles', name: 'admin_articles')]
    public function adminArticles(ArticleRepository $repo, EntityManagerInterface $manager): Response
    {

        /// Le manager permet de récupérer le nom des champs d'une table

        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();

        // dd($colonnes);
        /// dump & die : dump puis tue le reste du script

        $articles = $repo->findAll();

        return $this->render('admin/admin_articles.html.twig', [
            'articles' => $articles,
            'colonnes' => $colonnes
        ]);
    }

    #[Route('/admin/articles/new', name: 'admin_new_articles')]
    #[Route('/admin/articles/edit/{id}', name: 'admin_edit_articles')]
    public function formArticle(Request $globals, EntityManagerInterface $manager, Article $article = null)
    {

        /// La classe Request contient les données véhiculées par les superglobales ($_POST, $_GET, $_SERVER...)
        if ($article == null) {
            $article = new Article; /// Je crée n objet de la classe Article vide prêt à être rempli
            /// Si $article est null, nous somme dans la route blog_create : nous devons créer un nouvel article
            /// Sinon, n$article n'est pas null nous somme donc dans la route bloc_edit : nous récupérons l'article correspondant à l'id
        }
        $form = $this->createForm(ArticleType::class, $article); /// Je lie le formulaire à mon objet $article
        /// CreatedForm() permet de récupérer un formulaire
        $form->handleRequest($globals);
        /// dump($globals); /// Permet d'afficher les données de l'objet $globals (comme var_dump())

        /// dump($article);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTime); /// Ajout de la date seulement à l'insertion d'un article
            $manager->persist($article); /// Prépare l'insertion de l'article en bdd
            $manager->flush(); /// Exécute la requête d'insertion 
            $this->addFlash('success', "L'article a bien été enregistré");
            /// addFlash() permet de créer un ms qui sera affiché une fois à l'utilisateur
            /// arg 1 : type du msg (tout ce qu'on veut)
            /// arg 2 : contenu du message


            return $this->redirectToRoute('admin_articles', [
                'id' => $article->getId()
            ]);
            ///Cette méthode permet de nos rediriger vers la page de notre article rnouvellement crée
        }


        return $this->renderForm("admin/form_article.html.twig", [
            'formArticle' => $form,
            'editMode' => $article->getId() !== null
            /// Si nous somme sur la route /new : editMode = 0
            /// Sinon, editMode = 1
        ]);
    }


    #[Route('/admin/article/delete/{id}', name: 'admin_delete_article')]
    public function deleteArticle(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', "L'article a bien été supprimé !");

        return $this->redirectToRoute('admin_articles');
    }
}
