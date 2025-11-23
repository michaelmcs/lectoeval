<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LECTOEVAL - Sistema de Evaluación Lectora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }
        .brand-font {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

    <nav class="bg-gradient-to-r from-purple-500 to-pink-500 shadow-lg border-b-4 border-yellow-400" x-data="navApp()">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center shadow-2xl border-4 border-yellow-300 animate-bounce">
                                <div class="text-3xl">📚</div>
                            </div>
                        </div>
                        
                        <div class="hidden lg:block">
                            <div class="flex flex-col">
                                <h1 class="text-3xl font-bold text-white tracking-wide" style="font-family: 'Comic Sans MS', cursive; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                                    LECTOEVAL
                                </h1>
                                <p class="text-yellow-200 text-sm font-semibold mt-1">¡Aprendemos a leer jugando!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navegación Principal - Diseño Infantil -->
                    <div class="hidden xl:flex items-center space-x-4">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center space-x-3 bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg border-2 border-white border-opacity-30 
                                  {{ request()->routeIs('dashboard') ? 'bg-white bg-opacity-30 shadow-lg scale-105' : '' }}">
                            <div class="text-2xl">🏠</div>
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-sm">Dashboard</span>
                                <span class="text-yellow-200 text-xs">Inicio</span>
                            </div>
                            <div class="w-3 h-3 bg-green-400 rounded-full ml-2 animate-pulse"></div>
                        </a>

                        <!-- Estudiantes -->
                        <a href="{{ route('students.index') }}" 
                           class="flex items-center space-x-3 bg-blue-500 bg-opacity-70 hover:bg-opacity-90 px-4 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg border-2 border-blue-300 
                                  {{ request()->routeIs('students.*') ? 'bg-blue-600 shadow-lg scale-105' : '' }}">
                            <div class="text-2xl">👦👧</div>
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-sm">Estudiantes</span>
                                <span class="text-blue-100 text-xs">Amiguitos</span>
                            </div>
                            <div class="bg-white text-blue-600 text-xs px-2 py-1 rounded-full font-bold ml-2" x-text="counts.students">
                                0
                            </div>
                        </a>

                        <!-- Docentes -->
                        <a href="{{ route('teachers.index') }}" 
                           class="flex items-center space-x-3 bg-green-500 bg-opacity-70 hover:bg-opacity-90 px-4 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg border-2 border-green-300 
                                  {{ request()->routeIs('teachers.*') ? 'bg-green-600 shadow-lg scale-105' : '' }}">
                            <div class="text-2xl">👨‍🏫👩‍🏫</div>
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-sm">Docentes</span>
                                <span class="text-green-100 text-xs">Profes</span>
                            </div>
                            <div class="bg-white text-green-600 text-xs px-2 py-1 rounded-full font-bold ml-2" x-text="counts.teachers">
                                0
                            </div>
                        </a>

                        <!-- Textos -->
                        <a href="{{ route('texts.index') }}" 
                           class="flex items-center space-x-3 bg-purple-500 bg-opacity-70 hover:bg-opacity-90 px-4 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg border-2 border-purple-300 
                                  {{ request()->routeIs('texts.*') ? 'bg-purple-600 shadow-lg scale-105' : '' }}">
                            <div class="text-2xl">📖</div>
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-sm">Textos</span>
                                <span class="text-purple-100 text-xs">Cuentos</span>
                            </div>
                            <div class="bg-white text-purple-600 text-xs px-2 py-1 rounded-full font-bold ml-2" x-text="counts.texts">
                                0
                            </div>
                        </a>

                        <!-- Lecturas -->
                        <a href="{{ route('readings.create') }}" 
                           class="flex items-center space-x-3 bg-red-500 bg-opacity-70 hover:bg-opacity-90 px-4 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg border-2 border-red-300 
                                  {{ request()->routeIs('readings.*') ? 'bg-red-600 shadow-lg scale-105' : '' }}">
                            <div class="text-2xl">🎤</div>
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-sm">Lecturas</span>
                                <span class="text-red-100 text-xs">Leer en voz alta</span>
                            </div>
                            <div class="bg-yellow-400 text-red-600 text-xs px-2 py-1 rounded-full font-bold ml-2 animate-pulse" x-text="counts.readings">
                                0
                            </div>
                        </a>

                        <!-- Comparador -->
                        <a href="{{ route('compare.index') }}" 
                           class="flex items-center space-x-3 bg-orange-500 bg-opacity-70 hover:bg-opacity-90 px-4 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg border-2 border-orange-300 
                                  {{ request()->routeIs('compare.*') ? 'bg-orange-600 shadow-lg scale-105' : '' }}">
                            <div class="text-2xl">📊</div>
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-sm">Comparador</span>
                                <span class="text-orange-100 text-xs">Ver progreso</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- LADO DERECHO: Acciones de Usuario -->
                <div class="flex items-center space-x-4">
                    
                    <!-- Botón Menú Móvil -->
                    <div class="xl:hidden">
                        <button @click="open = !open" class="text-white p-2 rounded-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Campana de Notificaciones -->
                    <button class="relative p-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl transition duration-300">
                        <div class="text-xl">🔔</div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center shadow" x-show="counts.notifications > 0">
                            <span class="text-white text-xs font-bold" x-text="counts.notifications"></span>
                        </div>
                    </button>

                    <!-- Perfil del Usuario -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-3 bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-2 rounded-xl transition-all duration-300">
                            
                            <!-- Avatar del Usuario -->
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center shadow border border-blue-300">
                                <span class="text-xl">👨‍🏫</span>
                            </div>
                            
                            <!-- Información del Usuario -->
                            <div class="hidden md:block text-left">
                                <div class="text-white font-semibold text-sm">{{ auth()->user()->name }}</div>
                                <div class="text-blue-200 text-xs">Docente</div>
                            </div>
                            
                            <!-- Flecha -->
                            <div class="text-white text-sm transform transition duration-300" :class="{ 'rotate-180': open }">
                                ▼
                            </div>
                        </button>

                        <!-- Menú Desplegable -->
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl py-2 z-50 border border-gray-200"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95">
                            
                            <!-- Header del Perfil -->
                            <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                                        <span class="text-xl text-blue-600">👨‍🏫</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-semibold">{{ auth()->user()->name }}</div>
                                        <div class="text-blue-100 text-sm">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones del Menú -->
                            <div class="space-y-1 py-2">
                                <a href="{{ route('profile.show') }}"
                                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 transition duration-200">
                                    <span class="text-lg mr-3">👤</span>
                                    <div>
                                        <div class="font-medium text-sm">Mi Perfil</div>
                                    </div>
                                </a>

                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 transition duration-200">
                                    <span class="text-lg mr-3">⚙️</span>
                                    <div>
                                        <div class="font-medium text-sm">Configuración</div>
                                    </div>
                                </a>

                                <a href="#" 
                                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 transition duration-200">
                                    <span class="text-lg mr-3">📊</span>
                                    <div>
                                        <div class="font-medium text-sm">Reportes</div>
                                    </div>
                                </a>

                                <a href="#" 
                                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 transition duration-200">
                                    <span class="text-lg mr-3">❓</span>
                                    <div>
                                        <div class="font-medium text-sm">Ayuda</div>
                                    </div>
                                </a>
                            </div>

                            <div class="border-t border-gray-200 mx-3 my-1"></div>

                            <!-- Cerrar Sesión -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 transition duration-200">
                                    <span class="text-lg mr-3">🚪</span>
                                    <div>
                                        <div class="font-medium text-sm">Cerrar Sesión</div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menú Móvil -->
            <div class="xl:hidden" x-show="open" x-transition>
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white bg-opacity-10 rounded-lg mt-2">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-3 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <span class="text-lg mr-3">🏠</span>
                        Dashboard
                    </a>
                    <a href="{{ route('students.index') }}" 
                       class="flex items-center px-3 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <span class="text-lg mr-3">👦👧</span>
                        Estudiantes
                        <span class="ml-auto bg-white text-blue-600 text-xs px-2 py-1 rounded-full font-bold" x-text="counts.students">0</span>
                    </a>
                    <a href="{{ route('teachers.index') }}" 
                       class="flex items-center px-3 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <span class="text-lg mr-3">👨‍🏫👩‍🏫</span>
                        Docentes
                        <span class="ml-auto bg-white text-green-600 text-xs px-2 py-1 rounded-full font-bold" x-text="counts.teachers">0</span>
                    </a>
                    <a href="{{ route('texts.index') }}" 
                       class="flex items-center px-3 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <span class="text-lg mr-3">📖</span>
                        Textos
                        <span class="ml-auto bg-white text-purple-600 text-xs px-2 py-1 rounded-full font-bold" x-text="counts.texts">0</span>
                    </a>
                    <a href="{{ route('readings.create') }}" 
                       class="flex items-center px-3 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <span class="text-lg mr-3">🎤</span>
                        Lecturas
                        <span class="ml-auto bg-yellow-400 text-red-600 text-xs px-2 py-1 rounded-full font-bold" x-text="counts.readings">0</span>
                    </a>
                    <a href="{{ route('compare.index') }}" 
                       class="flex items-center px-3 py-2 text-white rounded-lg hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <span class="text-lg mr-3">📊</span>
                        Comparador
                    </a>
                </div>
            </div>
        </div>

        <script>
            function navApp() {
                return {
                    open: false,
                    counts: {
                        students: 0,
                        teachers: 0,
                        texts: 0,
                        readings: 0,
                        notifications: 0
                    },
                    
                    async loadCounts() {
                        try {
                            const [studentsRes, teachersRes, textsRes, readingsRes] = await Promise.all([
                                fetch('/api/students').catch(() => null),
                                fetch('/api/teachers').catch(() => null),
                                fetch('/admin/texts/api').catch(() => null),
                                fetch('/admin/readings/api').catch(() => null)
                            ]);
                            
                            if (studentsRes && studentsRes.ok) {
                                const studentsData = await studentsRes.json();
                                this.counts.students = studentsData.length;
                            }
                            
                            if (teachersRes && teachersRes.ok) {
                                const teachersData = await teachersRes.json();
                                this.counts.teachers = teachersData.length;
                            }
                            
                            if (textsRes && textsRes.ok) {
                                const textsData = await textsRes.json();
                                this.counts.texts = textsData.length;
                            }
                            
                            if (readingsRes && readingsRes.ok) {
                                const readingsData = await readingsRes.json();
                                this.counts.readings = readingsData.length;
                            }
                            
                            this.counts.notifications = this.calculateNotifications();
                            
                        } catch (error) {
                            this.setDefaultCounts();
                        }
                    },
                    
                    calculateNotifications() {
                        let notifications = 0;
                        
                        if (this.counts.readings > 0) {
                            notifications += Math.min(this.counts.readings, 3);
                        }
                        
                        if (this.counts.students > this.counts.readings) {
                            notifications += 1;
                        }
                        
                        return Math.min(notifications, 9);
                    },
                    
                    setDefaultCounts() {
                        this.counts = {
                            students: 0,
                            teachers: 0,
                            texts: 0,
                            readings: 0,
                            notifications: 0
                        };
                    },
                    
                    init() {
                        this.loadCounts();
                        
                        setInterval(() => {
                            this.loadCounts();
                        }, 30000);
                    }
                }
            }
        </script>
    </nav>

</body>
</html>