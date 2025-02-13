<?php

use Illuminate\Database\Schema\Builder;

$remindersKey = 'duzhenye-best-answer.remind_tag_ids';

return [
    'up' => function (Builder $schema) use ($remindersKey) {
        $reminderIds = $schema->getConnection()
            ->table('settings')
            ->where('key', $remindersKey)
            ->value('value');

        $schema->getConnection()
            ->table('tags')
            ->whereIn('id', explode(',', str_replace(' ', '', $reminderIds)))
            ->update(['is_qna' => true, 'qna_reminders' => true]);

        $schema->getConnection()
            ->table('settings')
            ->where('key', $remindersKey)
            ->delete();
    },
    'down' => function (Builder $schema) use ($remindersKey) {
        $tagIds = $schema->getConnection()
            ->table('tags')
            ->where('qna_reminders', true)
            ->pluck('id');

        $schema->getConnection()
            ->table('settings')
            ->insertOrIgnore([
                'key'   => $remindersKey,
                'value' => $tagIds->implode(','),
            ]);
    },
];
