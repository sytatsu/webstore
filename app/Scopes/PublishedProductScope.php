<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Livewire\Livewire;

class PublishedProductScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($this->isAdminRequest()) {
            return;
        }

        $builder->where($model->getTable().'.status', 'published');
    }

    /**
     * Livewire updates all hit /livewire/update, so for those we look at the
     * page the component was rendered on (memo.path in its checksummed
     * snapshot) instead of exempting every Livewire request — otherwise a
     * storefront update (e.g. collection filters) would expose drafts.
     */
    protected function isAdminRequest(): bool
    {
        $path = Livewire::isLivewireRequest()
            ? ltrim((string) Livewire::originalPath(), '/')
            : Request::path();

        return Str::is(['lunar', 'lunar/*', 'admin', 'admin/*'], $path);
    }
}
