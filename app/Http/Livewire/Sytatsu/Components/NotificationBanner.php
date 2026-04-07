<?php

namespace App\Http\Livewire\Sytatsu\Components;

use App\Models\NotificationBanner as BannerModel;
use Livewire\Component;

class NotificationBanner extends Component
{
    public $banner;

    public function mount()
    {
        $now = now();
        $this->banner = BannerModel::where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('banner_start_at')
                    ->orWhere('banner_start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('banner_end_at')
                    ->orWhere('banner_end_at', '>=', $now);
            })
            ->first();
    }

    public function render()
    {
        return view('sytatsu.components.notification-banner');
    }
}
