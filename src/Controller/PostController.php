<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\PostsPaginator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly PostRepository      $postRepository,
        private readonly PostsPaginator      $postsPaginator,
        private readonly ValidatorInterface  $validator,
    )
    {
    }

    #[Route('/posts', name: 'post_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $post = $this->serializer->deserialize($request->getContent(), Post::class, 'json');
        $errors = $this->validator->validate($post);

        if (count($errors) > 0) {
            $formattedErrors = $this->formatValidationErrors($errors);

            return new JsonResponse(['errors' => $formattedErrors], 400);
        }

        $this->postRepository->save($post);

        return new JsonResponse($this->serializer->serialize($post, 'json'), 200, [], true);
    }

    #[Route('/posts/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($post, 'json'), 200, [], true);
    }

    #[Route('/posts', name: 'post_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $posts = $this->postsPaginator->paginate($page, $limit);

        return new JsonResponse($this->serializer->serialize($posts, 'json'), 200, [], true);
    }

    #[Route('/posts/{id}', name: 'post_delete', methods: ['DELETE'])]
    public function delete(Post $post): JsonResponse
    {
        $this->postRepository->delete($post);

        return new JsonResponse(null, 204);
    }

    #[Route('/posts/{id}', name: 'post_update', methods: ['PUT'])]
    public function update(Post $post, Request $request): JsonResponse
    {
        $this->serializer->deserialize(
            $request->getContent(),
            Post::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $post,
            ]
        );

        $errors = $this->validator->validate($post);
        if (count($errors) > 0) {
            $formattedErrors = $this->formatValidationErrors($errors);

            return new JsonResponse(['errors' => $formattedErrors], 400);
        }

        $post->setUpdatedAt(new DateTimeImmutable());
        $this->postRepository->save($post);

        return new JsonResponse($this->serializer->serialize($post, 'json'), 200, [], true);
    }

    private function formatValidationErrors($errors): array
    {
        $formattedErrors = [];

        foreach ($errors as $error) {
            /** @var ConstraintViolationInterface $error */
            $formattedErrors[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        return $formattedErrors;
    }

}