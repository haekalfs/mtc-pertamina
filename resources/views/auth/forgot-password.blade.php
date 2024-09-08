{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

@extends('layouts.login-layout')

@section('content')
<x-auth-session-status class="mb-2" :status="session('status')" />
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

    <form class="login100-form validate-form" method="POST" action="{{ route('password.email') }}">
        @csrf
        <span class="login100-form-title">
            Reset Password
        </span>

        <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
            <input class="input100" id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" >
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit">
                Send Me Reset Password Link
            </button>
        </div>

        <div class="text-center p-t-12">
            <span class="txt1">
                Already have an Accout?
            </span>
            <a class="txt2" href="/login">
                Login
            </a>
        </div>

        <div class="text-center p-t-136">
            <a class="txt2" href="#">
                {{-- Create your Account
                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i> --}}
            </a>
        </div>
    </form>
</div>
@endsection
