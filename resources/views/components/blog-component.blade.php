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
                            <li>
                                <i class="fa fa-pencil-square-o"></i>Edit
                                Post
                            </li>
                            <li>
                                <i class="fa fa-trash-o"></i>Delete Post
                            </li>
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
                    <x-image-bunch-component :images="$blog->files" />
                    <ul class="like-dislike">
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
                    </ul>
                </figure>
                <div class="we-video-info">
                    <ul>
                        <x-count-feeling-component :obj=$blog />

                        <li>
                            <span class="comment" title="Comments">
                                <i class="fa fa-commenting"></i>
                                <ins>{{ $blog->countComment }}</ins>
                            </span>
                        </li>

                        <li>
                            <span>
                                <a class="share-pst" href="#" title="Share">
                                    <i class="fa fa-share-alt"></i>
                                </a>
                                <ins>0</ins>
                            </span>
                        </li>
                    </ul>
                    <div class="users-thumb-list">
                        @foreach ($blog->feelings as $feelingDetail)
                            <a data-toggle="tooltip" title="{{ $feelingDetail->userName }}" href="{{ route('profile',$feelingDetail->userId) }}">
                                <img alt="{{ $feelingDetail->userName }}" src="{{ $feelingDetail->url_avatar }}" />
                            </a>
                            @if($loop->index == 4) @break @endif
                        @endforeach
                        <span><strong>You</strong>, <b>Sarah</b> and
                            <a href="#" title="">24+ more</a>
                            liked</span>
                    </div>
                </div>
            </div>
            <div class="coment-area" style="display: block">
                <ul class="we-comet">
                    <li>
                        <div class="comet-avatar">
                            <img src="images/resources/nearly3.jpg" alt="" />
                        </div>
                        <div class="we-comment">
                            <h5>
                                <a href="time-line.html" title="">Jason
                                    borne</a>
                            </h5>
                            <p>
                                we are working for the dance and sing
                                songs. this video is very awesome for the
                                youngster. please vote this video and like
                                our channel
                            </p>
                            <div class="inline-itms">
                                <span>1 year ago</span>
                                <a class="we-reply" href="#" title="Reply"><i class="fa fa-reply"></i></a>
                                <a href="#" title=""><i class="fa fa-heart"></i><span>20</span></a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="comet-avatar">
                            <img src="images/resources/comet-4.jpg" alt="" />
                        </div>
                        <div class="we-comment">
                            <h5>
                                <a href="time-line.html" title="">Sophia</a>
                            </h5>
                            <p>
                                we are working for the dance and sing
                                songs. this video is very awesome for the
                                youngster.
                                <i class="em em-smiley"></i>
                            </p>
                            <div class="inline-itms">
                                <span>1 year ago</span>
                                <a class="we-reply" href="#" title="Reply"><i class="fa fa-reply"></i></a>
                                <a href="#" title=""><i class="fa fa-heart"></i><span>20</span></a>
                            </div>
                        </div>
                    </li>
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
