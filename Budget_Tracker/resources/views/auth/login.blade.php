@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Login</h1>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        
        <div>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter Email / Phone No" required
                   class="custom-input w-full px-6 py-4 rounded-2xl text-sm text-gray-800 placeholder-gray-400">
            @error('email') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        
        <div class="relative">
            <input type="password" name="password" id="password" placeholder="Passcode" required
                   class="custom-input w-full px-6 py-4 rounded-2xl text-sm text-gray-800 placeholder-gray-400 pr-16">
            <button type="button" onclick="togglePassword()" 
                    class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 hover:text-gray-600">
                Hide
            </button>
        </div>

        <button type="submit" class="w-full bg-[#FBCF97] text-gray-900 font-extrabold py-4 rounded-2xl text-sm mt-4 shadow-sm hover:bg-[#f7bc71] transition-all">
            Sign in
        </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-10 font-medium">
        Don't have an account? 
        <a href="{{ route('register') }}" class="text-[#2EB872] font-extrabold hover:underline ml-1">Request Now</a>
    </p>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const pass = document.getElementById('password');
        pass.type = pass.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush