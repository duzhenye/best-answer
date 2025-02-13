<?php

namespace Duzhenye\BestAnswer\Api;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use Duzhenye\BestAnswer\Repository\BestAnswerRepository;

class UserBestAnswerCount
{
    /**
     * @var BestAnswerRepository
     */
    public $bestAnswers;

    public function __construct(BestAnswerRepository $bestAnswers)
    {
        $this->bestAnswers = $bestAnswers;
    }

    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        $attributes['bestAnswerCount'] = $user->best_answer_count ?? $this->bestAnswers->calculateBestAnswersForUser($user);

        return $attributes;
    }
}
