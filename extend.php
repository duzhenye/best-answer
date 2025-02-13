<?php


namespace Duzhenye\BestAnswer;

use Flarum\Api\Controller;
use Flarum\Api\Serializer;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving as DiscussionSaving;
use Flarum\Discussion\Filter\DiscussionFilterer;
use Flarum\Discussion\Search\DiscussionSearcher;
use Flarum\Extend;
use Flarum\Post\Filter\PostFilterer;
use Flarum\Post\Post;
use Flarum\Settings\Event\Saving as SettingsSaving;
use Flarum\Tags\Api\Serializer\TagSerializer;
use Flarum\Tags\Tag;
use Flarum\User\User;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\ServiceProvider())
        ->register(Providers\BestAnswerServiceProvider::class),

    (new Extend\Model(Discussion::class))
        ->belongsTo('bestAnswerPost', Post::class, 'best_answer_post_id')
        ->belongsTo('bestAnswerUser', User::class, 'best_answer_user_id')
        ->cast('best_answer_post_id', 'int')
        ->cast('best_answer_user_id', 'int')
        ->cast('best_answer_set_at', 'datetime')
        ->cast('best_answer_notified', 'boolean'),

    (new Extend\View())
        ->namespace('duzhenye-best-answer', __DIR__.'/resources/views'),

    (new Extend\Model(Tag::class))
        ->cast('is_qna', 'boolean')
        ->cast('qna_reminders', 'boolean'),

    (new Extend\Model(User::class))
        ->cast('best_answer_count', 'int'),

    (new Extend\Event())
        ->listen(DiscussionSaving::class, Listeners\SaveBestAnswerToDatabase::class)
        ->listen(Events\BestAnswerSet::class, Listeners\QueueNotificationJobs::class)
        ->subscribe(Listeners\RecalculateBestAnswerCounts::class)
        ->listen(SettingsSaving::class, Listeners\SaveTagSettings::class),

    (new Extend\Notification())
        ->type(Notification\SelectBestAnswerBlueprint::class, Serializer\BasicDiscussionSerializer::class, ['alert', 'email'])
        ->type(Notification\AwardedBestAnswerBlueprint::class, Serializer\BasicDiscussionSerializer::class, ['alert'])
        ->type(Notification\BestAnswerSetInDiscussionBlueprint::class, Serializer\BasicDiscussionSerializer::class, []),

    (new Extend\ApiSerializer(Serializer\DiscussionSerializer::class))
        ->attributes(Api\DiscussionAttributes::class),

    (new Extend\ApiSerializer(Serializer\BasicDiscussionSerializer::class))
        ->hasOne('bestAnswerPost', Serializer\BasicPostSerializer::class)
        ->hasOne('bestAnswerUser', Serializer\BasicUserSerializer::class)
        ->attributes(Api\BasicDiscussionAttributes::class),

    (new Extend\ApiSerializer(Serializer\UserSerializer::class))
        ->attributes(Api\UserBestAnswerCount::class),

    (new Extend\ApiController(Controller\ListUsersController::class))
        ->addSortField('bestAnswerCount'),

    (new Extend\Settings())
        ->default('duzhenye-best-answer.schedule_on_one_server', false)
        ->default('duzhenye-best-answer.stop_overnight', false)
        ->default('duzhenye-best-answer.store_log_output', false)
        ->default('duzhenye-best-answer.enabled-tags', '[]')
        ->default('duzhenye-best-answer.search.solution_search', true)
        ->default('duzhenye-best-answer.search.remove_solutions_from_main_search', false)
        ->default('duzhenye-best-answer.search.display_tags', true)
        ->default('duzhenye-best-answer.discussion_sidebar_jump_button', false)
        ->serializeToForum('duzhenye-best-answer.show_max_lines', 'duzhenye-best-answer.show_max_lines', 'intVal'),

    (new Extend\ApiSerializer(Serializer\ForumSerializer::class))
        ->attributes(Api\ForumAttributes::class),

    (new Extend\ApiController(Controller\ShowDiscussionController::class))
        ->addInclude(['bestAnswerPost', 'bestAnswerUser', 'bestAnswerPost.user'])
        ->load(['bestAnswerPost', 'bestAnswerPost.user']),

    (new Extend\ApiController(Controller\ListDiscussionsController::class))
        ->addOptionalInclude(['bestAnswerPost', 'bestAnswerUser', 'bestAnswerPost.discussion', 'bestAnswerPost.user']),

    (new Extend\ApiController(Controller\UpdateDiscussionController::class))
        ->addOptionalInclude('tags'),

    (new Extend\ApiController(Controller\ListPostsController::class))
        ->addInclude(['discussion', 'discussion.bestAnswerPost', 'discussion.bestAnswerUser', 'discussion.bestAnswerPost.user'])
        ->load(['discussion', 'discussion.bestAnswerUser', 'discussion.bestAnswerPost', 'discussion.bestAnswerPost.user']),

    (new Extend\ApiController(Controller\ShowPostController::class))
        ->addInclude(['discussion', 'discussion.bestAnswerPost', 'discussion.bestAnswerUser', 'discussion.bestAnswerPost.user'])
        ->load(['discussion', 'discussion.bestAnswerUser', 'discussion.bestAnswerPost', 'discussion.bestAnswerPost.user']),

    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->addGambit(Search\BestAnswerFilterGambit::class),

    (new Extend\Console())
        ->command(Console\NotifyCommand::class)
        ->command(Console\UpdateBestAnswerCounts::class)
        ->schedule(Console\NotifyCommand::class, Console\NotifySchedule::class),

    (new Extend\Filter(DiscussionFilterer::class))
        ->addFilter(Search\BestAnswerFilterGambit::class),

    (new Extend\Filter(PostFilterer::class))
        ->addFilter(Search\BestAnswerPostFilter::class),

    (new Extend\ApiSerializer(TagSerializer::class))
        ->attributes(Api\AddTagAttributes::class),
];
