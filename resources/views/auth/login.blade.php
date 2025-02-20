@extends('layouts.login-layout')

@section('content')
@if (session('two-factor'))
    <div class="wrap-login100" style="zoom: 80%; position: relative;">
        <div class="red-background" style="position: absolute; top: 0; left: 0; background-color: #ff0000; width: 50%; height: 100px; border-bottom-right-radius: 50px; z-index: 1;">
            <div class="ml-3" style="position: absolute; top: 10px; left: 10px; color: #fff; z-index: 2; font-family: Poppins-Bold;">
                <h4 class="corner-text font-weight-bold mt-2">Sistem Informasi Manajemen MTC</h4>
                <p class="text-white">Pertamina Maritime Training Center</p>
            </div>
        </div>
        <div class="login100-pic text-center js-tilt" data-tilt>
            <img src="{{ asset('img/MTC.png') }}" style="zoom: 100%;" alt="IMG">
        </div>

        <form class="login100-form validate-form" method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <span class="login100-form-title-2" style="margin-top: 60px;">
                Two-Factor Authentication
            </span>

            <div class="wrap-input100 validate-input mb-4" data-validate = "Valid value is required: Numeric">
                <div class="otp-container">
                    <!-- Six input fields for OTP digits -->
                    <input type="text" class="otp-input" pattern="\d" maxlength="1">
                    <input type="text" class="otp-input" pattern="\d" maxlength="1" disabled>
                    <input type="text" class="otp-input" pattern="\d" maxlength="1" disabled>
                    <input type="text" class="otp-input" pattern="\d" maxlength="1" disabled>
                    <input type="text" class="otp-input" pattern="\d" maxlength="1" disabled>
                    <input type="text" class="otp-input" pattern="\d" maxlength="1" disabled>
                </div>
                <input id="verificationCode" type="number" name="code" placeholder="Enter your authentication code" required hidden>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <div class="container-login100-form-btn">
                <button class="login100-form-btn" type="submit">
                    Verify Code
                </button>
            </div>

            <div class="text-center p-t-136">
                <a class="txt2" href="#">
                    {{-- Create your Account
                    <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i> --}}
                </a>
            </div>
        </form>
    </div>
@else
<x-auth-session-status class="mb-4" :status="session('status')" />
<div class="wrap-login100" style="zoom: 80%; position: relative;">
    <div class="red-background" style="position: absolute; top: 0; left: 0; background-color: #ff0000; width: 50%; height: 100px; border-bottom-right-radius: 50px; z-index: 1;">
        <div class="ml-3 login-text" style="position: absolute; top: 10px; left: 10px; color: #fff; z-index: 2; font-family: Poppins-Bold;">
            <h4 class="corner-text font-weight-bold mt-2">Sistem Informasi Manajemen MTC</h4>
            <p class="text-white">Pertamina Maritime Training Center</p>
        </div>
    </div>

    <!-- Text Content Positioned on Top of Red Background -->
    <div class="login100-pic text-center pr-3">
        <img src="img/MTC.png" style="zoom: 150%; padding-top: 20px;" alt="IMG">
    </div>

    <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
        @csrf
        <span class="login100-form-title">
            Member Login
        </span>

        <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
            <input class="input100" id="email" placeholder="your email..." class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" >
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

        <div class="wrap-input100 validate-input" data-validate = "Password is required">
            <input class="input100" id="password" placeholder="your password..." class="block mt-1 w-full"
            type="password"
            name="password"
            required autocomplete="current-password" >
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit">
                Login
            </button>
        </div>

        <div class="text-center p-t-12">
            <span class="txt1">
                Forgot
            </span>
            <a class="txt2" href="{{ route('password.request') }}">
                Username / Password?
            </a>
        </div>

        <div class="text-center p-t-100">
            <a class="txt2">
                Minimum Display 1360x720
            </a>
        </div>
    </form>
</div>
@endif
@endsection
