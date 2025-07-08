<?php

namespace App\Http\Livewire\Sytatsu\Components\Product;

use App\Enums\CarouselTypeEnum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Carousel extends Component
{
    public CarouselTypeEnum $carouselType;

    public Product|ProductVariant $product;

    /** @var \Illuminate\Support\Collection<Media> $images */
    public Collection $images;

    /**
     * @param \Lunar\Models\Product|\Lunar\Models\ProductVariant $product
     * @param \Illuminate\Support\Collection                     $images
     * @param CarouselTypeEnum|null                              $carouselType
     *
     * @return void
     */
    public function mount(Product|ProductVariant $product, Collection $images, $carouselType = null): void
    {
        $this->product = $product;
        $this->images = $images->sortByDesc(fn (Media $image) => $image->custom_properties['primary']);
        $this->carouselType = $carouselType instanceof CarouselTypeEnum
            ? $carouselType
            : CarouselTypeEnum::COMPACT;
    }

    public function render(): Factory|View|Application
    {
        return view('sytatsu.components.product.carousel', [
            'product' => $this->product,
            'images' => $this->images,
            'carouselType' => $this->carouselType,
        ]);
    }
}
