<?php

use Flarum\Database\Migration;

return Migration::addColumns('tags', [
    'is_qna' => ['boolean', 'default' => false, 'nullable' => false],
]);
