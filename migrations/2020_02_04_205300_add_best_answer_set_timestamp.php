<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('discussions', 'best_answer_set_at')) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->dateTime('best_answer_set_at')->nullable();
                $table->index('best_answer_set_at');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->table('discussions', function (Blueprint $table) {
            $table->dropIndex(['best_answer_set_at']);
            $table->dropColumn('best_answer_set_at');
        });
    },
];
