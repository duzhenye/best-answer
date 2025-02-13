<?php

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->getConnection()
            ->table('settings')
            ->insert([
                'key'   => 'duzhenye-best-answer.show_filter_dropdown',
                'value' => true,
            ]);
    },
    'down' => function (Builder $schema) {
        $schema->getConnection()
            ->table('settings')
            ->where('key', 'duzhenye-best-answer.show_filter_dropdown')
            ->delete();
    },
];
