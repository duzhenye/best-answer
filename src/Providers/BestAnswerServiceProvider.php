<?php

namespace Duzhenye\BestAnswer\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Duzhenye\BestAnswer\Repository\BestAnswerRepository;

class BestAnswerServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->bind(BestAnswerRepository::class);
    }
}
