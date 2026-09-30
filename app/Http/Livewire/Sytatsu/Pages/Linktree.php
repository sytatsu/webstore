<?php

namespace App\Http\Livewire\Sytatsu\Pages;

use App\Http\Livewire\Sytatsu\SytatsuBasePage;

class Linktree extends SytatsuBasePage
{
    protected string $view = 'sytatsu.linktree';
    protected ?string $title = 'Links';
    protected ?string $description = 'All Sytatsu links in one place: webshop, Instagram and Facebook.';

    // Standalone single-page layout with no navigation/footer — see the layout file itself
    // for why, and routes/sytatsu.php + public/robots.txt for how this page is kept unindexed.
    protected string $layout = 'layouts.sytatsu-linktree-layout';
}
