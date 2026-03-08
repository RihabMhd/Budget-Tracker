@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Create Account</h1>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        
        <div>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Username" required
                   class="custom-input w-full px-6 py-4 rounded-2xl text-sm text-gray-800 placeholder-gray-400">
            @error('username') <p class="text-[10px] text-red-500 mt-1 ml-2">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required
                   class="custom-input w-full px-6 py-4 rounded-2xl text-sm text-gray-800 placeholder-gray-400">
            @error('email') <p class="text-[10px] text-red-500 mt-1 ml-2">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <input type="password" name="password" placeholder="Password" required
                   class="custom-input w-full px-6 py-4 rounded-2xl text-sm text-gray-800 placeholder-gray-400">
            <input type="password" name="password_confirmation" placeholder="Confirm" required
                   class="custom-input w-full px-6 py-4 rounded-2xl text-sm text-gray-800 placeholder-gray-400">
        </div>
        @error('password') <p class="text-[10px] text-red-500 mt-1 ml-2">{{ $message }}</p> @enderror

        <button type="submit" class="w-full bg-[#FBCF97] text-gray-900 font-extrabold py-4 rounded-2xl text-sm hover:bg-[#f7bc71] transition-all shadow-sm">
            Create Account
        </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-8 font-medium">
        Already have an account? 
        <a href="{{ route('login') }}" class="text-[#2EB872] font-extrabold hover:underline ml-1">Sign In</a>
    </p>
@endsection