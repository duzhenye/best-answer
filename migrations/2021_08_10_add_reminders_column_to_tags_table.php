<?php

use Flarum\Database\Migration;

return Migration::addColumns('tags', [
    'qna_reminders' => ['boolean', 'default' => false, 'nullable' => false],
]);
