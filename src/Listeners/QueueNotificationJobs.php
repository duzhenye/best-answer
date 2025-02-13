<?php

namespace Duzhenye\BestAnswer\Listeners;

use Duzhenye\BestAnswer\Events\BestAnswerSet;
use Duzhenye\BestAnswer\Jobs;
use Illuminate\Contracts\Queue\Queue;

class QueueNotificationJobs
{
    /**
     * @var Queue
     */
    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(BestAnswerSet $event)
    {
        $this->queue->push(
            new Jobs\SendNotificationWhenBestAnswerSetInDiscussion($event->discussion, $event->actor)
        );
    }
}
