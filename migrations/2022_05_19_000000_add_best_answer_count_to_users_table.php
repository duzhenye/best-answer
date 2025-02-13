<?php

use Flarum\Database\Migration;

return Migration::addColumns('users', [
    'best_answer_count' => ['integer', 'unsigned' => true, 'default' => null, 'nullable' => true],
]);
