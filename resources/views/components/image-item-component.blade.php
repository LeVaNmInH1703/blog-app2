<figure>
    <a href="#" title="" data-toggle="modal" data-target="#img-comt">
        <img src="{{ asset((file_exists(public_path('images_resize/' . $image->image_name)) ? 'images_resize/' : 'images/') . $image->file_name) }}" alt="" />
    </a>
    @if($morePhotos>0)
    <div class="more-photos">
        <span>{{ $morePhotos }}</span>
    </div>
    @endif
</figure>