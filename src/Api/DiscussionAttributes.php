<?php

namespace Duzhenye\BestAnswer\Api;

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;
use Duzhenye\BestAnswer\Repository\BestAnswerRepository;

class DiscussionAttributes
{
    /**
     * @var BestAnswerRepository
     */
    protected $bestAnswerRepository;

    public function __construct(BestAnswerRepository $bestAnswerRepository)
    {
        $this->bestAnswerRepository = $bestAnswerRepository;
    }

    public function __invoke(DiscussionSerializer $serializer, Discussion $discussion, array $attributes): array
    {
        $attributes['canSelectBestAnswer'] = $this->bestAnswerRepository->canSelectBestAnswer($serializer->getActor(), $discussion);

        return $attributes;
    }
}
