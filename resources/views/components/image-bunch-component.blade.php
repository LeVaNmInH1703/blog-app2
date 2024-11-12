<div class="img-bunch">
    <div class="row">
        @if (isset($images))
            @if ($images->count() == 1)
                <div class="col-lg-6 col-md-6 col-sm-6">

                    <x-image-item-component :image="$images->get(0)" />
                </div>
            @elseif ($images->count() == 2)
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-image-item-component :image="$images->get(0)" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-image-item-component :image="$images->get(1)" />
                </div>
            @elseif ($images->count() == 3)
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <x-image-item-component :image="$images->get(0)" />

                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <x-image-item-component :image="$images->get(1)" />
                    <x-image-item-component :image="$images->get(2)" />
                </div>
            @elseif ($images->count() == 4)
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-image-item-component :image="$images->get(0)" />
                    <x-image-item-component :image="$images->get(1)" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-image-item-component :image="$images->get(2)" />
                    <x-image-item-component :image="$images->get(3)" />
                </div>
            @elseif ($images->count() >= 5)
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-image-item-component :image="$images->get(0)" />
                    <x-image-item-component :image="$images->get(1)" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-image-item-component :image="$images->get(2)" />
                    <x-image-item-component :image="$images->get(3)" />
                    <x-image-item-component :image="$images->get(4)" :morePhotos="$images->count() - 5" />
                </div>
            @endif
        @endif
    </div>
</div>
