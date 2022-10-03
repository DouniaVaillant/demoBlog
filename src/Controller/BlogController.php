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

class BlogController extends AbstractController
{
    

    #[Route('/blog/show/{id}', name:'blog_show')]
    public function show($id, ArticleRepository $repo)   //$id correspond à {id} dans l'URL
    {
        $articles = $repo->find($id);
        //find() permet de récupérer un article en fonction de son id

        return $this->render('blog/show.html.twig', [
            'item' => $articles
        ]);
        
    }


    #[Route('/', name: 'home')]

    public function home ()
    {
        return $this->render('blog/home.html.twig', [
            'slogan' => "La démo d'un Blog",
            'age' => 28
            // pour envoyer des variables depuis le controller, la méthode render() prend en 2ème argument un tableau associatif
        ]);
    }
    
    #[Route('/blog', name: 'app_blog')]
    // Une route est définie par 2 arguments : son chemin (/blog) et son nom(app_blog)
    // Allez sur une route permet de lancer la méthode qui se trouve directement en-dessous

    // Les méthodes d'un controller renvoient TOUJOURS un objet de classe Reponse
    public function index(ArticleRepository $repo): Response
    {
        // pour récipérer le repository, je le passe en arg de la méthode index()
        // Cela s'appelle une injection de dépendance

        $article =$repo->findAll();
        // j'utilise findAll() por récupérer tous les articles
        return $this->render('blog/index.html.twig', [
            'article' => $article, // j'envoie les articles au template
        ]);
        
        // render() permet d'afficher le contenu d'un template
    }

    #[Route("/blog/new", name: "blog_create")]
    #[Route("/blog/edit/{id}", name:"blog_edit")]

    public function form (Request $globals, EntityManagerInterface $manager, Article $article = null)
    {
        //la classe Request contient les données véhiculées par les superglobales ($_POST, $_GET, $_SERVER...)
        if($article==null)
        {
            $article = new Article; // je crée n objet de la classe Article vide prêt à être rempli


            //Si $article est null, nous somme dans la route blog_create : nous devons créer un nouvel article
            //sinon, n$article n'est pas null nous somme donc dans la route bloc_edit : nous récupérons l'article correspondant à l'id
        }
        $form = $this->createForm(ArticleType::class, $article); // Je lie le formulaire à mon objet $article
        //createdForm() permet de récupérer un formulaire

        $form->handleRequest($globals);

        //dump($globals); // permet d'afficher les données de l'objet $globals (comme var_dump())
       // dump($article);

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new \DateTime); //ajout de la date seulement à l'insertion d'un article
            $manager->persist($article); //prépare l'insertion de l'article en bdd
            $manager->flush(); // exécute la requête d'insertion 
            
            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()            
            ]);
            //cette méthode permet de nos rediriger vers la page de notre article rnouvellement crée
        }

        return $this->renderForm("blog/form.html.twig", [
            'formArticle' => $form,
            'editMode' => $article -> getId() !==null

            // si nous somme sur la route /new : editMode = 0
            //sinon, editMode = 1
        ]);
    }

    #[Route("/blog/delete/{id}", name:"blog_delete")]

    public function delete($id,EntityManagerInterface $manager, ArticleRepository $repo)
    {
        $article = $repo->find($id);

        $manager->remove($article); // prépare la suppression
        $manager->flush(); //exécute la suppression
        
        return $this->redirectToRoute('app_blog'); // Redirection vers la liste des articles
    }

}

