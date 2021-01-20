<?php

namespace Tests\Unit\Feedback\Domain\Feedback;

use App\Application\Domain\ValueObject\Email;
use App\Feedback\Domain\Feedback\Feedback;
use App\Feedback\Domain\Feedback\ValueObject\Comment;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackCategory;
use App\Feedback\Domain\Feedback\ValueObject\FeedbackId;
use App\Feedback\Domain\Feedback\ValueObject\FullName;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Clients\Domain\User\UserTest;

class FeedbackTest extends TestCase
{

    public function testCreate(): void
    {
        $idString = '550e8400-e29b-41d4-a716-446655440000';
        $id = FeedbackId::fromString($idString);
        $name = new FullName('Test name');
        $email = new Email('test@mail.com');
        $user = UserTest::createValidEntity();
        $category = new FeedbackCategory('general-question');
        $comment = new Comment('test comment text');

        $entity = Feedback::create(
            $id,
            $email,
            $user,
            $name,
            $category,
            $comment
        );

        $this->assertEquals($id, $entity->getId());
        $this->assertEquals($email, $entity->getEmail());
        $this->assertEquals($name, $entity->getName());
        $this->assertEquals($category, $entity->getCategory());
        $this->assertEquals($comment, $entity->getComment());
    }
}
