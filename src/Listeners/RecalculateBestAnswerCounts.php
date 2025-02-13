<?php

namespace Duzhenye\BestAnswer\Listeners;

use Flarum\Discussion\Event\Deleting as DiscussionDeleting;
use Flarum\Post\Event\Deleting as PostDeleting;
use Flarum\Post\Post;
use Duzhenye\BestAnswer\Events\BestAnswerSet;
use Duzhenye\BestAnswer\Events\BestAnswerUnset;
use Illuminate\Contracts\Events\Dispatcher;

class RecalculateBestAnswerCounts
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(BestAnswerSet::class, [$this, 'addBestAnswerCount']);
        $events->listen(BestAnswerUnset::class, [$this, 'subtractBestAnswerCount']);
        $events->listen(PostDeleting::class, [$this, 'handlePostDeletion']);
        $events->listen(DiscussionDeleting::class, [$this, 'handleDiscussionDeletion']);
    }

    public function addBestAnswerCount(BestAnswerSet $event): void
    {
        $author = $event->post->user;

        if ($author) {
            $author->best_answer_count++;
            $author->save();
        }
    }

    public function subtractBestAnswerCount(BestAnswerUnset $event): void
    {
        $author = $event->post->user;

        if ($author && $author->best_answer_count && $author->best_answer_count > 0) {
            $author->best_answer_count--;
            $author->save();
        }
    }

    public function handlePostDeletion(PostDeleting $event): void
    {
        $post = $event->post;

        if ($post->discussion->best_answer_post_id === $post->id) {
            $post->discussion->best_answer_post_id = null;
            $post->discussion->best_answer_user_id = null;
            $post->discussion->best_answer_set_at = null;
            $post->discussion->save();

            $author = $post->user;

            if ($author) {
                $author->best_answer_count--;
                $author->save();
            }
        }
    }

    public function handleDiscussionDeletion(DiscussionDeleting $event): void
    {
        $discussion = $event->discussion;

        if ($discussion->best_answer_post_id) {
            $post = Post::find($discussion->best_answer_post_id);
            $author = $post->user;

            if ($author) {
                $author->best_answer_count--;
                $author->save();
            }
        }
    }
}
