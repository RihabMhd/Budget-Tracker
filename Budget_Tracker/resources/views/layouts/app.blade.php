<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - MyBudget</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/css/sidebar.css'])
    @stack('styles')

    <style>
        @keyframes robot-peek {
            0%, 100% { transform: translateX(1.2rem) translateY(-50%); }
            50% { transform: translateX(0.6rem) translateY(-50%); }
        }

        #robot-buddy-trigger {
            animation: robot-peek 3s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-gray-50">

    <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')"
        aria-label="Toggle menu">
        <svg viewBox="0 0 24 24">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>

    <nav class="sidebar">
        <a href="#" class="sidebar-logo">
            <div class="sidebar-logo-icon text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                    <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                        <path d="M13.29 4.654c-.07-.22-.17-.42-.24-.64a4.5 4.5 0 0 1-.17-.83a.28.28 0 0 0-.27-.31a.29.29 0 0 0-.3.27a4.4 4.4 0 0 0 0 2q.137.555.39 1.069c.12.26.22.26.36.43s.69.19.72-.15c-.13-.55-.1-.61-.27-1.16c-.06-.26-.14-.47-.22-.68m3.808-1.638a.3.3 0 0 0-.4.08a5.6 5.6 0 0 0-1 1.619q-.19.488-.31 1a2.7 2.7 0 0 0-.18.909c0 .42.39.44.77.18c.2-.286.358-.6.47-.93q.186-.49.31-1a8 8 0 0 1 .43-1.469a.3.3 0 0 0-.09-.39" />
                        <path d="M11.41 6.063a9 9 0 0 1-1.918-1.47a2.88 2.88 0 0 1-.76-2.118q0-.441.1-.87c.05-.2.1-.41.26-.5a.58.58 0 0 1 .54 0q.58.277 1.079.68a2.54 2.54 0 0 0 1.08.51a1.2 1.2 0 0 0 .769-.15c.312-.195.595-.434.84-.71c.14-.14.27-.31.45-.33c.263-.028.529.003.779.09c.743.409 1.54.709 2.369.89q.345.017.68-.07c.64-.17 1.179-.69 1.869-.76a2 2 0 0 1 .42 0q.186.01.36.08c.4.15.629.28.689.48a.6.6 0 0 1-.14.46c-.52.81-1.82 1.67-2.249 2.279q-.582.9-1.06 1.859c-.25.45.44.44.67 0c.44-.56.57-.9 1-1.47a17 17 0 0 1 1.649-1.449c.46-.373.807-.868 1-1.429c.13-.58-.1-1.2-1.2-1.68a2.3 2.3 0 0 0-.63-.17a4 4 0 0 0-.61 0a7 7 0 0 0-1.828.7a1.3 1.3 0 0 1-.55.09c-.84-.06-1.38-.6-2.07-.84A2.9 2.9 0 0 0 13.72.017a1.54 1.54 0 0 0-.72.3c-.25.19-.489.46-.739.69s-.23.26-.39.24a1.7 1.7 0 0 1-.6-.31a6.6 6.6 0 0 0-1.269-.76a1.53 1.53 0 0 0-1.4.11a1.4 1.4 0 0 0-.549.73c-.15.478-.217.978-.2 1.479a3.65 3.65 0 0 0 1.08 2.599a8.4 8.4 0 0 0 2.359 1.459c.35.14.54-.2.12-.49" />
                        <path d="M11.92 7.682a15 15 0 0 0 1.6.43q.496.086.999.11q.5.037 1 0c.45 0 3.048-.49 2.858-1a.3.3 0 0 0-.38-.27c-1.728.33-3.501.35-5.237.06q-.735-.14-1.48-.2a5.15 5.15 0 0 0-3.088.06a7.3 7.3 0 0 0-1.859 1c-.82.59-1.55 1.32-2.319 2h-.01q-.405.04-.8.14q-.447.135-.87.339c-.57.28-1.07.683-1.468 1.18a2.4 2.4 0 0 0-.51 1.299a.91.91 0 0 0 .87 1a3.64 3.64 0 0 0 1.929-.45q.269-.175.5-.4q.247-.203.45-.45q.374-.425.629-.93a3.8 3.8 0 0 0 .32-1a.31.31 0 0 0-.24-.379a.3.3 0 0 0-.14 0c.74-.49 1.48-1 2.229-1.46a12 12 0 0 1 1.669-.869a5.7 5.7 0 0 1 1.949-.46c.478-.012.954.073 1.4.25m-7.496 2.769c-.078.29-.2.566-.36.82a2.9 2.9 0 0 1-.58.68q-.194.177-.41.329q-.21.162-.45.28a3.2 3.2 0 0 1-.719.2a4 4 0 0 1-.45.07a1.52 1.52 0 0 1 .39-.89a3.7 3.7 0 0 1 .88-.85a5 5 0 0 1 .71-.4q.35-.162.73-.24a.35.35 0 0 0 .259-.06l.07-.049s-.06.06-.07.11" />
                        <path d="M23.205 15.938c-.68-1.4-2-2.609-2.879-3.898a8.8 8.8 0 0 1-.93-1.839c-.35-.89-.599-1.46-.889-2.359c-.1-.23-.68.09-.5.36c.27.89.39 1.37.71 2.259c.265.698.6 1.368 1 1.999c.69 1.1 1.739 2.139 2.428 3.288a3.52 3.52 0 0 1 .6 2.31c-.33 2.598-2.419 3.997-4.998 4.577c-2.241.457-4.562.34-6.746-.34a9.1 9.1 0 0 1-3.848-2.159a4.4 4.4 0 0 1-1.24-2.898a10.6 10.6 0 0 1 1.26-4.998c.415-.806.933-1.555 1.539-2.229a15 15 0 0 1 1.879-1.859c.38-.54 0-.63-.54-.36q-.991.826-1.849 1.79a12.2 12.2 0 0 0-1.7 2.358a11.3 11.3 0 0 0-1.498 5.338a5.3 5.3 0 0 0 1.439 3.568a9.9 9.9 0 0 0 4.248 2.499c2.356.733 4.86.853 7.276.35c2.999-.68 5.338-2.48 5.668-5.528a4.23 4.23 0 0 0-.43-2.229" />
                        <path d="M9.881 10.57a5 5 0 0 0-.13 1.15a6.3 6.3 0 0 0 .39 2.43a2.6 2.6 0 0 0-.48 1.279c-.045.51.041 1.022.25 1.489c.22.47.517.899.88 1.27c.223.212.505.351.81.399a1.12 1.12 0 0 0 1.169-.8l.15-.62q.015-.213 0-.43a6 6 0 0 0-.07-.569a5 5 0 0 0-.14-.62a3.6 3.6 0 0 0-.27-.6a3 3 0 0 0-.52-.619c-.26-.24-.55-.45-.84-.68a.3.3 0 0 0-.34 0a8 8 0 0 1 .09-2.159c.09-.54.2-1.07.34-1.599q.19-.964.55-1.879c0-.38-.4-.3-.65 0c-.35.439-.645.919-.879 1.43a6 6 0 0 0-.31 1.129m.72 3.999a.31.31 0 0 0 .2-.24q.28.25.53.53a2.3 2.3 0 0 1 .32.49q.087.212.13.44q.028.24 0 .48v.739l-.08.29c-.12.23 0 .22-.29 0a3.9 3.9 0 0 1-.74-.87a2.2 2.2 0 0 1-.34-1c.017-.304.11-.6.27-.86" />
                    </g>
                </svg>
            </div>
            <span class="sidebar-logo-text">MyBudget</span>
        </a>

        <div class="nav-label">Overview</div>
        <a href="{{route('dashboard')}}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
            </svg>
            Dashboard
        </a>
        <a href="{{route('transactions.index')}}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
            Transactions
        </a>
        <a href="{{route('analytics')}}" class="nav-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <line x1="18" y1="20" x2="18" y2="10" />
                <line x1="12" y1="20" x2="12" y2="4" />
                <line x1="6" y1="20" x2="6" y2="14" />
            </svg>
            Analytics
        </a>

        <div class="nav-label">Planning</div>
        <a href="{{route('goals.index')}}" class="nav-item {{ request()->routeIs('goals.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
            </svg>
            Savings Goals
        </a>
        <a href="{{route('budgets.index')}}" class="nav-item {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                <polyline points="14,2 14,8 20,8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
            </svg>
            Budgets
        </a>
        <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                <line x1="7" y1="7" x2="7.01" y2="7" />
            </svg>
            Categories
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <a href='{{ route('profile.show') }}'>
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->username, 0, 2)) }}</div>
                </a>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->username }}</div>
                    <div class="user-pts">{{ number_format(Auth::user()->points) }} pts</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <svg viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                            <polyline points="16,17 21,12 16,7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="content">
        @yield('content')
    </main>

    <div id="robot-buddy-trigger" onclick="openChat()" class="fixed right-0 top-1/2 -translate-y-1/2 translate-x-5 hover:translate-x-0 transition-transform duration-300 cursor-pointer z-50 group">
        <div class="bg-emerald-700 p-3 rounded-l-full shadow-lg flex items-center gap-2 border-y border-l border-emerald-600">
            <div class="bg-white p-1 rounded-full shadow-inner animate-pulse text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24"><path fill="currentColor" d="M22.078 8.347a1.4 1.4 0 0 0-.488-.325V4.647a.717.717 0 1 0-1.434 0V7.85h-.21a5.48 5.48 0 0 0-5.25-3.92H9.427a5.48 5.48 0 0 0-5.25 3.92H3.9V4.647a.717.717 0 1 0-1.434 0v3.385a1.5 1.5 0 0 0-.469.315A1.72 1.72 0 0 0 1.5 9.552v4.896a1.7 1.7 0 0 0 1.702 1.702h.956a5.48 5.48 0 0 0 5.25 3.92h5.183a5.48 5.48 0 0 0 5.25-3.92h.955a1.7 1.7 0 0 0 1.702-1.702V9.552c.02-.44-.131-.872-.42-1.205M3.996 14.716H3.24a.27.27 0 0 1-.191-.077a.3.3 0 0 1-.076-.191V9.552a.26.26 0 0 1 .248-.268h.775a.6.6 0 0 0 0 .125v5.182a.6.6 0 0 0 0 .125m4.695-3.118a.813.813 0 0 1-1.386-.578c0-.217.086-.425.238-.579l.956-.956a.813.813 0 0 1 1.148 0l.956.956a.812.812 0 0 1-.574 1.387a.8.8 0 0 1-.573-.23l-.412-.41zm5.9 4.074a3.605 3.605 0 0 1-5.068 0a.813.813 0 0 1 .885-1.326a.8.8 0 0 1 .262.178a2.017 2.017 0 0 0 2.773 0a.804.804 0 0 1 1.148 0a.813.813 0 0 1 0 1.148m1.912-4.074a.813.813 0 0 1-1.148 0l-.41-.41l-.402.41a.82.82 0 0 1-.574.23a.8.8 0 0 1-.574-.23a.82.82 0 0 1 0-1.157l.957-.956a.813.813 0 0 1 1.147 0l.956.956a.82.82 0 0 1 .077 1.157zm4.609 2.869a.3.3 0 0 1-.077.191a.27.27 0 0 1-.191.077h-.755a.6.6 0 0 0 0-.125V9.37a.6.6 0 0 0-.124h.765a.25.25 0 0 1 .181.077c.049.052.076.12.077.19z"/></svg>
            </div>
            <span class="text-white font-bold text-xs pr-2 hidden group-hover:block">Ask Buddy!</span>
        </div>
    </div>

    <div id="ai-chat-window" class="hidden fixed bottom-6 right-6 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col h-96 z-50">
        <div class="p-4 bg-emerald-700 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24"><path fill="currentColor" d="M22.078 8.347a1.4 1.4 0 0 0-.488-.325V4.647a.717.717 0 1 0-1.434 0V7.85h-.21a5.48 5.48 0 0 0-5.25-3.92H9.427a5.48 5.48 0 0 0-5.25 3.92H3.9V4.647a.717.717 0 1 0-1.434 0v3.385a1.5 1.5 0 0 0-.469.315A1.72 1.72 0 0 0 1.5 9.552v4.896a1.7 1.7 0 0 0 1.702 1.702h.956a5.48 5.48 0 0 0 5.25 3.92h5.183a5.48 5.48 0 0 0 5.25-3.92h.955a1.7 1.7 0 0 0 1.702-1.702V9.552c.02-.44-.131-.872-.42-1.205M3.996 14.716H3.24a.27.27 0 0 1-.191-.077a.3.3 0 0 1-.076-.191V9.552a.26.26 0 0 1 .248-.268h.775a.6.6 0 0 0 0 .125v5.182a.6.6 0 0 0 0 .125m4.695-3.118a.813.813 0 0 1-1.386-.578c0-.217.086-.425.238-.579l.956-.956a.813.813 0 0 1 1.148 0l.956.956a.812.812 0 0 1-.574 1.387a.8.8 0 0 1-.573-.23l-.412-.41zm5.9 4.074a3.605 3.605 0 0 1-5.068 0a.813.813 0 0 1 .885-1.326a.8.8 0 0 1 .262.178a2.017 2.017 0 0 0 2.773 0a.804.804 0 0 1 1.148 0a.813.813 0 0 1 0 1.148m1.912-4.074a.813.813 0 0 1-1.148 0l-.41-.41l-.402.41a.82.82 0 0 1-.574.23a.8.8 0 0 1-.574-.23a.82.82 0 0 1 0-1.157l.957-.956a.813.813 0 0 1 1.147 0l.956.956a.82.82 0 0 1 .077 1.157zm4.609 2.869a.3.3 0 0 1-.077.191a.27.27 0 0 1-.191.077h-.755a.6.6 0 0 0 0-.125V9.37a.6.6 0 0 0-.124h.765a.25.25 0 0 1 .181.077c.049.052.076.12.077.19z"/></svg>
                <h3 class="font-bold text-sm">Budget Buddy AI</h3>
            </div>
            <button onclick="closeChat()" class="hover:text-emerald-100">✕</button>
        </div>
        
        <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50 text-sm">
            <div class="flex items-start gap-3">
                <div class="bg-emerald-100 p-1.5 rounded-full text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M22.078 8.347a1.4 1.4 0 0 0-.488-.325V4.647a.717.717 0 1 0-1.434 0V7.85h-.21a5.48 5.48 0 0 0-5.25-3.92H9.427a5.48 5.48 0 0 0-5.25 3.92H3.9V4.647a.717.717 0 1 0-1.434 0v3.385a1.5 1.5 0 0 0-.469.315A1.72 1.72 0 0 0 1.5 9.552v4.896a1.7 1.7 0 0 0 1.702 1.702h.956a5.48 5.48 0 0 0 5.25 3.92h5.183a5.48 5.48 0 0 0 5.25-3.92h.955a1.7 1.7 0 0 0 1.702-1.702V9.552c.02-.44-.131-.872-.42-1.205M3.996 14.716H3.24a.27.27 0 0 1-.191-.077a.3.3 0 0 1-.076-.191V9.552a.26.26 0 0 1 .248-.268h.775a.6.6 0 0 0 0 .125v5.182a.6.6 0 0 0 0 .125m4.695-3.118a.813.813 0 0 1-1.386-.578c0-.217.086-.425.238-.579l.956-.956a.813.813 0 0 1 1.148 0l.956.956a.812.812 0 0 1-.574 1.387a.8.8 0 0 1-.573-.23l-.412-.41zm5.9 4.074a3.605 3.605 0 0 1-5.068 0a.813.813 0 0 1 .885-1.326a.8.8 0 0 1 .262.178a2.017 2.017 0 0 0 2.773 0a.804.804 0 0 1 1.148 0a.813.813 0 0 1 0 1.148m1.912-4.074a.813.813 0 0 1-1.148 0l-.41-.41l-.402.41a.82.82 0 0 1-.574.23a.8.8 0 0 1-.574-.23a.82.82 0 0 1 0-1.157l.957-.956a.813.813 0 0 1 1.147 0l.956.956a.82.82 0 0 1 .077 1.157zm4.609 2.869a.3.3 0 0 1-.077.191a.27.27 0 0 1-.191.077h-.755a.6.6 0 0 0 0-.125V9.37a.6.6 0 0 0-.124h.765a.25.25 0 0 1 .181.077c.049.052.076.12.077.19z"/></svg>
                </div>
                <div class="bg-white p-3 rounded-lg shadow-sm border border-emerald-100 text-gray-700">
                    Salam, <strong>{{ Auth::user()->username }}</strong>! I'm your robot buddy. Ask me about your spending!
                </div>
            </div>
        </div>
        
        <div class="p-3 border-t bg-white flex gap-2">
            <input id="chat-input" type="text" placeholder="How much did I spend..." class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            <button onclick="sendMessage()" class="bg-emerald-700 hover:bg-emerald-800 text-white p-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        function openChat() { 
            document.getElementById('ai-chat-window').classList.remove('hidden'); 
            document.getElementById('robot-buddy-trigger').classList.add('hidden'); 
        }
        
        function closeChat() { 
            document.getElementById('ai-chat-window').classList.add('hidden'); 
            document.getElementById('robot-buddy-trigger').classList.remove('hidden'); 
        }

        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const msg = input.value;
            if(!msg) return;

            const chat = document.getElementById('chat-messages');
            chat.innerHTML += `<div class="flex justify-end"><div class="bg-emerald-600 text-white p-2 rounded-lg max-w-[80%] shadow-sm">${msg}</div></div>`;
            input.value = '';
            chat.scrollTop = chat.scrollHeight;

            try {
                const response = await fetch("{{ route('ai.chat') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                    },
                    body: JSON.stringify({ message: msg })
                });
                
                const data = await response.json();
                chat.innerHTML += `
                    <div class="flex items-start gap-3">
                        <div class="bg-emerald-100 p-1.5 rounded-full text-emerald-700 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M22.078 8.347a1.4 1.4 0 0 0-.488-.325V4.647a.717.717 0 1 0-1.434 0V7.85h-.21a5.48 5.48 0 0 0-5.25-3.92H9.427a5.48 5.48 0 0 0-5.25 3.92H3.9V4.647a.717.717 0 1 0-1.434 0v3.385a1.5 1.5 0 0 0-.469.315A1.72 1.72 0 0 0 1.5 9.552v4.896a1.7 1.7 0 0 0 1.702 1.702h.956a5.48 5.48 0 0 0 5.25 3.92h5.183a5.48 5.48 0 0 0 5.25-3.92h.955a1.7 1.7 0 0 0 1.702-1.702V9.552c.02-.44-.131-.872-.42-1.205M3.996 14.716H3.24a.27.27 0 0 1-.191-.077a.3.3 0 0 1-.076-.191V9.552a.26.26 0 0 1 .248-.268h.775a.6.6 0 0 0 0 .125v5.182a.6.6 0 0 0 0 .125m4.695-3.118a.813.813 0 0 1-1.386-.578c0-.217.086-.425.238-.579l.956-.956a.813.813 0 0 1 1.148 0l.956.956a.812.812 0 0 1-.574 1.387a.8.8 0 0 1-.573-.23l-.412-.41zm5.9 4.074a3.605 3.605 0 0 1-5.068 0a.813.813 0 0 1 .885-1.326a.8.8 0 0 1 .262.178a2.017 2.017 0 0 0 2.773 0a.804.804 0 0 1 1.148 0a.813.813 0 0 1 0 1.148m1.912-4.074a.813.813 0 0 1-1.148 0l-.41-.41l-.402.41a.82.82 0 0 1-.574.23a.8.8 0 0 1-.574-.23a.82.82 0 0 1 0-1.157l.957-.956a.813.813 0 0 1 1.147 0l.956.956a.82.82 0 0 1 .077 1.157zm4.609 2.869a.3.3 0 0 1-.077.191a.27.27 0 0 1-.191.077h-.755a.6.6 0 0 0 0-.125V9.37a.6.6 0 0 0-.124h.765a.25.25 0 0 1 .181.077c.049.052.076.12.077.19z"/></svg>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-gray-100 max-w-[80%] shadow-sm">${data.reply}</div>
                    </div>`;
                chat.scrollTop = chat.scrollHeight;
            } catch (error) {
                console.error("AI Error:", error);
            }
        }
    </script>
</body>
</html>