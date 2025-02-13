<?php

namespace Duzhenye\BestAnswer\Jobs;

use Flarum\Discussion\Discussion;
use Flarum\Notification\NotificationSyncer;
use Flarum\Post\Post;
use Flarum\Queue\AbstractJob;
use Flarum\User\User;
use Duzhenye\BestAnswer\Notification;

class SendNotificationWhenBestAnswerSetInDiscussion extends AbstractJob
{
    /**
     * @var Discussion
     */
    protected $discussion;

    /**
     * @var User
     */
    protected $actor;

    protected $settings;

    public function __construct(
        Discussion $discussion,
        User $actor
    ) {
        $this->discussion = $discussion;
        $this->actor = $actor;
    }

    public function handle(NotificationSyncer $notifications)
    {
        if ($this->discussion === null || $this->discussion->best_answer_post_id === null) {
            return;
        }

        $bestAnswerAuthor = $this->getUserFromPost($this->discussion->best_answer_post_id);

        // Send notification to the post author that has been awarded the best answer, except if the best answer was set by the author
        if ($bestAnswerAuthor && $bestAnswerAuthor->id !== $this->actor->id) {
            $notifications->sync(new Notification\AwardedBestAnswerBlueprint($this->discussion, $this->actor), [$bestAnswerAuthor]);
        }

        // Send notifications to other participants of the discussion
        $recipientsBuilder = User::whereIn('id', Post::select('user_id')->where('discussion_id', $this->discussion->id));

        $exclude = [$this->actor->id];

        if ($bestAnswerAuthor) {
            array_push($exclude, $bestAnswerAuthor->id);
        }

        $recipients = $recipientsBuilder
            ->whereNotIn('id', $exclude)
            ->get();

        $notifications->sync(new Notification\BestAnswerSetInDiscussionBlueprint($this->discussion, $this->actor), $recipients->all());
    }

    public function getUserFromPost(int $post_id): ?User
    {
        return Post::find($post_id)->user;
    }
}
