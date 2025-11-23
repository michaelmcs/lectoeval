<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6" x-data="textApp()">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800" style="font-family: 'Comic Sans MS', cursive;">Nuestros Textos</h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">Gestiona los textos y cuentos para las lecturas</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <div class="export-buttons flex gap-2">
                    <button @click="exportToPDF()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-4 rounded-xl flex items-center shadow-md text-sm transition duration-300">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                    <button @click="exportToExcel()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-4 rounded-xl flex items-center shadow-md text-sm transition duration-300">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </div>
                <button @click="openModal()" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2.5 px-4 sm:px-6 rounded-xl flex items-center shadow-lg text-sm sm:text-base transition duration-300">
                    <i class="fas fa-plus-circle mr-2"></i> Agregar Texto
                </button>
            </div>
        </div>

        <!-- Panel de Búsqueda y Filtros -->
        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6 transition duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" x-model="searchTerm" placeholder="Buscar textos por título, tema o descripción..." class="w-full bg-gray-50 border-0 rounded-2xl py-3 px-5 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <div class="absolute right-4 top-3 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <select x-model="filterStatus" class="bg-gray-50 border-0 rounded-2xl py-3 px-4 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <option value="">Todos los estados</option>
                        <option value="ok">OCR Completado</option>
                        <option value="pending">Pendiente OCR</option>
                        <option value="error">Error OCR</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Textos -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition duration-300 hover:shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                        <tr>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Título</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Tema</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Palabras</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Estado OCR</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="text in paginatedTexts" :key="text.id">
                            <tr class="border-b border-gray-200 hover:bg-purple-50 transition duration-200">
                                <td class="p-3 font-semibold" x-text="text.titulo"></td>
                                <td class="p-3">
                                    <span x-text="text.tema || 'Sin tema'" :class="!text.tema ? 'text-gray-400 italic' : ''"></span>
                                </td>
                                <td class="p-3">
                                    <span class="bg-blue-100 text-blue-800 rounded-xl px-3 py-1 text-xs font-bold" x-text="text.palabras_totales"></span>
                                </td>
                                <td class="p-3">
                                    <span x-bind:class="{
                                        'bg-green-100 text-green-800 rounded-xl px-3 py-1 text-xs font-bold': text.ocr_status === 'ok',
                                        'bg-yellow-100 text-yellow-800 rounded-xl px-3 py-1 text-xs font-bold': text.ocr_status === 'pending',
                                        'bg-red-100 text-red-800 rounded-xl px-3 py-1 text-xs font-bold': text.ocr_status === 'error'
                                    }" x-text="getStatusText(text.ocr_status)"></span>
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <button @click="viewText(text)" class="bg-green-500 hover:bg-green-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                        <button @click="editText(text)" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </button>
                                        <button @click="deleteText(text)" class="bg-red-500 hover:bg-red-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredTexts.length === 0">
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-book text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg" style="font-family: 'Comic Sans MS', cursive;">No se encontraron textos</p>
                                        <p class="mt-1 text-sm">Intenta con otros términos de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="p-3 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-600 mb-3 md:mb-0 text-sm">
                    Mostrando <span x-text="showingFrom"></span> a <span x-text="showingTo"></span> de <span x-text="filteredTexts.length"></span> textos
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

        <!-- Modal para Agregar/Editar Texto -->
        <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h5 class="text-xl sm:text-2xl" style="font-family: 'Comic Sans MS', cursive;" x-text="editingText ? 'Editar Texto' : 'Agregar Nuevo Texto'"></h5>
                        <button type="button" @click="closeModal()" class="text-white text-xl sm:text-2xl hover:text-yellow-200 transition duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form id="textForm" @submit.prevent="saveText()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="hidden" x-model="formData.id">
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                                <input type="text" x-model="formData.titulo" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Título del texto o cuento">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tema</label>
                                <input type="text" x-model="formData.tema" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Tema principal del texto">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea x-model="formData.descripcion" rows="2" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Breve descripción del texto"></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Texto (opcional)</label>
                                <textarea x-model="formData.texto_plano" rows="6" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Pega aquí el texto directamente..."></textarea>
                                <div class="text-xs text-gray-500 mt-1">O sube un PDF para extraer el texto automáticamente</div>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">PDF (opcional)</label>
                                <input type="file" id="pdfFile" @change="handleFileUpload($event)" accept=".pdf" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                                <div class="text-xs text-gray-500 mt-1">Sube un archivo PDF para extraer el texto automáticamente</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition duration-300 text-sm">
                        Cancelar
                    </button>
                    <button type="button" @click="saveText()" :disabled="saving" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-2 px-4 rounded-xl shadow-md text-sm transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="editingText ? 'Actualizar' : 'Guardar'"></span> Texto
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal para Ver Texto -->
        <div x-show="isViewModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.away="closeViewModal()">
            <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h5 class="text-xl sm:text-2xl" style="font-family: 'Comic Sans MS', cursive;" x-text="viewingText ? viewingText.titulo : ''"></h5>
                        <button type="button" @click="closeViewModal()" class="text-white text-xl sm:text-2xl hover:text-yellow-200 transition duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <template x-if="viewingText">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold">Tema:</span>
                                    <span x-text="viewingText.tema || 'Sin tema'" class="ml-2" :class="!viewingText.tema ? 'text-gray-400 italic' : ''"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Palabras:</span>
                                    <span class="ml-2 bg-blue-100 text-blue-800 rounded-xl px-2 py-1 text-xs font-bold" x-text="viewingText.palabras_totales"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Estado:</span>
                                    <span x-bind:class="{
                                        'bg-green-100 text-green-800 rounded-xl px-2 py-1 text-xs font-bold ml-2': viewingText.ocr_status === 'ok',
                                        'bg-yellow-100 text-yellow-800 rounded-xl px-2 py-1 text-xs font-bold ml-2': viewingText.ocr_status === 'pending',
                                        'bg-red-100 text-red-800 rounded-xl px-2 py-1 text-xs font-bold ml-2': viewingText.ocr_status === 'error'
                                    }" x-text="getStatusText(viewingText.ocr_status)"></span>
                                </div>
                            </div>
                            
                            <div x-show="viewingText.descripcion">
                                <span class="font-semibold">Descripción:</span>
                                <p class="mt-1 text-gray-700" x-text="viewingText.descripcion"></p>
                            </div>
                            
                            <div>
                                <span class="font-semibold">Contenido:</span>
                                <div class="mt-2 p-4 bg-gray-50 rounded-2xl max-h-96 overflow-y-auto">
                                    <pre class="whitespace-pre-wrap text-sm font-sans" x-text="viewingText.texto_plano || 'No hay texto disponible'"></pre>
                                </div>
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
        function textApp() {
            return {
                textsData: [],
                searchTerm: '',
                filterStatus: '',
                currentPage: 1,
                textsPerPage: 5,
                isModalOpen: false,
                isViewModalOpen: false,
                editingText: null,
                viewingText: null,
                saving: false,
                formData: {
                    id: '',
                    titulo: '',
                    tema: '',
                    descripcion: '',
                    texto_plano: '',
                    pdf_file: null
                },
                
                get filteredTexts() {
                    let filtered = this.textsData;
                    
                    if (this.searchTerm) {
                        const term = this.searchTerm.toLowerCase();
                        filtered = filtered.filter(text => 
                            text.titulo.toLowerCase().includes(term) ||
                            (text.tema && text.tema.toLowerCase().includes(term)) ||
                            (text.descripcion && text.descripcion.toLowerCase().includes(term))
                        );
                    }
                    
                    if (this.filterStatus) {
                        filtered = filtered.filter(text => text.ocr_status === this.filterStatus);
                    }
                    
                    return filtered;
                },
                
                get paginatedTexts() {
                    const startIndex = (this.currentPage - 1) * this.textsPerPage;
                    return this.filteredTexts.slice(startIndex, startIndex + this.textsPerPage);
                },
                
                get totalPages() {
                    return Math.ceil(this.filteredTexts.length / this.textsPerPage);
                },
                
                get showingFrom() {
                    return (this.currentPage - 1) * this.textsPerPage + 1;
                },
                
                get showingTo() {
                    const end = this.showingFrom + this.textsPerPage - 1;
                    return Math.min(end, this.filteredTexts.length);
                },
                
                getStatusText(status) {
                    const statusMap = {
                        'ok': 'Completado',
                        'pending': 'Pendiente',
                        'error': 'Error'
                    };
                    return statusMap[status] || status;
                },
                
                openModal(text = null) {
                    this.editingText = text;
                    if (text) {
                        this.formData = { ...text };
                    } else {
                        this.formData = {
                            id: '',
                            titulo: '',
                            tema: '',
                            descripcion: '',
                            texto_plano: '',
                            pdf_file: null
                        };
                    }
                    this.isModalOpen = true;
                },
                
                closeModal() {
                    this.isModalOpen = false;
                    this.editingText = null;
                    this.saving = false;
                    this.formData = {
                        id: '',
                        titulo: '',
                        tema: '',
                        descripcion: '',
                        texto_plano: '',
                        pdf_file: null
                    };
                    document.getElementById('pdfFile').value = '';
                },
                
                viewText(text) {
                    this.viewingText = text;
                    this.isViewModalOpen = true;
                },
                
                closeViewModal() {
                    this.isViewModalOpen = false;
                    this.viewingText = null;
                },


async handleFileUpload(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const file = event.target.files[0];
    if (file && file.type === 'application/pdf') {
        this.formData.pdf_file = file;
        
        Swal.fire({
            title: 'Procesando PDF...',
            text: 'Extrayendo texto del archivo',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        try {
            const formData = new FormData();
            formData.append('pdf', file);
            
            const response = await fetch('/admin/texts/process-pdf', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            
            if (!response.ok) {
                let errorMessage = `Error ${response.status} del servidor`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || 'Error al procesar el PDF';
                } catch (e) {
                    const errorText = await response.text();
                    if (errorText.includes('<!DOCTYPE') || errorText.length > 1000) {
                        errorMessage = 'Error interno del servidor';
                    } else {
                        try {
                            const jsonError = JSON.parse(errorText);
                            errorMessage = jsonError.message || errorText.substring(0, 200);
                        } catch {
                            errorMessage = errorText.substring(0, 200);
                        }
                    }
                }
                throw new Error(errorMessage);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // ASIGNAR EL TEXTO REAL EXTRAÍDO DEL PDF
                this.formData.texto_plano = result.text;
                this.formData.palabras_totales = result.word_count;
                
                console.log('Texto extraído del PDF:', {
                    palabras: result.word_count,
                    longitud: result.text.length,
                    preview: result.text.substring(0, 100),
                    file_info: result.file_info
                });
                
                // ESPERAR A QUE ALPINE.JS ACTUALICE
                await this.$nextTick();
                
                // ACTUALIZAR VISUALMENTE EL TEXTAREA
                const textarea = document.querySelector('textarea[x-model="formData.texto_plano"]');
                if (textarea) {
                    textarea.value = result.text;
                    // FORZAR ACTUALIZACIÓN
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    console.log('Textarea actualizado con texto real del PDF');
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'PDF procesado exitosamente',
                    html: `<strong>Texto extraído correctamente</strong><br>
                           Archivo: ${result.file_info?.name || file.name}<br>
                           Tamaño: ${result.file_info?.size_kb || Math.round(file.size / 1024)} KB<br>
                           Palabras: <strong>${result.word_count}</strong><br>
                           <small>El texto se ha cargado automáticamente en el campo de abajo</small>`,
                    confirmButtonColor: '#667eea'
                });
                
                // SCROLL AL TEXTAREA
                setTimeout(() => {
                    if (textarea) {
                        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 500);
                
            } else {
                throw new Error(result.message || 'Error al procesar el PDF');
            }
        } catch (error) {
            console.error('Error procesando PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error al procesar PDF',
                html: `<strong>No se pudo extraer texto del PDF</strong><br>
                       <small>${error.message}</small><br><br>
                       <strong>Posibles soluciones:</strong>
                       <ul style="text-align: left; margin: 10px 0;">
                         <li>• El PDF contiene imágenes escaneadas</li>
                         <li>• El PDF está protegido con contraseña</li>
                         <li>• El PDF no contiene texto seleccionable</li>
                         <li>• Puedes copiar y pegar el texto manualmente</li>
                       </ul>`,
                confirmButtonColor: '#667eea'
            });
        }
    } else if (file) {
        Swal.fire({
            icon: 'error',
            title: 'Formato no válido',
            text: 'Por favor, selecciona un archivo PDF',
            confirmButtonColor: '#667eea'
        });
    }
},


                async saveText() {
                    if (!this.formData.titulo.trim()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campo obligatorio',
                            text: 'Por favor, complete el campo Título',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    
                    if (this.saving) return;
                    this.saving = true;
                    
                    Swal.fire({
                        title: this.editingText ? 'Actualizando texto...' : 'Guardando texto...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    try {
                        const formData = new FormData();
                        formData.append('titulo', this.formData.titulo);
                        formData.append('tema', this.formData.tema || '');
                        formData.append('descripcion', this.formData.descripcion || '');
                        formData.append('texto_plano', this.formData.texto_plano || '');
                        
                        if (this.formData.pdf_file) {
                            formData.append('pdf', this.formData.pdf_file);
                        }
                        
                        let response;
                        
                        if (this.editingText) {
                            formData.append('_method', 'PUT');
                            response = await fetch(`/admin/texts/${this.formData.id}`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: formData
                            });
                        } else {
                            response = await fetch('{{ route("texts.store") }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: formData
                            });
                        }
                        
                        if (!response.ok) {
                            let errorMessage = `Error ${response.status} del servidor`;
                            
                            try {
                                const errorData = await response.text();
                                
                                // Si es HTML muy largo, mostrar mensaje corto
                                if (errorData.length > 1000 || errorData.includes('<!DOCTYPE')) {
                                    errorMessage = 'Error interno del servidor. Por favor, intenta nuevamente.';
                                } else {
                                    // Intentar parsear como JSON
                                    try {
                                        const jsonError = JSON.parse(errorData);
                                        errorMessage = jsonError.message || errorData.substring(0, 200);
                                    } catch {
                                        errorMessage = errorData.substring(0, 200);
                                    }
                                }
                            } catch (e) {
                                errorMessage = 'Error de conexión con el servidor';
                            }
                            
                            throw new Error(errorMessage);
                        }
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            if (this.editingText) {
                                const index = this.textsData.findIndex(t => t.id === this.editingText.id);
                                if (index !== -1) {
                                    this.textsData[index] = result.text;
                                }
                            } else {
                                this.textsData.unshift(result.text);
                            }
                            
                            await Swal.fire({
                                icon: 'success',
                                title: this.editingText ? '¡Texto actualizado!' : '¡Texto agregado!',
                                text: this.editingText ? 'La información del texto se ha actualizado correctamente' : 'El texto se ha agregado correctamente al sistema',
                                confirmButtonColor: '#667eea'
                            });
                            
                            this.closeModal();
                        } else {
                            throw new Error(result.message || 'Error al guardar el texto');
                        }
                        
                    } catch (error) {
                        console.error('Error completo:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar el texto: ' + error.message,
                            confirmButtonColor: '#667eea'
                        });
                    } finally {
                        this.saving = false;
                    }
                },
                
                editText(text) {
                    this.openModal(text);
                },
                
                async deleteText(text) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `Vas a eliminar el texto "${text.titulo}". Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#667eea',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Eliminando texto...',
                                text: 'Por favor espere',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });
                            
                            try {
                                const response = await fetch(`/admin/texts/${text.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                });
                                
                                if (!response.ok) {
                                    const errorText = await response.text();
                                    throw new Error(`Error ${response.status}: ${errorText.substring(0, 100)}`);
                                }
                                
                                const index = this.textsData.findIndex(t => t.id === text.id);
                                if (index !== -1) {
                                    this.textsData.splice(index, 1);
                                }
                                
                                if (this.paginatedTexts.length === 0 && this.currentPage > 1) {
                                    this.currentPage--;
                                }
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Texto eliminado!',
                                    text: 'El texto ha sido eliminado del sistema',
                                    confirmButtonColor: '#667eea'
                                });
                            } catch (error) {
                                console.error('Error eliminando:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo eliminar el texto: ' + error.message,
                                    confirmButtonColor: '#667eea'
                                });
                            }
                        }
                    });
                },
                
                exportToPDF() {
                    if (this.filteredTexts.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No hay datos',
                            text: 'No hay textos para exportar',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Generando PDF...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    setTimeout(() => {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF();
                        
                        doc.setFontSize(20);
                        doc.setTextColor(40, 40, 40);
                        doc.text('Lista de Textos - LECTOEVAL', 105, 15, { align: 'center' });
                        
                        doc.setFontSize(10);
                        doc.setTextColor(100, 100, 100);
                        doc.text(`Generado el: ${new Date().toLocaleDateString()}`, 105, 22, { align: 'center' });
                        
                        const tableData = this.filteredTexts.map(text => [
                            text.titulo,
                            text.tema || 'Sin tema',
                            text.palabras_totales.toString(),
                            this.getStatusText(text.ocr_status)
                        ]);
                        
                        doc.autoTable({
                            startY: 30,
                            head: [['Título', 'Tema', 'Palabras', 'Estado OCR']],
                            body: tableData,
                            theme: 'grid',
                            headStyles: {
                                fillColor: [102, 126, 234],
                                textColor: 255,
                                fontStyle: 'bold'
                            },
                            styles: {
                                fontSize: 9,
                                cellPadding: 3
                            }
                        });
                        
                        doc.save(`textos_lectoeval_${new Date().toISOString().split('T')[0]}.pdf`);
                        Swal.close();
                    }, 1000);
                },
                
                exportToExcel() {
                    if (this.filteredTexts.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No hay datos',
                            text: 'No hay textos para exportar',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Generando Excel...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    setTimeout(() => {
                        const data = this.filteredTexts.map(text => ({
                            'Título': text.titulo,
                            'Tema': text.tema || '',
                            'Descripción': text.descripcion || '',
                            'Palabras Totales': text.palabras_totales,
                            'Estado OCR': this.getStatusText(text.ocr_status)
                        }));
                        
                        const ws = XLSX.utils.json_to_sheet(data);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Textos');
                        XLSX.writeFile(wb, `textos_lectoeval_${new Date().toISOString().split('T')[0]}.xlsx`);
                        Swal.close();
                    }, 1000);
                },
                
                async loadTexts() {
                    try {
                        const response = await fetch('{{ route("texts.api.index") }}');
                        if (response.ok) {
                            this.textsData = await response.json();
                        } else {
                            console.error('Error cargando textos:', response.status);
                        }
                    } catch (error) {
                        console.error('Error cargando textos:', error);
                        this.textsData = [];
                    }
                },
                
                init() {
                    this.loadTexts();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js no está cargado');
                return;
            }
            
            Alpine.data('textApp', textApp);
        });
    </script>
</x-app-layout>