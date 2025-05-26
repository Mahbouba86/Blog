<?php

namespace App\Controller;

use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
final class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $ar,
        private EntityManagerInterface $em
    ) {}

    // Route "/articles" menant à la liste des articles
    #[Route('s', name: 'articles', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->ar->findBy(['is_published' => true, 'is_archived' => false], ['id' => 'DESC']);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('article/index.html.twig', [
            'articles' => $pagination
        ]);
    }

    // Route "/article" menant à un article
    #[Route('/{slug}', name: 'article', methods: ['GET'])]
    public function view(string $slug): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) {
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        return $this->render('article/view.html.twig', [
            'article' => $article
        ]);
    }

    // Route "/article/{slug}/edit" menant à la modification d'un article
    #[Route('/{slug}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, Request $request): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) {
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        $form = $this->createForm(ArticleForm::class, $article); // Mise en place du formulaire
        $form->handleRequest($request); // Traitement de la requête

        if ($form->isSubmitted() && $form->isValid()) // Si le form est soumis et valide
        {
            try {
                $this->em->persist($article); // Enregistrement de l'article (query SQL)
                $this->em->flush($article); // Exécution de l'enregistrement en BDD
                $this->addFlash('success', 'Modification bien prise en compte'); // Message Flash Success
            } catch (\Throwable $th) {
                $this->addFlash('error', 'La modification a rencontré une erreur'); // Message Flash Error
            }

            // Redirection vers l'article modifié
            return $this->redirectToRoute('article', ['slug' => $slug]);
        }

        return $this->render('article/edit.html.twig', [
            'articleForm' => $form, // Envoi du formulaire à la vue
            'article' => $article
        ]);
    }

    // Route "/article/{slug}/publish" pour publier un article
    #[Route('/{slug}/publish', name: 'article_publish', methods: ['GET'])]
    public function publish(string $slug): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) { // Ce sera ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        if ($article->isPublished()) { // Si l'article est déjà publié
            $article->setIsPublished(false); // On le met en brouillon
        } else { // Sinon
            $article->setIsPublished(true); // On le met en public
        }

        $this->em->persist($article); // Enregistrement de l'article (query SQL)
        $this->em->flush($article); // Exécution de l'enregistrement en BDD

        // On créer un message flash
        $this->addFlash('success', $article->isPublished() ? "Article publié" : "Mis en brouillon");

        // On redirige l'utilisateur vers l'article
        return $this->redirectToRoute('article', ['slug' => $slug]);
    }

    // Route "/article/{slug}/archive" pour publier un article
    #[Route('/{slug}/archive', name: 'article_archive', methods: ['GET'])]
    public function archive(string $slug): Response
    {

        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article
       if (!$article) {
         $this->addFlash('error', "L'article n'existe pas");
       return $this->redirectToRoute('articles');}  //Vérifier que l'article existe
       if ($article->isArchived()) { // Si l'article est déjà archivé
      $article->setIsArchived(false); // On le désarchive
     } else { // Sinon
      $article->setIsArchived(true); } //Vérifier que l'article est archivé
        $this->em->persist($article);
        $this->em->flush();
        $this->addFlash('success', $article->isArchived() ? 'Article archivé' : 'Article désarchivé');
        $this->addFlash('echec', $article->isArchived() ? 'Article désarchivé' : 'Article archivé');
        return $this->redirectToRoute('article', ['slug' => $slug]);

    }
}