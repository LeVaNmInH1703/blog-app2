@extends('layout.app')
@section('title', 'Reset password')
@section('style')
    <link rel="icon" href="{{ asset('images/fav.png') }}" type="image/png" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/weather-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
@endsection
@section('content')
    <div class="www-layout">
        <section>
            <div class="gap no-gap signin whitish medium-opacity">
                <div class="bg-image" style="background-image:url(images/resources/theme-bg.jpg)"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="big-ad">
                                <figure><img src="images/logo2.png" alt=""></figure>
                                <h1>Welcome to the Pitnik</h1>
                                <p>
                                    Pitnik is a social network template that can be used to connect people. use this
                                    template for multipurpose social activities like job, dating, posting, bloging and much
                                    more. Now join & Make Cool Friends around the world !!!
                                </p>

                                <div class="fun-fact">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-4">
                                            <div class="fun-box">
                                                <i class="ti-check-box"></i>
                                                <h6>Registered People</h6>
                                                <span>1,01,242</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4">
                                            <div class="fun-box">
                                                <i class="ti-layout-media-overlay-alt-2"></i>
                                                <h6>Posts Published</h6>
                                                <span>21,03,245</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-4">
                                            <div class="fun-box">
                                                <i class="ti-user"></i>
                                                <h6>Online Users</h6>
                                                <span>40,145</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="barcode">
                                    <figure><img src="images/resources/Barcode.jpg" alt=""></figure>
                                    <div class="app-download">
                                        <span>Download Mobile App and Scan QR Code to login</span>
                                        <ul class="colla-apps">
                                            <li><a title="" href="https://play.google.com/store?hl=en"><img
                                                        src="images/android.png" alt="">android</a></li>
                                            <li><a title="" href="https://www.apple.com/lae/ios/app-store/"><img
                                                        src="images/apple.png" alt="">iPhone</a></li>
                                            <li><a title="" href="https://www.microsoft.com/store/apps"><img
                                                        src="images/windows.png" alt="">Windows</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="we-login-register">
                                <div class="form-title">
                                    <i class="fa fa-key"></i>forgot password?
                                    <span>enter your email to continue reset password.</span>
                                </div>
                                <form class="we-form" action="/reset-password" method="POST">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                                    <input type="password" autocomplete="cc-csc" value="" id="password"
                                        name="password" placeholder='{{ __('public.Password') }}' required>
                                    <x-show-errors-component :errors=$errors :name="'password'" />
                                    <input type="password" autocomplete="cc-csc" value="" name="confirm"
                                        placeholder='{{ __('public.Confirm') }}' required>
                                    <x-show-errors-component :errors=$errors :name="'confirm'" />
                                    <input type="hidden" name="token" value="{{ $token ?? old('token') }}">
                                    <button type="submit" data-ripple="">done</button>
                                    <x-show-errors-component :errors=$errors :name="'incorrect'" />
                                    <a class="forgot underline" href="{{ route('login') }}" title="">login?</a>
                                </form>
                                @if (session('urlResetPassword') || old('urlResetPassword'))
                                    <a href="{{ session('urlResetPassword') ?? old('urlResetPassword') }}">Link reset
                                        password</a>
                                @endif
                                <a class="with-smedia facebook" href="#" title="" data-ripple=""><i
                                        class="fa fa-facebook"></i></a>
                                <a class="with-smedia twitter" href="#" title="" data-ripple=""><i
                                        class="fa fa-twitter"></i></a>
                                <a class="with-smedia instagram" href="#" title="" data-ripple=""><i
                                        class="fa fa-instagram"></i></a>
                                <a class="with-smedia google" href="#" title="" data-ripple=""><i
                                        class="fa fa-google-plus"></i></a>
                                <span>don't have an account? <a class="we-account underline" href="#"
                                        title="">register now</a></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
@section('script')
@endsection
