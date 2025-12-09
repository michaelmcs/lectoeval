<x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6" x-data="compareApp()">
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
                    <div class="text-center py-4">
                        <i class="fas fa-search text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">No hay sesiones con análisis completado.</p>
                        <p class="text-gray-400 text-xs mt-1">
                            Las sesiones aparecerán aquí después de procesar el audio con ASR.
                        </p>
                    </div>
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
                                        <span x-text="reading.precision ? reading.precision.toFixed(1) : '0.0'"></span>% /
                                        <span x-text="reading.wer ? reading.wer.toFixed(1) : '0.0'"></span>% WER
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

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

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-xs">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <div class="text-lg font-bold text-blue-600" 
                                     x-text="selectedReading.precision ? selectedReading.precision.toFixed(2) + '%' : '—'"></div>
                                <div class="mt-1 text-blue-700">Precisión</div>
                            </div>
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <div class="text-lg font-bold text-purple-600" 
                                     x-text="selectedReading.wer ? selectedReading.wer.toFixed(2) + '%' : '—'"></div>
                                <div class="mt-1 text-purple-700">Tasa de Error (WER)</div>
                            </div>
                            <div class="p-3 bg-green-50 rounded-xl">
                                <div class="text-lg font-bold text-green-600" 
                                     x-text="selectedReading.velocidad_ppm ? selectedReading.velocidad_ppm + ' ppm' : '—'"></div>
                                <div class="mt-1 text-green-700">Velocidad</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <div class="text-lg font-bold text-gray-700" 
                                     x-text="selectedReading.duration_ms ? (selectedReading.duration_ms / 1000).toFixed(1) + ' s' : '—'"></div>
                                <div class="mt-1 text-gray-700">Duración</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-2">Texto objetivo</h3>
                                <div class="bg-gray-50 rounded-2xl p-3 max-h-60 overflow-y-auto">
                                    <template x-if="selectedReading.text && selectedReading.text.texto_plano">
                                        <pre class="whitespace-pre-wrap text-xs"
                                             x-text="selectedReading.text.texto_plano"></pre>
                                    </template>
                                    <template x-if="!selectedReading.text || !selectedReading.text.texto_plano">
                                        <p class="text-gray-400 text-xs">Texto no disponible</p>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-2">Transcripción del estudiante</h3>
                                <div class="bg-gray-50 rounded-2xl p-3 max-h-60 overflow-y-auto">
                                    <template x-if="selectedReading.transcripcion">
                                        <pre class="whitespace-pre-wrap text-xs"
                                             x-text="selectedReading.transcripcion"></pre>
                                    </template>
                                    <template x-if="!selectedReading.transcripcion">
                                        <p class="text-gray-400 text-xs">No hay transcripción almacenada.</p>
                                    </template>
                                </div>
                            </div>
                        </div>

  
                        <template x-if="selectedReading.resultado_json && typeof selectedReading.resultado_json === 'string'">
                            <div class="border-t pt-4">
                                <h3 class="font-semibold text-gray-800 mb-2">Análisis Detallado</h3>
                                <div class="space-y-4">
                                    <!-- Estadísticas -->
                                    <template x-if="parseJson(selectedReading.resultado_json)">
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <template x-if="getOmissionsCount(selectedReading.resultado_json) > 0">
                                                <div class="p-3 bg-red-50 rounded-xl">
                                                    <div class="text-lg font-bold text-red-600" 
                                                         x-text="getOmissionsCount(selectedReading.resultado_json)"></div>
                                                    <div class="mt-1 text-red-700 text-xs">Palabras omitidas</div>
                                                </div>
                                            </template>
                                            <template x-if="getSubstitutionsCount(selectedReading.resultado_json) > 0">
                                                <div class="p-3 bg-yellow-50 rounded-xl">
                                                    <div class="text-lg font-bold text-yellow-600" 
                                                         x-text="getSubstitutionsCount(selectedReading.resultado_json)"></div>
                                                    <div class="mt-1 text-yellow-700 text-xs">Sustituciones</div>
                                                </div>
                                            </template>
                                            <template x-if="getInsertionsCount(selectedReading.resultado_json) > 0">
                                                <div class="p-3 bg-blue-50 rounded-xl">
                                                    <div class="text-lg font-bold text-blue-600" 
                                                         x-text="getInsertionsCount(selectedReading.resultado_json)"></div>
                                                    <div class="mt-1 text-blue-700 text-xs">Palabras adicionales</div>
                                                </div>
                                            </template>
                                            <div class="p-3 bg-gray-50 rounded-xl">
                                                <div class="text-lg font-bold text-gray-600" 
                                                     x-text="getTotalErrors(selectedReading.resultado_json)"></div>
                                                <div class="mt-1 text-gray-700 text-xs">Errores totales</div>
                                            </div>
                                        </div>
                                    </template>

        
                                    <div class="bg-gray-50 rounded-2xl p-4 max-h-60 overflow-y-auto">
                                        <h4 class="font-semibold text-gray-700 mb-3 text-sm">Detalle de errores por palabra:</h4>
                                        <template x-if="parseJson(selectedReading.resultado_json)">
                                            <div class="space-y-2">
                                                <template x-for="(error, index) in getErrorDetails(selectedReading.resultado_json)" :key="index">
                                                    <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-200">
                                                        <div class="flex items-center space-x-3">
                                                            <span class="text-xs text-gray-500" x-text="'Pos: ' + (error.pos + 1)"></span>
                                                            <template x-if="error.target && !error.said">
                                                                <div class="flex items-center">
                                                                    <span class="text-red-500 line-through text-sm" x-text="error.target"></span>
                                                                    <i class="fas fa-arrow-right text-xs text-gray-400 mx-2"></i>
                                                                    <span class="text-gray-400 text-sm italic">(omisión)</span>
                                                                </div>
                                                            </template>
                                                            <template x-if="error.said && !error.target">
                                                                <div class="flex items-center">
                                                                    <span class="text-gray-400 text-sm italic">(adicional)</span>
                                                                    <i class="fas fa-arrow-right text-xs text-gray-400 mx-2"></i>
                                                                    <span class="text-blue-500 text-sm" x-text="error.said"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="error.said && error.target">
                                                                <div class="flex items-center">
                                                                    <span class="text-gray-700 font-medium text-sm" x-text="error.target"></span>
                                                                    <i class="fas fa-arrow-right text-xs text-gray-400 mx-2"></i>
                                                                    <span class="text-yellow-600 text-sm" x-text="error.said"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <span class="text-xs px-2 py-1 rounded-full" 
                                                              :class="getErrorTypeClass(error)">
                                                            <span x-text="getErrorTypeLabel(error)"></span>
                                                        </span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!parseJson(selectedReading.resultado_json)">
                                            <p class="text-gray-400 text-xs">No se pudo parsear el análisis detallado.</p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

          
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button @click="downloadResults()"
                                    class="px-4 py-2 bg-purple-600 text-white text-sm rounded-xl hover:bg-purple-700 transition duration-200">
                                <i class="fas fa-download mr-2"></i>Descargar Resultados
                            </button>
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
                            (r.student && r.student.apellidos && r.student.apellidos.toLowerCase().includes(term)) ||
                            (r.student && r.student.nombres && r.student.nombres.toLowerCase().includes(term)) ||
                            (r.text && r.text.titulo && r.text.titulo.toLowerCase().includes(term))
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
                        if (!resp.ok) {
                            console.error('Error al cargar lecturas:', resp.statusText);
                            return;
                        }
                        this.readings = await resp.json();
                    } catch (e) {
                        console.error('Error en fetch:', e);
                        this.readings = [];
                    }
                },

                // Funciones para procesar el JSON de análisis
                parseJson(jsonString) {
                    if (!jsonString) return null;
                    try {
                        // Si ya es un objeto, devolverlo
                        if (typeof jsonString === 'object') return jsonString;
                        
                        // Si es un string, intentar parsearlo
                        if (typeof jsonString === 'string') {
                            // Limpiar el string si es necesario
                            let cleanString = jsonString;
                            if (cleanString.startsWith('"') && cleanString.endsWith('"')) {
                                cleanString = cleanString.slice(1, -1);
                            }
                            // Reemplazar comillas escapadas
                            cleanString = cleanString.replace(/\\"/g, '"');
                            return JSON.parse(cleanString);
                        }
                        return null;
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        return null;
                    }
                },

                getOmissionsCount(jsonString) {
                    const data = this.parseJson(jsonString);
                    if (!data || !Array.isArray(data)) return 0;
                    
                    // Contar omisiones (primer elemento con omissions)
                    const omissions = data.find(item => item.omissions !== undefined);
                    return omissions ? omissions.omissions : 0;
                },

                getSubstitutionsCount(jsonString) {
                    const data = this.parseJson(jsonString);
                    if (!data || !Array.isArray(data)) return 0;
                    
                    return data.filter(item => 
                        item.said && item.target && item.said !== item.target
                    ).length;
                },

                getInsertionsCount(jsonString) {
                    const data = this.parseJson(jsonString);
                    if (!data || !Array.isArray(data)) return 0;
                    
                    return data.filter(item => 
                        item.said && !item.target
                    ).length;
                },

                getTotalErrors(jsonString) {
                    return this.getOmissionsCount(jsonString) + 
                           this.getSubstitutionsCount(jsonString) + 
                           this.getInsertionsCount(jsonString);
                },

                getErrorDetails(jsonString) {
                    const data = this.parseJson(jsonString);
                    if (!data || !Array.isArray(data)) return [];
                    
                    // Filtrar solo los errores (excluir el contador de omisiones)
                    return data.filter(item => 
                        item.target !== undefined || item.said !== undefined
                    );
                },

                getErrorTypeClass(error) {
                    if (error.target && !error.said) {
                        return 'bg-red-100 text-red-800';
                    } else if (error.said && !error.target) {
                        return 'bg-blue-100 text-blue-800';
                    } else if (error.said && error.target) {
                        return 'bg-yellow-100 text-yellow-800';
                    }
                    return 'bg-gray-100 text-gray-800';
                },

                getErrorTypeLabel(error) {
                    if (error.target && !error.said) {
                        return 'Omisión';
                    } else if (error.said && !error.target) {
                        return 'Adicional';
                    } else if (error.said && error.target) {
                        return 'Sustitución';
                    }
                    return 'Desconocido';
                },

                downloadResults() {
                    if (!this.selectedReading) return;
                    
                    // Parsear resultado_json si es un string
                    let resultadoParsed = this.selectedReading.resultado_json;
                    if (typeof resultadoParsed === 'string') {
                        try {
                            let cleanString = resultadoParsed;
                            if (cleanString.startsWith('"') && cleanString.endsWith('"')) {
                                cleanString = cleanString.slice(1, -1);
                            }
                            cleanString = cleanString.replace(/\\"/g, '"');
                            resultadoParsed = JSON.parse(cleanString);
                        } catch (e) {
                            console.error('Error parsing JSON for download:', e);
                        }
                    }

                    const data = {
                        estudiante: this.selectedReading.student,
                        texto: this.selectedReading.text,
                        docente: this.selectedReading.teacher,
                        transcripcion: this.selectedReading.transcripcion,
                        metricas: {
                            precision: this.selectedReading.precision,
                            wer: this.selectedReading.wer,
                            velocidad_ppm: this.selectedReading.velocidad_ppm,
                            duracion: this.selectedReading.duration_ms
                        },
                        analisis_detallado: resultadoParsed,
                        resumen_errores: {
                            omisiones: this.getOmissionsCount(this.selectedReading.resultado_json),
                            sustituciones: this.getSubstitutionsCount(this.selectedReading.resultado_json),
                            inserciones: this.getInsertionsCount(this.selectedReading.resultado_json),
                            total_errores: this.getTotalErrors(this.selectedReading.resultado_json)
                        },
                        fecha_analisis: new Date().toISOString()
                    };
                    
                    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `lectura_${this.selectedReading.id}_${new Date().toISOString().split('T')[0]}.json`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
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
            
            // Asegurarse de que el componente esté registrado
            if (typeof Alpine.data('compareApp') === 'undefined') {
                Alpine.data('compareApp', compareApp);
            }
        });
    </script>


    <style>
        /* Scroll personalizado */
        .max-h-60::-webkit-scrollbar {
            width: 6px;
        }
        .max-h-60::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .max-h-60::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 10px;
        }
        .max-h-60::-webkit-scrollbar-thumb:hover {
            background: #a5b4fc;
        }


        pre {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            line-height: 1.5;
        }
    </style>
</x-app-layout>