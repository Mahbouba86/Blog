<?php

namespace App\Controller;

use Throwable;
use App\Entity\Article;
use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
final class ArticleController extends AbstractController
{
    /**
     * Le constructeur permet de déclarer les dépendances une fois
     * et d'éviter la non-application du concept DRY (Don't Repeat Yourself)
     */
    public function __construct(
        private ArticleRepository $ar, // Repository de l'entité Article
        private EntityManagerInterface $em // Gestionnaire d'entités avec Doctrine
    ) {}

    // Route "/articles" menant à la liste des articles
    #[Route('s', name: 'articles', methods: ['GET'])]
    public function index(
        PaginatorInterface $paginator, // Classe pour la fonctionnalité de pagination
        Request $request // Classe pour récupérer les paramètres de la requête HTTP
    ): Response {
        $all = $this->ar->findBy(['is_published' => true, 'is_archived' => false]);
        $pagination = $paginator->paginate(
            $all,
            $request->query->getInt('page', 1),
            100
        );

        return $this->render('article/index.html.twig', [
            'controller_name' => 'INDEX',
            'articles' => $pagination
        ]);
    }

    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article); // Mise en place du formulaire
        $form->handleRequest($request);

        // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser()); // Récupération de l'utilisateur
            $this->em->persist($article); // Enregistrement de l'article (query SQL)
            $this->em->flush($article); // Exécution de l'erregistrement en BDD
            $this->addFlash('success', "L'article a été créé."); // Message flash succès
            return $this->redirectToRoute('articles'); // Redirection vers l'article
        }

        return $this->render('article/new.html.twig', [
            'articleForm' => $form // Envoi du formulaire à la vue
        ]);
    }

    // Route menant à un article
    #[Route('/{slug}', name: 'article', methods: ['GET'])]
    public function view(string $slug): Response
    {
        return $this->render('article/view.html.twig', [
            'article' => $this->ar->findOneBySlug($slug)
        ]);
    }

    // Route menant à la modification d'un article
    #[Route('/{slug}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, Request $request): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article
        $form = $this->createForm(ArticleForm::class, $article); // Mise en place du formulaire
        $form->handleRequest($request); // Traitement du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->persist($article); // Enregistrement de l'article (query SQL)
                $this->em->flush($article); // Exécution de l'enregistrement en BDD
                $this->addFlash('success', 'Modification bien prise en compte'); // Message flash (message d'information)
            } catch (\Throwable $th) {
                $this->addFlash('error', 'La modification a rencontré une erreur');
            }
            return $this->redirectToRoute('article', ['slug' => $slug]); // Redirection vers la vue de l'article modifié
        };
        return $this->render('article/edit.html.twig', [
            'article' => $article, // Envoi de l'article à la vue
            'articleForm' => $form // Envoi du formulaire à la vue
        ]);
    }

    // Route pour publier un article
    #[Route('/{slug}/publish', name: 'article_publish', methods: ['GET'])]
    public function publish(string $slug): Response
    {
        $article = $this->ar->findOneBySlug($slug);

        if (!$article) { // Ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        if ($article->isPublished()) {
            $article->setIsPublished(false);
        } else {
            $article->setIsPublished(true);
        }

        $this->em->persist($article);
        $this->em->flush($article);
        $this->addFlash('success', $article->isPublished() ? "Article publié" : "Mis en brouillon");
        return $this->redirectToRoute('article', ['slug' => $slug]);
    }

    // Route pour archiver un article
    #[Route('/{slug}/archive', name: 'article_archive', methods: ['GET'])]
    public function archive(string $slug): Response
    {
        // Récupérer l'article
        $article = $this->ar->findOneBySlug($slug);
        // Vérifier que l'article existe
        if (!$article) { // Ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }
        // Vérifier que l'article est archivé
        if ($article->isArchived()) {
            $article->setIsArchived(false); // Oui : Le désarchiver
        } else {
            $article->setIsArchived(true); // Non : L'archiver
        }
        // Enregistrer les modifications
        $this->em->persist($article);
        $this->em->flush($article);
        // Rediriger vers l'article
        $this->addFlash('success', $article->isArchived() ? "Article archivé" : "Article désarchivé");
        return $this->redirectToRoute('article', ['slug' => $slug]);
    }

    #[Route('/{slug}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete(string $slug): Response
    {
        $article = $this->ar->findOneBySlug($slug);

        if (!$article) { // Ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        $this->em->remove($article);
        $this->em->flush($article);

        $this->addFlash('success', 'Article supprimé avec succès');
        return $this->redirectToRoute('articles');
    }
}