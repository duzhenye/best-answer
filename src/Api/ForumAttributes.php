<?php

namespace Duzhenye\BestAnswer\Api;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Settings\SettingsRepositoryInterface;

class ForumAttributes
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(ForumSerializer $serializer, $model, array $attributes): array
    {
        if ($value = $this->getBooleanSetting('duzhenye-best-answer.search.solution_search')) {
            $attributes['showTagsInSearchResults'] = $this->getBooleanSetting('duzhenye-best-answer.search.display_tags');
            $attributes['removeSolutionResultsFromMainSearch'] = $this->getBooleanSetting('duzhenye-best-answer.search.remove_solutions_from_main_search');
        }

        $attributes['solutionSearchEnabled'] = $value;
        $attributes['canSelectBestAnswerOwnPost'] = $this->getBooleanSetting('duzhenye-best-answer.allow_select_own_post');
        $attributes['useAlternativeBestAnswerUi'] = $this->getBooleanSetting('duzhenye-best-answer.use_alternative_ui');
        $attributes['showBestAnswerFilterUi'] = $this->getBooleanSetting('duzhenye-best-answer.show_filter_dropdown');
        $attributes['bestAnswerDiscussionSidebarJumpButton'] = $this->getBooleanSetting('duzhenye-best-answer.discussion_sidebar_jump_button');

        return $attributes;
    }

    protected function getBooleanSetting(string $key): bool
    {
        return (bool) $this->settings->get($key);
    }
}
