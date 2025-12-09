<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <div class="max-w-7xl mx-auto p-4 sm:p-6" x-data="readingApp()">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800" style="font-family: 'Comic Sans MS', cursive;">
                    Sesiones de Lectura
                </h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">
                    Gestiona y revisa las sesiones de lectura de los estudiantes
                </p>
            </div>
            <div class="flex gap-3">
                <button @click="openCreateModal()" 
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 
                        text-white font-bold py-2.5 px-6 rounded-xl flex items-center shadow-lg transition duration-300">
                    <i class="fas fa-plus-circle mr-2"></i> Nueva Sesión
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center transition duration-300 hover:shadow-xl">
                <div class="text-2xl font-bold text-purple-600" x-text="stats.total"></div>
                <div class="text-sm text-gray-600">Total Sesiones</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center transition duration-300 hover:shadow-xl">
                <div class="text-2xl font-bold text-green-600" x-text="stats.completed"></div>
                <div class="text-sm text-gray-600">Completadas</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center transition duration-300 hover:shadow-xl">
                <div class="text-2xl font-bold text-blue-600" x-text="stats.processing"></div>
                <div class="text-sm text-gray-600">En Proceso</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center transition duration-300 hover:shadow-xl">
                <div class="text-2xl font-bold text-red-600" x-text="stats.error"></div>
                <div class="text-sm text-gray-600">Con Error</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6 transition duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" x-model="searchTerm" placeholder="Buscar por estudiante, texto o docente..." 
                               class="w-full bg-gray-50 border-0 rounded-2xl py-3 px-5 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <div class="absolute right-4 top-3 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <select x-model="filterStatus" class="bg-gray-50 border-0 rounded-2xl py-3 px-4 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <option value="">Todos los estados</option>
                        <option value="draft">Borrador</option>
                        <option value="processing">Procesando</option>
                        <option value="ready">Completado</option>
                        <option value="error">Error</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition duration-300 hover:shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                        <tr>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Estudiante</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Texto</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Docente</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Estado</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Precisión / Nivel</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="reading in paginatedReadings" :key="reading.id">
                            <tr class="border-b border-gray-200 hover:bg-purple-50 transition duration-200">
                                <td class="p-3">
                                    <div class="font-semibold" x-text="reading.student.apellidos + ', ' + reading.student.nombres"></div>
                                    <div class="text-xs text-gray-500" x-text="reading.student.grado + '°'"></div>
                                </td>
                                <td class="p-3 font-semibold" x-text="reading.text.titulo"></td>
                                <td class="p-3">
                                    <span x-text="reading.teacher ? reading.teacher.apellidos + ', ' + reading.teacher.nombres : '—'" 
                                          :class="!reading.teacher ? 'text-gray-400 italic' : ''"></span>
                                </td>
                                <td class="p-3">
                                    <span x-bind:class="{
                                        'bg-yellow-100 text-yellow-800 rounded-xl px-3 py-1 text-xs font-bold': reading.status === 'draft',
                                        'bg-blue-100 text-blue-800 rounded-xl px-3 py-1 text-xs font-bold': reading.status === 'processing',
                                        'bg-green-100 text-green-800 rounded-xl px-3 py-1 text-xs font-bold': reading.status === 'ready',
                                        'bg-red-100 text-red-800 rounded-xl px-3 py-1 text-xs font-bold': reading.status === 'error'
                                    }" x-text="getStatusText(reading.status)"></span>
                                </td>
                                <td class="p-3">
                                    <template x-if="reading.precision !== null && reading.wer !== null">
                                        <div class="flex flex-col gap-1">
                                            <span class="bg-green-100 text-green-800 rounded-xl px-3 py-1 text-xs font-bold" 
                                                  x-text="reading.precision.toFixed(2) + '%'"></span>
                                            <span class="rounded-xl px-3 py-1 text-[0.65rem] font-semibold text-center"
                                                  :class="getLevelColor(getLevel(reading.precision, reading.wer))"
                                                  x-text="getLevel(reading.precision, reading.wer)">
                                            </span>
                                        </div>
                                    </template>
                                    <template x-if="reading.precision === null || reading.wer === null">
                                        <span class="text-gray-400 text-xs">—</span>
                                    </template>
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <button @click="viewReading(reading)" 
                                                class="bg-green-500 hover:bg-green-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                        <button @click="editReading(reading)" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </button>
                                        <button @click="deleteReading(reading)" 
                                                class="bg-red-500 hover:bg-red-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredReadings.length === 0">
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-book-open text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg" style="font-family: 'Comic Sans MS', cursive;">No se encontraron sesiones</p>
                                        <p class="mt-1 text-sm">Crea una nueva sesión de lectura para comenzar</p>
                                        <button @click="openCreateModal()" class="mt-3 bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-xl text-sm transition duration-300">
                                            Crear Sesión
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-600 mb-3 md:mb-0 text-sm">
                    Mostrando <span x-text="showingFrom"></span> a <span x-text="showingTo"></span> de <span x-text="filteredReadings.length"></span> sesiones
                </div>
                <div class="flex space-x-1">
                    <button 
                        @click="currentPage--" 
                        :disabled="currentPage === 1"
                        class="py-2 px-3 bg-white border border-gray-300 text-gray-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 text-sm transition duration-300">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <template x-for="page in totalPages" :key="page">
                        <button 
                            @click="currentPage = page"
                            :class="currentPage === page ? 'bg-purple-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                            class="py-2 px-3 border border-gray-300 rounded-lg text-sm transition duration-300"
                            x-text="page">
                        </button>
                    </template>
                    
                    <button 
                        @click="currentPage++" 
                        :disabled="currentPage === totalPages"
                        class="py-2 px-3 bg-white border border-gray-300 text-gray-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 text-sm transition duration-300">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h5 class="text-xl sm:text-2xl" style="font-family: 'Comic Sans MS', cursive;" 
                            x-text="editingReading ? 'Editar Sesión' : 'Nueva Sesión de Lectura'"></h5>
                        <button type="button" @click="closeModal()" class="text-white text-xl sm:text-2xl hover:text-yellow-200 transition duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form id="readingForm" @submit.prevent="saveReading()">
                        <div class="grid grid-cols-1 gap-4">
                            <input type="hidden" x-model="formData.id">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estudiante *</label>
                                <select x-model="formData.student_id" required
                                        class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                                    <option value="">Selecciona un estudiante</option>
                                    <template x-for="student in studentsData" :key="student.id">
                                        <option :value="student.id" x-text="student.apellidos + ', ' + student.nombres + ' (' + student.grado + '°)'"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Texto *</label>
                                <select x-model="formData.text_id" required
                                        class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                                    <option value="">Selecciona un texto</option>
                                    <template x-for="text in textsData" :key="text.id">
                                        <option :value="text.id" 
                                                x-text="text.titulo + (text.tema ? ' - ' + text.tema : '') + ' (' + text.palabras_totales + ' palabras)'"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Docente (Opcional)</label>
                                <select x-model="formData.teacher_id"
                                        class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                                    <option value="">Sin docente asignado</option>
                                    <template x-for="teacher in teachersData" :key="teacher.id">
                                        <option :value="teacher.id" x-text="teacher.apellidos + ', ' + teacher.nombres"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition duration-300 text-sm">
                        Cancelar
                    </button>
                    <button type="button" @click="saveReading()" :disabled="saving" 
                            class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-4 rounded-xl shadow-md text-sm transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="editingReading ? 'Actualizar' : 'Guardar'"></span> Sesión
                    </button>
                </div>
            </div>
        </div>
        <div x-show="isViewModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.away="closeViewModal()">
            <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h5 class="text-xl sm:text-2xl" style="font-family: 'Comic Sans MS', cursive;" 
                            x-text="viewingReading ? 'Sesión de ' + viewingReading.student.apellidos + ', ' + viewingReading.student.nombres : ''"></h5>
                        <button type="button" @click="closeViewModal()" class="text-white text-xl sm:text-2xl hover:text-yellow-200 transition duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <template x-if="viewingReading">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold">Estudiante:</span>
                                    <span class="ml-2" x-text="viewingReading.student.apellidos + ', ' + viewingReading.student.nombres + ' (' + viewingReading.student.grado + '°)'"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Texto:</span>
                                    <span class="ml-2" x-text="viewingReading.text.titulo"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Docente:</span>
                                    <span class="ml-2" x-text="viewingReading.teacher ? viewingReading.teacher.apellidos + ', ' + viewingReading.teacher.nombres : '—'"></span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="font-semibold">Estado:</span>
                                <span x-bind:class="{
                                    'bg-yellow-100 text-yellow-800 rounded-xl px-3 py-1 text-xs font-bold': viewingReading.status === 'draft',
                                    'bg-blue-100 text-blue-800 rounded-xl px-3 py-1 text-xs font-bold': viewingReading.status === 'processing',
                                    'bg-green-100 text-green-800 rounded-xl px-3 py-1 text-xs font-bold': viewingReading.status === 'ready',
                                    'bg-red-100 text-red-800 rounded-xl px-3 py-1 text-xs font-bold': viewingReading.status === 'error'
                                }" x-text="getStatusText(viewingReading.status)"></span>
                            </div>

                            <template x-if="viewingReading.status === 'ready'">
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-4">Resultados del Análisis:</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div class="text-center p-3 bg-blue-50 rounded-xl">
                                            <div class="text-2xl font-bold text-blue-600" x-text="viewingReading.precision.toFixed(2) + '%'"></div>
                                            <div class="text-xs text-blue-600">Precisión</div>
                                        </div>
                                        <div class="text-center p-3 bg-purple-50 rounded-xl">
                                            <div class="text-2xl font-bold text-purple-600" x-text="viewingReading.wer.toFixed(2) + '%'"></div>
                                            <div class="text-xs text-purple-600">Tasa de Error</div>
                                        </div>
                                        <div class="text-center p-3 rounded-xl"
                                             :class="getLevelColor(getLevel(viewingReading.precision, viewingReading.wer))">
                                            <div class="text-xl font-bold" 
                                                 x-text="getLevel(viewingReading.precision, viewingReading.wer) || 'Sin evaluación'"></div>
                                            <div class="text-xs mt-1"
                                                 x-text="getLevelFeedback(getLevel(viewingReading.precision, viewingReading.wer))"></div>
                                        </div>
                                    </div>

                                    <template x-if="viewingReading.velocidad_ppm">
                                        <div class="text-center p-3 bg-green-50 rounded-xl mb-4">
                                            <div class="text-xl font-bold text-green-600" x-text="viewingReading.velocidad_ppm + ' ppm'"></div>
                                            <div class="text-xs text-green-600">Velocidad de Lectura</div>
                                        </div>
                                    </template>

                                    <div class="mt-4">
                                        <h4 class="font-semibold text-gray-700 mb-2">Transcripción:</h4>
                                        <div class="bg-gray-50 rounded-xl p-3 max-h-40 overflow-y-auto">
                                            <pre class="whitespace-pre-wrap text-sm" x-text="viewingReading.transcripcion || 'No hay transcripción disponible'"></pre>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-2">Texto para Leer:</h3>
                                <div class="bg-gray-50 rounded-2xl p-4 max-h-60 overflow-y-auto">
                                    <pre class="whitespace-pre-wrap text-sm" x-text="viewingReading.text.texto_plano"></pre>
                                </div>
                            </div>

                            <div x-data="recordingApp(viewingReading.id)">
                                <h3 class="font-semibold text-gray-800 mb-2">Grabación de Lectura:</h3>
                                <div class="space-y-4">
                                    <div class="flex gap-3">
                                        <button @click="startRecording()" 
                                                :disabled="isRecording"
                                                class="flex-1 bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white font-bold py-3 px-4 rounded-xl transition duration-300">
                                            <i class="fas fa-circle mr-2"></i>
                                            <span x-text="isRecording ? 'Grabando...' : 'Iniciar Grabación'"></span>
                                        </button>
                                        <button @click="stopRecording()" 
                                                :disabled="!isRecording"
                                                class="flex-1 bg-red-500 hover:bg-red-600 disabled:bg-red-300 text-white font-bold py-3 px-4 rounded-xl transition duration-300">
                                            <i class="fas fa-stop mr-2"></i>
                                            Detener
                                        </button>
                                    </div>
                                    <div x-show="isRecording" class="flex items-center justify-center p-3 bg-red-50 rounded-xl">
                                        <div class="flex items-center text-red-600">
                                            <div class="w-3 h-3 bg-red-600 rounded-full animate-pulse mr-2"></div>
                                            <span class="text-sm font-semibold">Grabando... <span x-text="formatTime(recordingTime)"></span></span>
                                        </div>
                                    </div>

                                    <div x-show="audioUrl">
                                        <audio :src="audioUrl" controls class="w-full rounded-xl"></audio>
                                    </div>

                                    <div x-show="audioBlob" class="flex gap-3 mt-4">
                                        <button type="button" @click="submitAudio()" 
                                                class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-4 rounded-xl transition duration-300">
                                            <i class="fas fa-upload mr-2"></i>
                                            Subir Grabación
                                        </button>
                                        <button type="button" @click="clearRecording()" 
                                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition duration-300">
                                            <i class="fas fa-trash mr-2"></i>
                                            Descartar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div x-show="viewingReading.audio_path && viewingReading.status === 'processing'">
                                <button type="button" @click="runASR()" 
                                        class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl transition duration-300 mt-4">
                                    <i class="fas fa-robot mr-2"></i>
                                    Ejecutar Análisis ASR
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="p-4 border-t border-gray-200 flex justify-end">
                    <button type="button" @click="closeViewModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition duration-300 text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function readingApp() {
            return {
                readingsData: [],
                studentsData: [],
                teachersData: [],
                textsData: [],
                searchTerm: '',
                filterStatus: '',
                currentPage: 1,
                readingsPerPage: 10,
                isModalOpen: false,
                isViewModalOpen: false,
                editingReading: null,
                viewingReading: null,
                saving: false,
                stats: {
                    total: 0,
                    completed: 0,
                    processing: 0,
                    error: 0,
                },
                formData: {
                    id: '',
                    student_id: '',
                    text_id: '',
                    teacher_id: '',
                },
                
                get filteredReadings() {
                    let filtered = this.readingsData;
                    
                    if (this.searchTerm) {
                        const term = this.searchTerm.toLowerCase();
                        filtered = filtered.filter(reading => 
                            reading.student.apellidos.toLowerCase().includes(term) ||
                            reading.student.nombres.toLowerCase().includes(term) ||
                            reading.text.titulo.toLowerCase().includes(term) ||
                            (reading.teacher && reading.teacher.apellidos.toLowerCase().includes(term)) ||
                            (reading.teacher && reading.teacher.nombres.toLowerCase().includes(term))
                        );
                    }
                    
                    if (this.filterStatus) {
                        filtered = filtered.filter(reading => reading.status === this.filterStatus);
                    }
                    
                    return filtered;
                },
                
                get paginatedReadings() {
                    const startIndex = (this.currentPage - 1) * this.readingsPerPage;
                    return this.filteredReadings.slice(startIndex, startIndex + this.readingsPerPage);
                },
                
                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredReadings.length / this.readingsPerPage));
                },
                
                get showingFrom() {
                    if (this.filteredReadings.length === 0) return 0;
                    return (this.currentPage - 1) * this.readingsPerPage + 1;
                },
                
                get showingTo() {
                    const end = this.showingFrom + this.readingsPerPage - 1;
                    return Math.min(end, this.filteredReadings.length);
                },
                
                getStatusText(status) {
                    const statusMap = {
                        'draft': 'Borrador',
                        'processing': 'Procesando',
                        'ready': 'Completado',
                        'error': 'Error',
                    };
                    return statusMap[status] || status;
                },

                getLevel(precision, wer) {
                    if (precision == null || wer == null) return 'Sin datos';
                    if (precision >= 90 && wer <= 10) return 'Excelente';
                    if (precision >= 75 && wer <= 25) return 'Buena';
                    if (precision >= 50 && wer <= 50) return 'En proceso';
                    return 'Necesita refuerzo';
                },

                getLevelColor(level) {
                    switch (level) {
                        case 'Excelente':
                            return 'bg-green-100 text-green-800';
                        case 'Buena':
                            return 'bg-blue-100 text-blue-800';
                        case 'En proceso':
                            return 'bg-yellow-100 text-yellow-800';
                        case 'Necesita refuerzo':
                            return 'bg-red-100 text-red-800';
                        default:
                            return 'bg-gray-100 text-gray-600';
                    }
                },

                getLevelFeedback(level) {
                    switch (level) {
                        case 'Excelente':
                            return 'Lectura muy precisa, prácticamente igual al texto original.';
                        case 'Buena':
                            return 'Lectura adecuada, con errores menores que no afectan demasiado la comprensión.';
                        case 'En proceso':
                            return 'Lectura con errores frecuentes, pero mantiene algunos fragmentos del texto.';
                        case 'Necesita refuerzo':
                            return 'Lectura muy diferente al texto objetivo; se requiere mayor apoyo y práctica.';
                        default:
                            return 'No hay suficientes datos para evaluar el desempeño.';
                    }
                },
                
                calculateStats() {
                    this.stats.total = this.readingsData.length;
                    this.stats.completed = this.readingsData.filter(r => r.status === 'ready').length;
                    this.stats.processing = this.readingsData.filter(r => r.status === 'processing').length;
                    this.stats.error = this.readingsData.filter(r => r.status === 'error').length;
                },
                
                openCreateModal() {
                    this.editingReading = null;
                    this.formData = {
                        id: '',
                        student_id: '',
                        text_id: '',
                        teacher_id: '',
                    };
                    this.isModalOpen = true;
                },
                
                editReading(reading) {
                    this.editingReading = reading;
                    this.formData = {
                        id: reading.id,
                        student_id: reading.student_id,
                        text_id: reading.text_id,
                        teacher_id: reading.teacher_id || '',
                    };
                    this.isModalOpen = true;
                },
                
                viewReading(reading) {
                    this.viewingReading = JSON.parse(JSON.stringify(reading));
                    this.isViewModalOpen = true;
                },
                
                closeModal() {
                    this.isModalOpen = false;
                    this.editingReading = null;
                    this.saving = false;
                },
                
                closeViewModal() {
                    this.isViewModalOpen = false;
                    this.viewingReading = null;
                },

                async runASR() {
                    if (!this.viewingReading) return;
                    const id = this.viewingReading.id;

                    Swal.fire({
                        title: 'Ejecutando Análisis ASR...',
                        text: 'Procesando la transcripción y métricas',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    try {
                        const response = await fetch(`/admin/readings/${id}/asr`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'No se pudo completar el ASR');
                        }

                        const idx = this.readingsData.findIndex(r => r.id === id);
                        if (idx !== -1) {
                            this.readingsData[idx] = result.reading;
                        }

                        this.viewingReading = result.reading;
                        this.calculateStats();

                        Swal.fire({
                            icon: 'success',
                            title: 'ASR completado',
                            text: result.message || 'Análisis ASR completado correctamente',
                            confirmButtonColor: '#667eea'
                        });
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en ASR',
                            text: e.message,
                            confirmButtonColor: '#667eea'
                        });
                    }
                },
                
                async saveReading() {
                    if (!this.formData.student_id || !this.formData.text_id) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campos obligatorios',
                            text: 'Por favor, selecciona un estudiante y un texto',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    
                    if (this.saving) return;
                    this.saving = true;
                    
                    Swal.fire({
                        title: this.editingReading ? 'Actualizando sesión...' : 'Creando sesión...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    try {
                        let url, method;
                        
                        if (this.editingReading) {
                            url = `/admin/readings/${this.formData.id}`;
                            method = 'PUT';
                        } else {
                            url = '{{ route("readings.store") }}';
                            method = 'POST';
                        }
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });
                        
                        const result = await response.json();
                        
                        if (!response.ok) {
                            let errorMessage = 'Error al guardar la sesión';
                            if (result.errors) {
                                const firstError = Object.values(result.errors)[0][0];
                                errorMessage = firstError;
                            } else if (result.message) {
                                errorMessage = result.message;
                            }
                            throw new Error(errorMessage);
                        }
                        
                        if (result.success) {
                            if (this.editingReading) {
                                const index = this.readingsData.findIndex(r => r.id === this.editingReading.id);
                                if (index !== -1) {
                                    this.readingsData[index] = result.reading;
                                }
                            } else {
                                this.readingsData.unshift(result.reading);
                            }
                            this.calculateStats();
                            
                            await Swal.fire({
                                icon: 'success',
                                title: this.editingReading ? '¡Sesión actualizada!' : '¡Sesión creada!',
                                text: result.message,
                                confirmButtonColor: '#667eea'
                            });
                            
                            this.closeModal();
                        } else {
                            throw new Error(result.message || 'Error al guardar la sesión');
                        }
                        
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar la sesión: ' + error.message,
                            confirmButtonColor: '#667eea'
                        });
                    } finally {
                        this.saving = false;
                    }
                },
                
                async deleteReading(reading) {
                    const result = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: `Vas a eliminar la sesión de lectura de ${reading.student.apellidos}, ${reading.student.nombres}. Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#667eea',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });
                    
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Eliminando sesión...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        
                        try {
                            const response = await fetch(`/admin/readings/${reading.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                            });
                            
                            const result = await response.json();
                            
                            if (response.ok && result.success) {
                                const index = this.readingsData.findIndex(r => r.id === reading.id);
                                if (index !== -1) {
                                    this.readingsData.splice(index, 1);
                                }
                                this.calculateStats();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Sesión eliminada!',
                                    text: result.message,
                                    confirmButtonColor: '#667eea'
                                });
                            } else {
                                throw new Error(result.message || 'Error al eliminar');
                            }
                            
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar la sesión: ' + error.message,
                                confirmButtonColor: '#667eea'
                            });
                        }
                    }
                },
                
                async loadReadings() {
                    try {
                        const response = await fetch('{{ route("readings.api.index") }}');
                        if (response.ok) {
                            this.readingsData = await response.json();
                            this.calculateStats();
                        }
                    } catch (error) {
                        this.readingsData = [];
                    }
                },
                
                async loadStudents() {
                    try {
                        const response = await fetch('/api/students');
                        if (response.ok) {
                            this.studentsData = await response.json();
                        }
                    } catch (error) {
                        this.studentsData = [];
                    }
                },
                
                async loadTeachers() {
                    try {
                        const response = await fetch('/api/teachers');
                        if (response.ok) {
                            this.teachersData = await response.json();
                        }
                    } catch (error) {
                        this.teachersData = [];
                    }
                },
                
                async loadTexts() {
                    try {
                        const response = await fetch('/admin/texts/api');
                        if (response.ok) {
                            this.textsData = await response.json();
                        }
                    } catch (error) {
                        this.textsData = [];
                    }
                },
                
                init() {
                    this.loadReadings();
                    this.loadStudents();
                    this.loadTeachers();
                    this.loadTexts();
                }
            }
        }

        function recordingApp(readingId) {
            return {
                isRecording: false,
                mediaRecorder: null,
                audioChunks: [],
                audioBlob: null,
                audioUrl: null,
                durationMs: 0,
                recordingTime: 0,
                recordingInterval: null,
                currentReadingId: readingId,
                
                startRecording() {
                    navigator.mediaDevices.getUserMedia({ audio: true })
                        .then(stream => {
                            this.mediaRecorder = new MediaRecorder(stream, { 
                                mimeType: 'audio/webm;codecs=opus' 
                            });
                            this.audioChunks = [];
                            
                            this.mediaRecorder.ondataavailable = (event) => {
                                if (event.data.size > 0) this.audioChunks.push(event.data);
                            };
                            
                            this.mediaRecorder.onstop = () => {
                                this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                                this.audioUrl = URL.createObjectURL(this.audioBlob);
                            };
                            
                            this.mediaRecorder.start(1000);
                            this.isRecording = true;
                            this.recordingTime = 0;
                            
                            this.recordingInterval = setInterval(() => {
                                this.recordingTime++;
                                this.durationMs = this.recordingTime * 1000;
                            }, 1000);
                            
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de micrófono',
                                text: 'No se pudo acceder al micrófono. Asegúrate de permitir el acceso.',
                                confirmButtonColor: '#667eea'
                            });
                        });
                },
                
                stopRecording() {
                    if (this.mediaRecorder && this.isRecording) {
                        this.mediaRecorder.stop();
                        this.isRecording = false;
                        clearInterval(this.recordingInterval);
                    }
                },
                
                clearRecording() {
                    this.audioBlob = null;
                    this.audioUrl = null;
                    this.durationMs = 0;
                    this.recordingTime = 0;
                },
                
                async submitAudio() {
                    if (!this.audioBlob) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sin grabación',
                            text: 'No hay ninguna grabación para subir.',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    if (!this.currentReadingId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo identificar la sesión de lectura.',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    
                    try {
                        Swal.fire({
                            title: 'Subiendo grabación...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        const formData = new FormData();
                        const audioFile = new File([this.audioBlob], `lectura_${this.currentReadingId}.webm`, {
                            type: 'audio/webm'
                        });
                        
                        formData.append('audio', audioFile);
                        formData.append('duration_ms', this.durationMs);
                        formData.append('_token', '{{ csrf_token() }}');
                        
                        const response = await fetch(`/admin/readings/${this.currentReadingId}/upload-audio`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Audio subido!',
                                text: result.message || 'La grabación se ha subido correctamente.',
                                confirmButtonColor: '#667eea'
                            });

                            // Recargar lista y refrescar sesión actual
                            const listResp = await fetch('{{ route("readings.api.index") }}');
                            if (listResp.ok) {
                                const all = await listResp.json();
                                const appRoot = document.querySelector('[x-data="readingApp()"]');
                                if (window.Alpine && appRoot) {
                                    const app = Alpine.$data(appRoot);
                                    app.readingsData = all;
                                    app.calculateStats();
                                    const updated = all.find(r => r.id === app.viewingReading?.id);
                                    if (updated) app.viewingReading = updated;
                                }
                            }
                        } else {
                            throw new Error(result.message || `Error ${response.status}`);
                        }

                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al subir',
                            text: 'No se pudo subir la grabación: ' + error.message,
                            confirmButtonColor: '#667eea'
                        });
                    }
                },
                
                formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js no está cargado');
                return;
            }
            Alpine.data('readingApp', readingApp);
        });
    </script>
</x-app-layout>
