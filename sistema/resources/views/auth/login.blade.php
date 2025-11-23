<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LECTOEVAL - Aprendiendo a Leer Mejor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700&family=Fredoka+One&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Comic Neue', cursive;
        }
        .educational-bg {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .bounce {
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
    </style>
</head>
<body class="educational-bg min-h-screen flex items-center justify-center p-4">
    <!-- Elementos decorativos educativos -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Nube 1 -->
        <div class="absolute top-10 left-10 text-white opacity-20 float" style="animation-delay: 0s;">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
            </svg>
        </div>
        
        <!-- Estrella -->
        <div class="absolute top-20 right-20 text-yellow-300 opacity-30 bounce" style="animation-delay: 1s;">
            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
        </div>
        
        <!-- Libro -->
        <div class="absolute bottom-20 left-20 text-green-400 opacity-30 float" style="animation-delay: 2s;">
            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
            </svg>
        </div>
        
        <!-- Lápiz -->
        <div class="absolute bottom-32 right-32 text-red-400 opacity-30 bounce" style="animation-delay: 1.5s;">
            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
        </div>
    </div>

    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Lado Izquierdo: Zona Educativa -->
        <div class="text-white text-center lg:text-left">
            <!-- Logo y Título -->
            <div class="flex justify-center lg:justify-start items-center mb-8">
                <div class="relative">
                    <div class="w-24 h-24 bg-yellow-400 rounded-3xl flex items-center justify-center shadow-2xl border-4 border-white">
                        <svg class="w-14 h-14 text-white" viewBox="0 0 200 200" fill="currentColor">
                            <path d="M60,60 L60,140 L140,140 L140,60 L60,60 Z" 
                                  fill="none" stroke="currentColor" stroke-width="8" stroke-linejoin="round"/>
                            <path d="M70,60 L70,140" fill="none" stroke="currentColor" stroke-width="4"/>
                            <path d="M75,80 L130,80" fill="none" stroke="currentColor" stroke-width="3"/>
                            <path d="M75,100 L130,100" fill="none" stroke="currentColor" stroke-width="3"/>
                            <path d="M75,120 L130,120" fill="none" stroke="currentColor" stroke-width="3"/>
                            <path d="M160,100 Q170,90 180,100 Q190,110 180,120 Q170,130 160,120 Q150,110 160,100 Z" 
                                  fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        🎯
                    </div>
                </div>
                <div class="ml-6">
                    <h1 class="text-5xl font-bold" style="font-family: 'Fredoka One', cursive;">LECTOEVAL</h1>
                    <p class="text-blue-100 text-xl mt-2" style="font-family: 'Fredoka One', cursive;">
                        ¡Aprendemos a Leer Juntos!
                    </p>
                </div>
            </div>

            <!-- Mensaje de Bienvenida -->
            <div class="bg-white bg-opacity-20 rounded-3xl p-8 backdrop-blur-sm mb-8">
                <div class="flex items-start mb-4">
                    <div class="text-4xl mr-4">👋</div>
                    <div>
                        <h3 class="text-2xl font-bold mb-2">¡Hola Super Lector!</h3>
                        <p class="text-blue-100 text-lg leading-relaxed">
                            Bienvenido a tu aventura de lectura. Aquí aprenderás a leer mejor 
                            mientras te diviertes con juegos y actividades.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Características Educativas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-yellow-400 bg-opacity-90 rounded-2xl p-4 text-center transform hover:scale-105 transition duration-300">
                    <div class="text-3xl mb-2">🎮</div>
                    <div class="font-bold text-gray-800">Juegos Divertidos</div>
                    <div class="text-gray-700 text-sm">Aprende jugando</div>
                </div>
                <div class="bg-green-400 bg-opacity-90 rounded-2xl p-4 text-center transform hover:scale-105 transition duration-300">
                    <div class="text-3xl mb-2">⭐</div>
                    <div class="font-bold text-gray-800">Estrellas y Premios</div>
                    <div class="text-gray-700 text-sm">Gana recompensas</div>
                </div>
                <div class="bg-blue-400 bg-opacity-90 rounded-2xl p-4 text-center transform hover:scale-105 transition duration-300">
                    <div class="text-3xl mb-2">📚</div>
                    <div class="font-bold text-gray-800">Cuentos Mágicos</div>
                    <div class="text-gray-700 text-sm">Historias increíbles</div>
                </div>
                <div class="bg-purple-400 bg-opacity-90 rounded-2xl p-4 text-center transform hover:scale-105 transition duration-300">
                    <div class="text-3xl mb-2">🎤</div>
                    <div class="font-bold text-gray-800">Lee en Voz Alta</div>
                    <div class="text-gray-700 text-sm">Practica tu voz</div>
                </div>
            </div>

            <!-- Personaje Animado -->
            <div class="flex items-center justify-center lg:justify-start">
                <div class="bg-white bg-opacity-20 rounded-2xl p-4 flex items-center">
                    <div class="text-4xl mr-4">🐰</div>
                    <div>
                        <p class="text-white font-bold">¡Vamos a leer!</p>
                        <p class="text-blue-100 text-sm">El Conejo Lector te espera</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lado Derecho: Formulario de Login Amigable -->
        <div class="login-card p-8 lg:p-10 relative">
            <!-- Decoración superior -->
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-yellow-400 rounded-full w-12 h-12 flex items-center justify-center text-2xl">
                🔑
            </div>

            <!-- Header del Formulario -->
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-gray-800 mb-2" style="font-family: 'Fredoka One', cursive;">
                    ¡Hola Profe!
                </h2>
                <p class="text-gray-600 text-lg">Accede al panel de profesores</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-2xl text-green-700 text-sm">
                    <div class="flex items-center">
                        <span class="text-lg mr-2">✅</span>
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-lg font-bold text-gray-800 mb-3">
                        <span class="flex items-center">
                            <span class="text-2xl mr-2">📧</span>
                            Tu Correo de Profesor
                        </span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-2xl text-gray-400">👨‍🏫</span>
                        </div>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               required 
                               autofocus 
                               autocomplete="email"
                               value="{{ old('email') }}"
                               class="block w-full pl-12 pr-4 py-4 border-2 border-gray-300 rounded-2xl focus:ring-4 focus:ring-yellow-200 focus:border-yellow-400 transition duration-200 text-lg"
                               placeholder="profe@colegio.edu">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <span class="text-lg mr-1">⚠️</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-lg font-bold text-gray-800 mb-3">
                        <span class="flex items-center">
                            <span class="text-2xl mr-2">🔒</span>
                            Tu Contraseña Secreta
                        </span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-2xl text-gray-400">🦸</span>
                        </div>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               required 
                               autocomplete="current-password"
                               class="block w-full pl-12 pr-4 py-4 border-2 border-gray-300 rounded-2xl focus:ring-4 focus:ring-yellow-200 focus:border-yellow-400 transition duration-200 text-lg"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <span class="text-lg mr-1">⚠️</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-lg">
                        <input id="remember_me" 
                               name="remember" 
                               type="checkbox" 
                               class="h-5 w-5 text-yellow-500 focus:ring-yellow-400 border-gray-300 rounded">
                        <span class="ml-3 text-gray-700">Recordarme</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="text-lg text-blue-500 hover:text-blue-600 transition duration-200 flex items-center">
                            <span class="mr-1">❓</span>
                            ¿Olvidaste?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-yellow-400 to-orange-400 text-white py-4 px-6 rounded-2xl font-bold text-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition duration-200 focus:outline-none focus:ring-4 focus:ring-yellow-200">
                    <div class="flex items-center justify-center">
                        <span class="text-2xl mr-3">🚀</span>
                        ¡Entrar al Mundo de la Lectura!
                        <span class="text-2xl ml-3">📖</span>
                    </div>
                </button>
            </form>

            <!-- Información Adicional -->
            <div class="mt-8 pt-6 border-t-2 border-yellow-200">
                <div class="text-center">
                    <p class="text-gray-600 text-lg">
                        <span class="text-2xl mr-2">🏫</span>
                        Sistema Educativo LECTOEVAL
                    </p>
                    <p class="text-gray-500 text-sm mt-2">
                        Para niños de primaria - ¡Aprendemos divirtiéndonos!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para efectos educativos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Efecto en los inputs
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-4', 'ring-yellow-100');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-4', 'ring-yellow-100');
                });
            });

            // Efecto de confeti al hacer hover en el botón
            const button = document.querySelector('button[type="submit"]');
            button.addEventListener('mouseenter', function() {
                this.innerHTML = `
                    <div class="flex items-center justify-center">
                        <span class="text-2xl mr-3">🎉</span>
                        ¡Vamos a Aprender!
                        <span class="text-2xl ml-3">✨</span>
                    </div>
                `;
            });
            button.addEventListener('mouseleave', function() {
                this.innerHTML = `
                    <div class="flex items-center justify-center">
                        <span class="text-2xl mr-3">🚀</span>
                        ¡Entrar al Mundo de la Lectura!
                        <span class="text-2xl ml-3">📖</span>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>