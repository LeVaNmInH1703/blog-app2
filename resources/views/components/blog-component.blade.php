<div class="central-meta item">
    <div class="user-post">
        <div class="friend-info">
            <figure>
                <img src="{{ $blog->user->url_avatar }}" alt="" />
            </figure>
            <div class="friend-name">
                <div class="more">
                    <div class="more-post-optns">
                        <i class="ti-more-alt"></i>
                        <ul>
                            @if ($blog->user->id == Auth::user()->id)
                                <li>
                                    <i class="fa fa-pencil-square-o"></i>Edit
                                    Post
                                </li>
                                <li>
                                    <i class="fa fa-trash-o"></i>Delete Post
                                </li>
                            @endif
                            <li class="bad-report">
                                <i class="fa fa-flag"></i>Report Post
                            </li>
                            <li>
                                <i class="fa fa-address-card-o"></i>Boost
                                This Post
                            </li>
                            <li>
                                <i class="fa fa-clock-o"></i>Schedule Post
                            </li>
                            <li>
                                <i class="fa fa-wpexplorer"></i>Select as
                                featured
                            </li>
                            <li>
                                <i class="fa fa-bell-slash-o"></i>Turn off
                                Notifications
                            </li>
                        </ul>
                    </div>
                </div>
                <ins><a href="time-line.html"
                        title="">{{ $blog->user->id == Auth::id() ? 'You' : $blog->user->name }}</a>
                    Post Album</ins>
                <span><i class="fa fa-globe"></i> published:
                    @diffForHumans($blog->created_at)
                </span>
            </div>
            <div class="post-meta">
                <p>
                    {{ $blog->content }}
                </p>
                {{-- todo: ẩn hiện bản dịch  --}}
                {{-- @if (session('locale') != $blog->getLastDetectedSource)
                    <p>{!! $blog->contentTranslated !!}</p>
                @endif --}}
                <figure>
                    <x-image-bunch-component :images="$blog->images" />
                    {{-- todo: hiện $blog->videos --}}
                    {{-- <ul class="like-dislike">
                        <li>
                            <a class="bg-purple" href="#" title="Save to Pin Post"><i
                                    class="fa fa-thumb-tack"></i></a>
                        </li>
                        <li>
                            <a class="bg-blue" href="#" title="Like Post"><i class="ti-thumb-up"></i></a>
                        </li>
                        <li>
                            <a class="bg-red" href="#" title="dislike Post"><i class="ti-thumb-down"></i></a>
                        </li>
                    </ul> --}}
                </figure>
                <div class="we-video-info">
                    <ul>
                        {{-- <x-count-emoji-component :obj=$blog /> --}}
                        <li>
                            <div class="likes heart {{ $blog->myEmoji ? 'happy' : '' }}" title="Like/Dislike">❤
                                <span>{{ $blog->countEmoji }}</span>
                            </div>
                        </li>
                        <li>
                            <span class="comment" title="Comments">
                                <i class="fa fa-commenting"></i>
                                <ins>{{ $blog->countComment }}</ins>
                            </span>
                        </li>

                        {{-- <li>
                            <span>
                                <a class="share-pst" href="#" title="Share">
                                    <i class="fa fa-share-alt"></i>
                                </a>
                                <ins>0</ins>
                            </span>
                        </li> --}}
                    </ul>
                    <div class="users-thumb-list">
                        @foreach ($blog->emojiDetails as $emojiDetail)
                            <a data-toggle="tooltip" title="{{ $emojiDetail->user->name }}"
                                href="{{ route('profile', $emojiDetail->user->id) }}">
                                <img class="img-custom size-8" alt="{{ $emojiDetail->user->name }}"
                                    src="{{ $emojiDetail->user->url_avatar }}" />
                            </a>
                            @if ($loop->index == 4)
                            @break
                        @endif
                    @endforeach
                    @if ($blog->countEmoji > 0)
                        <span>

                            <b>{{ $blog->emojiDetails[0]->user->name }}</b>
                            @if ($blog->countEmoji > 1)
                                , <b>{{ $blog->emojiDetails[1]->user->name }}</b>
                            @endif
                            @if ($blog->countEmoji > 2)
                                and<a href="#" title="">{{ $blog->countEmoji - 2 }} others</a>
                            @endif
                            liked
                        </span>

                    @endif
                </div>
            </div>
        </div>
        <div class="coment-area" style="display: block">
            <ul class="we-comet">
                @foreach ($blog->comments as $comment)
                    @if ($comment->replyCommentDetail == null)
                        {{-- <x-comment-component :comment=$comment :feelings=$feelings /> --}}
                        <x-comment-component :comment="$comment" />
                    @endif
                @endforeach
                <li>

                    <a href="#" title="" class="showmore underline">more comments+</a>
                </li>
                <li class="post-comment">
                    <div class="comet-avatar">
                        <img src="images/resources/nearly1.jpg" alt="" />
                    </div>
                    <div class="post-comt-box">
                        <form method="post">
                            <textarea placeholder="Post your comment"></textarea>
                            <div class="add-smiles">
                                <div class="uploadimage">
                                    <i class="fa fa-image"></i>
                                    <label class="fileContainer">
                                        <input type="file" />
                                    </label>
                                </div>
                                <span class="em em-expressionless" title="add icon"></span>
                                <div class="smiles-bunch">
                                    <i class="em em---1"></i>
                                    <i class="em em-smiley"></i>
                                    <i class="em em-anguished"></i>
                                    <i class="em em-laughing"></i>
                                    <i class="em em-angry"></i>
                                    <i class="em em-astonished"></i>
                                    <i class="em em-blush"></i>
                                    <i class="em em-disappointed"></i>
                                    <i class="em em-worried"></i>
                                    <i class="em em-kissing_heart"></i>
                                    <i class="em em-rage"></i>
                                    <i class="em em-stuck_out_tongue"></i>
                                </div>
                            </div>

                            <button type="submit"></button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
</div>
<!-- album post -->
