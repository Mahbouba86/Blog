<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
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
    public function index(
        ArticleRepository $ar, // Repository de l'entité Article
        PaginatorInterface $paginator, // Classe pour la fonctionnalité de pagination
        Request $request // Classe pour récupérer les paramètres de la requête HTTP
    ): Response {
        $all = $ar->findBy(
            [
                'is_published' => true,
                'is_archived' => false,
            ],
            ['id' => 'DESC'],
        );
        $pagination = $paginator->paginate(
            $all,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('article/index.html.twig', [
            'articles' => $pagination
        ]);
    }

    // Route "/article/slug" menant à la page d'un article
    #[Route('/{slug}', name: 'article', methods: ['GET'])]
    public function view(string $slug): Response
    {
        return $this->render('article/view.html.twig', [
            'article' => $this->ar->findOneBySlug($slug)
        ]);
    }

    // Route "/article/slug/edit" menant à la page de modification d'un article
    #[Route('/{slug}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {
        return $this->render('article/edit.html.twig', [
            // 'article' => $article
        ]);
    }

    // Route "/article/slug/delete" de suppression d'un article
    #[Route('/{slug}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete(): Response
    {
        return $this->redirectToRoute('articles');
    }
}