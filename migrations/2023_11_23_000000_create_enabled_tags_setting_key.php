<?php

use Illuminate\Database\Schema\Builder;

$remindersKey = 'duzhenye-best-answer.remind-tags';
$qnaKey = 'duzhenye-best-answer.enabled-tags';

return [
    'up' => function (Builder $schema) use ($remindersKey, $qnaKey) {
        $reminderTagIds = $schema->getConnection()
            ->table('tags')
            ->where('qna_reminders', true)
            ->pluck('id')
            ->map(function ($id) {
                return (string) $id; // Convert each ID to string
            });

        $qnaTagIds = $schema->getConnection()
            ->table('tags')
            ->where('is_qna', true)
            ->pluck('id')
            ->map(function ($id) {
                return (string) $id; // Convert each ID to string
            });

        // Convert the arrays to JSON strings
        $reminderTagIdsJson = json_encode($reminderTagIds->all());
        $qnaTagIdsJson = json_encode($qnaTagIds->all());

        $schema->getConnection()
            ->table('settings')
            ->insertOrIgnore([
                'key'   => $remindersKey,
                'value' => $reminderTagIdsJson,
            ]);

        $schema->getConnection()
            ->table('settings')
            ->insertOrIgnore([
                'key'   => $qnaKey,
                'value' => $qnaTagIdsJson,
            ]);
    },
    'down' => function (Builder $schema) use ($remindersKey, $qnaKey) {
        // Delete the settings keys
        $schema->getConnection()
            ->table('settings')
            ->whereIn('key', [$remindersKey, $qnaKey])
            ->delete();
    },
];
