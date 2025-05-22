<?php

namespace App\Controller;

use App\Dto\PostRequestDto;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\PostsPaginator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly PostsPaginator $postsPaginator,
    ) {
    }

    #[Route('/posts', name: 'post_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] PostRequestDto $postRequestDto
    ): JsonResponse {
        $post = new Post();
        $post->setTitle($postRequestDto->title)
            ->setContent($postRequestDto->content);

        $this->postRepository->save($post);

        return $this->json($post, Response::HTTP_CREATED);
    }

    #[Route('/posts/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post): JsonResponse
    {
        return $this->json($post, Response::HTTP_OK);
    }

    #[Route('/posts', name: 'post_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $posts = $this->postsPaginator->paginate($page, $limit);

        return $this->json($posts, Response::HTTP_OK);
    }

    #[Route('/posts/{id}', name: 'post_delete', methods: ['DELETE'])]
    public function delete(Post $post): JsonResponse
    {
        $this->postRepository->delete($post);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/posts/{id}', name: 'post_update', methods: ['PUT'])]
    public function update(
        Post $post,
        #[MapRequestPayload] PostRequestDto $postRequestDto
    ): JsonResponse {
        $post->setTitle($postRequestDto->title)
            ->setContent($postRequestDto->content)
            ->setUpdatedAt(new DateTimeImmutable());

        $this->postRepository->save($post);

        return $this->json($post, Response::HTTP_OK);
    }
}
