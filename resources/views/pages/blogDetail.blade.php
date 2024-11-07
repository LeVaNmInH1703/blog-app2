@extends('layout.app')
@section('content-app')
    <div class="container col-md-8 col-sm-8">
        <div class="blog_container obj_container"><x-blog-component :blog=$blog :feelings=$feelings /></div>
        <div id="form_container">
            <img src="#" alt="Preview" id="filePreview" hidden class="img-thumbnail mb-2">
            <form action="/create-comment/{{ $blog->id }}" method="POST" id="form_rep" enctype="multipart/form-data"
                class="form_comment" onsubmit="handleFormCommentSubmit(event)">
                @csrf
                <div>
                    <label for="input-file" style="margin: 4px 8px 4px 4px"><i style="font-size: 29px"
                            class="fa-regular fa-image"></i></label>
                    <input name='fileImage' type="file" id="input-file" hidden accept="image/*">
                    <input name="content" required type="text" class="content"
                        placeholder="{{ __('public.Write a comment...') }}">
                </div>
                <button class="btn btn-primary btn-submit" type="submit">{{ __('public.Send') }}</button>
            </form>
        </div>
        <div class="comment_container obj_container">
            @foreach ($blog->comments as $comment)
                @if ($comment->replyCommentDetail == null)
                    <x-comment-component :comment=$comment :feelings=$feelings />
                @endif
            @endforeach
        </div>
    </div>
@section('style-app')
    <style>
        .container {}

        .comment_container {
            margin: 10px 0px;
        }

        #form_container {
            margin: 10px 0px;
            border: 1px solid #fff;
            border-radius: 10px;
            padding: 10px;
        }

        #form_rep,
        .form_comment {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;

            & .content {

                padding-left: 8px;
                flex: 1;
                background: transparent;
                color: inherit;
                line-height: 18px;
                outline: none;
                border: none;
                border-left: 1px solid #fff;

            }

            & label {
                cursor: pointer;
            }

            & #input-file {

                display: none;
            }

            & button {}

            & div {
                flex: 1;
                display: flex;
            }
        }
    </style>

    @yield('style-comment-partial-view')
@endsection
@section('script-app')
    <script src="{{ asset('js/feelingAction.js') }}"></script>
    <script src="{{ asset('js/comment/toggleCommentChild.js') }}"></script>
    <script src="{{ asset('js/comment/repAction.js') }}"></script>
    <script src="{{ asset('js/comment/submitFormCreate.js') }}"></script>
    <script src="{{ asset('js/previewImageOnChange.js') }}"></script>
@endsection

@endsection
