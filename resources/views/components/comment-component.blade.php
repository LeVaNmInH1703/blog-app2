<div class="comment comment_wrap obj_wrap" data-id="{{ $comment->id }}" data-name='comment'>
    <!-- Header -->
    <div class="comment-header">
        <div><img src="{{ $comment->user->url_avatar }}" alt="Avatar" class="avatar">
        </div>
        <div class="mx-2">
            <h5 class="mb-0">{{ $comment->user->id == Auth::id() ? __('public.You') : $comment->user->name }}</h5>
            <p class="mb-0 text-secondary">{{ $comment->content }}</p>
        </div>
    </div>
    @if ($comment->image)
        <!-- Ảnh (nếu có) -->
        <div class="comment-images">
            <img src="{{ asset((file_exists(public_path('images_resize/' . $comment->image->image_comment_name)) ? 'images_resize/' : 'images/') . $comment->image->image_comment_name) }}"
                alt="{{ __('public.Comment image') }}" class="img-fluid">
            <img src="image2.jpg" alt="Comment Image 2" class="img-fluid">
        </div>
    @endif

    <!-- Footer -->
    <div class="comment-footer">
        <span class="time text-secondary">@diffForHumans($comment->created_at)</span>
        <div class="comment_options">
            <button class="btn_like btn text-white btn-sm btn-custom" data-id="{{ $comment->id }}" data-name="comment"
                onmouseleave="handleMouseLeaveButtonLike(event)" onmouseenter="handleMouseEnterButtonLike(event)"
                onclick="handleClickButtonLike(event)">
                <div class="btn_like_text text-center">
                    <x-get-feedback-component :feedback='$comment->clientFeedback' :isShowName=true />
                </div>
            </button>
            <button class="btn btn-custom btn_comment text-white btn-sm" onclick="handleClickToComment(event)">
                <i class="fas fa-reply"></i> {{ __('public.Reply') }}
            </button>
            <!-- Nút xem/bỏ xem comment con -->
            <span class='comment_count'>
                <x-comment-count-comment-component :number="$comment->comments->count()"/>
            </span>
        </div>
        <span class="stats count_feedback">
            <x-count-feedback-component :obj=$comment :size=12 />
        </span>
    </div>
    <!-- Comment con -->
    <div class="comment_children">
        @foreach ($comment->comments as $commentChild)
            <x-comment-component :level={{ ($level ?? 1) + 1 }} :comment=$commentChild />
        @endforeach
    </div>

</div>
@section('style-comment-partial-view')
<style>
    .comment_children {
        display: none;
    }

    .btn_like {
        position: relative;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }

    .comment {
        border-top: 1px solid #666;
        border-left: 1px solid #666;
        background-color: #242527;
        border-radius: 5px;
        padding: 15px;
    }

    .comment_options {
        display: flex;

    }

    .comment+.comment {
        margin-top: 20px;
    }

    .comment-header {
        height: fit-content;
        display: flex;
        align-items: flex-start;
    }

    .comment-footer {
        display: flex;
        justify-content: flex-start;
        /* Căn trái */
        align-items: center;
    }

    .comment-footer>* {
        margin-right: 30px;
        /* Khoảng cách giữa các nút */
    }

    .comment-footer .stats {
        color: #ffffff;
        /* Màu chữ thống kê */
        margin-right: 15px;
        /* Khoảng cách cho thống kê */
    }

    .child-comment {
        margin-left: 20px;
        /* Căn lề cho comment con */
        margin-top: 10px;
        /* Khoảng cách giữa comment con */
    }

    .comment-images {
        margin-top: 10px;
        /* Khoảng cách ảnh với nội dung comment */
    }

    .comment-images img {
        max-width: 100%;
        /* Đảm bảo ảnh không vượt quá chiều rộng */
        border-radius: 5px;
        /* Bo góc ảnh */
        margin-right: 10px;
        /* Khoảng cách giữa các ảnh */
    }

    @media (max-width: 576px) {
        .comment-header {
            flex-direction: column;
            /* Căn theo cột trên màn hình nhỏ */
            align-items: flex-start;
            /* Căn trái */
        }

        .comment-header .name {
            margin-left: 0;
            /* Xóa khoảng cách bên trái */
        }

        .comment-footer {
            flex-direction: column;
            /* Căn theo cột trên màn hình nhỏ */
            align-items: flex-start;
            /* Căn trái */
        }

        .comment-footer .btn {
            margin-right: 0;
            /* Xóa khoảng cách bên phải */
            margin-bottom: 5px;
            /* Khoảng cách giữa các nút */
        }
    }
</style>
@endsection
