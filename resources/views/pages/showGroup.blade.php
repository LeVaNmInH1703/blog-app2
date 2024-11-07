@extends('layout.app')
@section('title', 'About Group')
@section('content-app')

    <div class="d-flex justify-content-around">
        <form action="/update-group/{{ $group->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mt-2">
                <label for="name">{{ __('public.Name') }}</label>
                <input name="nameGroup" {{ Auth::user()->roleId!=1?'readonly':'' }} type="text" class="form-control" required id="name" value="{{ $group->name }}">
                <x-show-errors-component :errors=$errors :name="'nameGroup'" />
            </div>
            <div class="mt-2">
                <label for="fileInput">{{ __('public.Choose avatar') }}</label> <br>
                <img id="previewImg" class="img-thumbnail" src="{{ $group->url_avatar }}"
                    alt="{{ __('public.Group avatar') }}">
                @if (Auth::user()->roleId == 1)
                    <input name="avatar" type="file" class="form-control" id="fileInput" onchange="PreviewImage(event)">
                    <x-show-errors-component :errors=$errors :name="'avatar'" />
                @endif
            </div>
            <div class="d-flex flex-md-row-reverse p-2">
                @if (Auth::user()->roleId == 1)
                    <button type="submit" class="btn btn-primary">{{ __('public.Done') }}</button>
                @endif
            </div>
        </form>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('public.User') }}</th>
                        <th>{{ __('public.Name') }}</th>
                        <th>{{ __('public.Role') }}</th>
                        <th>{{ __('public.Option') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($group->users as $user)
                        <tr>
                            <td class="clearfix d-flex justify-content-start" data-id={{ $user->id }}>
                                <img src="{{ $user->url_avatar }}" alt="avatar" class="img_profile img-circle">

                            </td>
                            <td>
                                <span
                                    @if ($user->name == Auth::user()->name) class='text-secondary' @endif>{{ $user->name == Auth::user()->name ? __('public.You') : $user->name }}</span>
                            </td>
                            <td>
                                <div class="text-{{ $user->roleId == 1 ? 'primary' : 'success' }}">
                                    {{ $user->roleId == 1 ? __('public.Admin') : __('public.Member') }}</div>
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('profile', $user->id) }}"
                                        class="btn btn-sm btn-outline-info">{{ __('public.Profile') }}</a>
                                    @if (Auth::user()->roleId == 1 && Auth::user()->id != $user->id)
                                        <a href="{{ route('kickMember', [$group->id, $user->id]) }}"
                                            class="btn btn-sm btn-outline-danger">{{ __('public.Remove from group') }}</a>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <div>
                <button data-toggle="modal" data-target="#formAddGroupMessage"
                    class="btn btn-outline-success">{{ __('public.Add member') }}</button>
                @if (Auth::user()->roleId == 1)
                    <a href="{{ route('disovleGroup', [$group->id]) }}" class="btn btn-outline-danger"
                        onClick="return confirm('{{ __('public.Are you sure to disolve this group') }}')">{{ __('public.Disolve group') }}</a>
                @else
                    <a href="{{ route('leaveGroup', [$group->id]) }}" class="btn btn-outline-danger"
                        onClick="return confirm('{{ __('public.Are you sure to leave this group') }}')">{{ __('public.Leave group') }}</a>
                @endif
            </div>
            <div class="modal fade" id="formAddGroupMessage" tabindex="100" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content bg-dark">
                        <div class="modal-header border-bottom-0">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('public.Add menber') }}</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white">&times;</span>
                            </button>
                        </div>
                        <div class="ml-4 mr-4">
                            <input type="text" class="form-control col-md-6 col-sm-6"
                                placeholder="{{ __('public.Search') }}...">
                        </div>
                        <ul id='friendList' class="list-unstyled ">
                            @foreach ($friends as $friend)
                                <li class="friend"
                                    data-id={{ $friend->id }}>
                                    <img src="{{ $friend->url_avatar }}" alt="avatar" class="img_profile img-circle">
                                    <span class="ml-3 d-flex align-items-center">{{ $friend->name }}</span>
                                    @if (!$friend->isInGroup)
                                        <a class="ml-3 btn btn-outline-success btn-sm"
                                            href="{{ route('addMember', [$group->id, $friend->id]) }}">{{ __('public.Add to group') }}</a>
                                    @else
                                        <span class="ml-3 text-secondary">{{ __('public.Member') }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('style-app')
    <style>
        .friend{
            padding: 5px 0px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 50%;
        }
        .modal-content{
            padding: 10px;

        }
        .modal-header{
            padding: 0px;
            margin-bottom: 10px;
        }
        .modal-title{
            font-size: 30px;
        }
        td+td,
        th+th {
            padding-left: 20px;
        }
    </style>
@endsection
<script src="{{ asset('js/groupchat/show.js') }}"></script>
@endsection
