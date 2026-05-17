<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sembako Brayan</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        dark: { bg: '#0c0e12' }, 
                        brand: { blue: '#4c6fff' }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Transisi warna halus */
        body, div, input, button { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }

        /* Background Glow */
        .glow-effect {
            position: absolute; top: -20%; right: -10%; width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(76, 111, 255, 0.15) 0%, rgba(12, 14, 18, 0) 70%);
            border-radius: 50%; filter: blur(80px); z-index: -1; pointer-events: none;
        }

        /* --- STYLE KURSOR --- */
        @media (max-width: 1024px) {
            .cursor-dot, .cursor-bubble { display: none !important; }
            body { cursor: auto !important; }
        }
        @media (min-width: 1025px) {
            body { cursor: none; } 
        }
        .cursor-dot {
            position: fixed; top: 0; left: 0; width: 8px; height: 8px;
            background-color: #4c6fff; border-radius: 50%; z-index: 9999;
            pointer-events: none; transform: translate(-50%, -50%);
            transition: transform 0.1s, background-color 0.2s;
        }
        .cursor-bubble {
            position: fixed; top: 0; left: 0; width: 30px; height: 30px;
            border: 1px solid rgba(76, 111, 255, 0.3);
            background-color: rgba(76, 111, 255, 0.02);
            border-radius: 50%; z-index: 9998;
            pointer-events: none; transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s, border-color 0.3s, background-color 0.3s;
            will-change: transform;
        }
        body.hovering .cursor-dot {
            transform: translate(-50%, -50%) scale(1.8); background-color: #60a5fa;
        }
        body.hovering .cursor-bubble {
            border-color: rgba(76, 111, 255, 0.6); background-color: rgba(76, 111, 255, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 relative bg-gray-50 text-gray-800 dark:bg-[#0c0e12] dark:text-gray-300"
    x-data="{ 
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggle() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        }
    }"
    x-init="$watch('darkMode', val => { 
        if(val) { document.documentElement.classList.add('dark'); document.documentElement.classList.remove('light'); }
        else { document.documentElement.classList.remove('dark'); document.documentElement.classList.add('light'); }
    }); 
    if(darkMode) { document.documentElement.classList.add('dark'); document.documentElement.classList.remove('light'); }
    else { document.documentElement.classList.remove('dark'); document.documentElement.classList.add('light'); }"
>

    <button @click="toggle()" class="absolute top-6 right-6 p-3 rounded-full bg-white shadow-md text-gray-600 hover:scale-110 transition-transform z-50 dark:bg-white/10 dark:text-yellow-400">
        <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
    </button>

    <div class="glow-effect"></div>

    <div class="w-full max-w-md relative z-10">
        
        <div class="bg-white border border-gray-200 shadow-xl rounded-3xl overflow-hidden p-8 transition-all duration-300 dark:bg-white/5 dark:backdrop-blur-xl dark:border-white/10 dark:shadow-2xl">
            
            <div class="text-center mb-10">
                <div class="w-12 h-12 bg-brand-blue rounded-xl flex items-center justify-center text-white font-bold text-xl mx-auto mb-4 shadow-lg shadow-brand-blue/30">S</div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 dark:text-white">Welcome Back</h1>
                <p class="text-gray-500 text-sm">Masuk untuk mengelola Toko Sembako Brayan</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider dark:text-gray-400">Email Address</label>
                    <div class="relative group">
                        <input id="email" type="email" class="cursor-pointer w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-300 bg-gray-50 text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all dark:border-white/10 dark:bg-black/30 dark:text-white dark:placeholder-gray-600 dark:focus:bg-black/50 @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-gray-400 group-focus-within:text-brand-blue transition-colors" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/></svg>
                        </div>
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider dark:text-gray-400">Password</label>
                    <div class="relative group">
                        <input id="password" type="password" class="cursor-pointer w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-300 bg-gray-50 text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all dark:border-white/10 dark:bg-black/30 dark:text-white dark:placeholder-gray-600 dark:focus:bg-black/50 @error('password') border-red-500 @enderror" name="password" required placeholder="••••••••">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-blue transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm mt-2">
                    <label class="flex items-center cursor-pointer">
                        <input class="w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-offset-gray-900" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl shadow-lg shadow-brand-blue/30 text-sm font-bold text-white bg-brand-blue hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-brand-blue/20 transform active:scale-95 transition-all duration-200 mt-4 cursor-pointer">
                    MASUK
                </button>
            </form>
        </div>
        
        <p class="text-center text-gray-500 text-xs mt-8 dark:text-gray-600">
            &copy; {{ date('Y') }} Toko Sembako Brayan. All rights reserved.
        </p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const numBubbles = 12; 
            const speedBase = 0.15;
            let mouseX = window.innerWidth / 2, mouseY = window.innerHeight / 2;
            let isHovering = false; 
            const bubbles = [];

            // Buat Elemen Kursor (Dot & Bubbles)
            const cursorDot = document.createElement('div'); cursorDot.className = 'cursor-dot';
            document.body.appendChild(cursorDot);

            for (let i = 0; i < numBubbles; i++) {
                const bubble = document.createElement('div'); bubble.className = 'cursor-bubble';
                document.body.appendChild(bubble);
                bubbles.push({ x: mouseX, y: mouseY, element: bubble });
            }

            // Track Mouse
            window.addEventListener("mousemove", function(e) {
                mouseX = e.clientX; mouseY = e.clientY;
                cursorDot.style.left = `${mouseX}px`; cursorDot.style.top = `${mouseY}px`;
            });

            // Animasi Loop (Bubble Trail)
            function animate() {
                let leaderX = mouseX, leaderY = mouseY;
                bubbles.forEach((bubble, index) => {
                    // Physics: Follow leader with delay
                    bubble.x += (leaderX - bubble.x) * speedBase;
                    bubble.y += (leaderY - bubble.y) * speedBase;
                    
                    bubble.element.style.left = `${bubble.x}px`; 
                    bubble.element.style.top = `${bubble.y}px`;
                    
                    // Scaling Effect (Mengecil ke belakang)
                    let scale = (1 - (index / numBubbles) * 0.6) + (isHovering ? 0.3 : 0);
                    bubble.element.style.transform = `translate(-50%, -50%) scale(${scale})`;
                    
                    leaderX = bubble.x; leaderY = bubble.y;
                });
                requestAnimationFrame(animate);
            }
            animate();

            // Deteksi Hover (Untuk membesarkan gelembung saat kena tombol)
            document.body.addEventListener('mouseover', (e) => {
                if (e.target.closest('a, button, input, label, .cursor-pointer')) {
                    isHovering = true; document.body.classList.add('hovering');
                } else {
                    isHovering = false; document.body.classList.remove('hovering');
                }
            });
        });
    </script>
</body>
</html>