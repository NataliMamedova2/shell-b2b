<?php

namespace App\Feedback\Domain\Feedback\UseCase\Create;

use App\Application\Domain\ValueObject\Email;

use App\Feedback\Domain\Feedback\Feedback;
use App\Feedback\Domain\Feedback\ValueObject\Comment;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackId;
use App\Feedback\Domain\Feedback\ValueObject\FullName;
use Doctrine\Common\Persistence\ObjectManager;
use Infrastructure\Interfaces\Repository\Repository;

final class Handler implements \Domain\Interfaces\Handler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(
        Repository $repository,
        ObjectManager $entityManager
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function handle(HandlerRequest $handlerRequest)
    {
        $entity = Feedback::create(
            FeedbackId::next(),
            new Email($handlerRequest->email),
            $handlerRequest->user,
            new FullName($handlerRequest->name),
            new FeedbackCategory($handlerRequest->category),
            new Comment($handlerRequest->comment)
        );

        $this->repository->add($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
