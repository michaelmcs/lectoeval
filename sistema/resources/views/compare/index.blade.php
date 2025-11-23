<x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6" x-data="compareApp()">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800" style="font-family: 'Comic Sans MS', cursive;">
                    Comparador de Lecturas
                </h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">
                    Revisa en detalle la lectura del estudiante frente al texto original.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Lista de sesiones -->
            <div class="bg-white rounded-2xl shadow-lg p-4 md:col-span-1 max-h-[80vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-3" style="font-family: 'Comic Sans MS', cursive;">
                    Sesiones Analizadas
                </h2>

                <div class="mb-3">
                    <input type="text" x-model="searchTerm"
                           placeholder="Buscar por estudiante o texto..."
                           class="w-full bg-gray-50 border-0 rounded-2xl py-2.5 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                </div>

                <template x-if="filteredReadings.length === 0">
                    <p class="text-gray-500 text-sm">No hay sesiones con análisis completado.</p>
                </template>

                <div class="space-y-2">
                    <template x-for="reading in filteredReadings" :key="reading.id">
                        <button type="button"
                                @click="selectReading(reading)"
                                class="w-full text-left p-3 rounded-xl border transition duration-200"
                                :class="selectedReading && selectedReading.id === reading.id 
                                    ? 'border-purple-400 bg-purple-50' 
                                    : 'border-gray-200 hover:bg-gray-50'">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-semibold" 
                                         x-text="reading.student.apellidos + ', ' + reading.student.nombres"></div>
                                    <div class="text-xs text-gray-500 truncate" x-text="reading.text.titulo"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold"
                                         :class="getLevelColor(getLevel(reading.precision, reading.wer))"
                                         x-text="getLevel(reading.precision, reading.wer)">
                                    </div>
                                    <div class="text-[0.65rem] text-gray-500 mt-1">
                                        <span x-text="reading.precision.toFixed(1)"></span>% /
                                        <span x-text="reading.wer.toFixed(1)"></span>% WER
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Detalle de sesión -->
            <div class="md:col-span-2">
                <template x-if="!selectedReading">
                    <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-book-open text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm">Selecciona una sesión de lectura en la lista para ver los detalles.</p>
                        </div>
                    </div>
                </template>

                <template x-if="selectedReading">
                    <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                        <!-- Info general -->
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <div class="space-y-1 text-sm">
                                <div>
                                    <span class="font-semibold">Estudiante:</span>
                                    <span class="ml-1" 
                                          x-text="selectedReading.student.apellidos + ', ' + selectedReading.student.nombres + ' (' + selectedReading.student.grado + '°)'"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Texto:</span>
                                    <span class="ml-1" x-text="selectedReading.text.titulo"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Docente:</span>
                                    <span class="ml-1" 
                                          x-text="selectedReading.teacher 
                                                    ? selectedReading.teacher.apellidos + ', ' + selectedReading.teacher.nombres 
                                                    : '—'"></span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="text-xs px-3 py-1 rounded-full"
                                      :class="getLevelColor(getLevel(selectedReading.precision, selectedReading.wer))"
                                      x-text="getLevel(selectedReading.precision, selectedReading.wer)">
                                </span>
                                <p class="text-[0.7rem] text-gray-500 text-right max-w-xs"
                                   x-text="getLevelFeedback(getLevel(selectedReading.precision, selectedReading.wer))"></p>
                            </div>
                        </div>

                        <!-- Métricas -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-xs">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <div class="text-lg font-bold text-blue-600" 
                                     x-text="selectedReading.precision.toFixed(2) + '%'"></div>
                                <div class="mt-1 text-blue-700">Precisión</div>
                            </div>
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <div class="text-lg font-bold text-purple-600" 
                                     x-text="selectedReading.wer.toFixed(2) + '%'"></div>
                                <div class="mt-1 text-purple-700">Tasa de Error (WER)</div>
                            </div>
                            <div class="p-3 bg-green-50 rounded-xl">
                                <div class="text-lg font-bold text-green-600" 
                                     x-text="selectedReading.velocidad_ppm ? selectedReading.velocidad_ppm + ' ppm' : '—'"></div>
                                <div class="mt-1 text-green-700">Velocidad</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <div class="text-lg font-bold text-gray-700" 
                                     x-text="(selectedReading.duration_ms ? (selectedReading.duration_ms / 1000).toFixed(1) : '—') + ' s'"></div>
                                <div class="mt-1 text-gray-700">Duración</div>
                            </div>
                        </div>

                        <!-- Comparación de textos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-2">Texto objetivo</h3>
                                <div class="bg-gray-50 rounded-2xl p-3 max-h-60 overflow-y-auto">
                                    <pre class="whitespace-pre-wrap text-xs"
                                         x-text="selectedReading.text.texto_plano"></pre>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-2">Transcripción del estudiante</h3>
                                <div class="bg-gray-50 rounded-2xl p-3 max-h-60 overflow-y-auto">
                                    <pre class="whitespace-pre-wrap text-xs"
                                         x-text="selectedReading.transcripcion || 'No hay transcripción almacenada.'"></pre>
                                </div>
                            </div>
                        </div>

                        <!-- (Espacio futuro) Resumen de errores -->
                        <div class="mt-4 text-xs text-gray-500">
                            <p>
                                Nota: en esta versión solo se muestran las métricas globales y los textos completos.
                                Más adelante aquí se puede usar <code>resultado_json</code> para resaltar palabras omitidas,
                                sustituciones y errores específicos.
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function compareApp() {
            return {
                readings: [],
                selectedReading: null,
                searchTerm: '',

                get filteredReadings() {
                    let list = this.readings.filter(r => r.status === 'ready');

                    if (this.searchTerm) {
                        const term = this.searchTerm.toLowerCase();
                        list = list.filter(r =>
                            r.student.apellidos.toLowerCase().includes(term) ||
                            r.student.nombres.toLowerCase().includes(term) ||
                            r.text.titulo.toLowerCase().includes(term)
                        );
                    }

                    return list;
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

                selectReading(reading) {
                    this.selectedReading = reading;
                },

                async loadReadings() {
                    try {
                        const resp = await fetch('{{ route("readings.api.index") }}');
                        if (!resp.ok) return;
                        this.readings = await resp.json();
                    } catch (e) {
                        this.readings = [];
                    }
                },

                init() {
                    this.loadReadings();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js no está cargado');
                return;
            }
            Alpine.data('compareApp', compareApp);
        });
    </script>
</x-app-layout>
