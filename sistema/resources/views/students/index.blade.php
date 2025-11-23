<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6" x-data="studentApp()">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800" style="font-family: 'Comic Sans MS', cursive;">Nuestros Estudiantes</h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">Gestiona la información de todos los niños en el sistema</p>
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
                    <i class="fas fa-plus-circle mr-2"></i> Agregar Estudiante
                </button>
            </div>
        </div>

        <!-- Panel de Búsqueda y Filtros -->
        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6 transition duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" x-model="searchTerm" @input="debounceSearch()" placeholder="Buscar estudiantes..." class="w-full bg-gray-50 border-0 rounded-2xl py-3 px-5 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <div class="absolute right-4 top-3 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <select x-model="filterGrado" class="bg-gray-50 border-0 rounded-2xl py-3 px-4 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <option value="">Todos los grados</option>
                        <option value="1">1° Grado</option>
                        <option value="2">2° Grado</option>
                        <option value="3">3° Grado</option>
                        <option value="4">4° Grado</option>
                        <option value="5">5° Grado</option>
                        <option value="6">6° Grado</option>
                    </select>
                    <select x-model="filterSeccion" class="bg-gray-50 border-0 rounded-2xl py-3 px-4 w-full md:w-auto focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <option value="">Todas las secciones</option>
                        <option value="A">Sección A</option>
                        <option value="B">Sección B</option>
                        <option value="C">Sección C</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Estudiantes -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition duration-300 hover:shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                        <tr>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">DNI</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Apellidos</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Nombres</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Grado</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Sección</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="student in paginatedStudents" :key="student.id">
                            <tr class="border-b border-gray-200 hover:bg-purple-50 transition duration-200">
                                <td class="p-3 font-mono" x-text="student.dni"></td>
                                <td class="p-3 font-semibold" x-text="student.apellidos"></td>
                                <td class="p-3" x-text="student.nombres"></td>
                                <td class="p-3">
                                    <span class="bg-blue-100 text-blue-800 rounded-xl px-3 py-1 text-xs font-bold" x-text="student.grado + '° Grado'"></span>
                                </td>
                                <td class="p-3">
                                    <template x-if="student.seccion">
                                        <span class="bg-purple-100 text-purple-800 rounded-xl px-3 py-1 text-xs font-bold" x-text="'Sección ' + student.seccion"></span>
                                    </template>
                                    <template x-if="!student.seccion">
                                        <span class="text-gray-400 text-xs">—</span>
                                    </template>
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <button @click="editStudent(student)" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </button>
                                        <button @click="deleteStudent(student)" class="bg-red-500 hover:bg-red-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredStudents.length === 0">
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-user-slash text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg" style="font-family: 'Comic Sans MS', cursive;">No se encontraron estudiantes</p>
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
                    Mostrando <span x-text="showingFrom"></span> a <span x-text="showingTo"></span> de <span x-text="filteredStudents.length"></span> estudiantes
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

        <!-- Modal para Agregar/Editar Estudiante -->
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
                        <h5 class="text-xl sm:text-2xl" style="font-family: 'Comic Sans MS', cursive;" x-text="editingStudent ? 'Editar Estudiante' : 'Agregar Nuevo Estudiante'"></h5>
                        <button type="button" @click="closeModal()" class="text-white text-xl sm:text-2xl hover:text-yellow-200 transition duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="hidden" x-model="formData.id">
                        
                        <!-- Primera fila: DNI, Edad, Nombres -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                            <input type="text" x-model="formData.dni" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Ingrese DNI" maxlength="12">
                            <div class="text-xs text-gray-500 mt-1">8 dígitos numéricos</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Edad</label>
                            <input type="number" x-model="formData.edad" min="3" max="16" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Edad">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                            <input type="text" x-model="formData.nombres" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Nombres del estudiante">
                        </div>
                        
                        <!-- Segunda fila: Apellidos, Grado, Sección -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                            <input type="text" x-model="formData.apellidos" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Apellidos del estudiante">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grado *</label>
                            <select x-model="formData.grado" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required>
                                <option value="">Seleccionar grado</option>
                                <option value="1">1° Grado</option>
                                <option value="2">2° Grado</option>
                                <option value="3">3° Grado</option>
                                <option value="4">4° Grado</option>
                                <option value="5">5° Grado</option>
                                <option value="6">6° Grado</option>
                            </select>
                        </div>
                        
                        <!-- Tercera fila: Colegio, Sección -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Colegio</label>
                            <input type="text" x-model="formData.colegio" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Nombre del colegio">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sección</label>
                            <input type="text" x-model="formData.seccion" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Sección" maxlength="5">
                        </div>
                        
                        <!-- Cuarta fila: Información del Apoderado -->
                        <div class="md:col-span-3">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2" style="font-family: 'Comic Sans MS', cursive;">Información del Apoderado</h3>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Apoderado</label>
                            <input type="text" x-model="formData.apoderado_nombre" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Nombre completo del apoderado">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" x-model="formData.apoderado_telefono" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Teléfono del apoderado" maxlength="20">
                        </div>
                        
                        <!-- Quinta fila: Observaciones -->
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea x-model="formData.observaciones" rows="3" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="Observaciones adicionales"></textarea>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition duration-300 text-sm">
                        Cancelar
                    </button>
                    <button type="button" @click="saveStudent()" :disabled="saving" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-2 px-4 rounded-xl shadow-md text-sm transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="editingStudent ? 'Actualizar' : 'Guardar'"></span> Estudiante
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function studentApp() {
            return {
                studentsData: [],
                searchTerm: '',
                filterGrado: '',
                filterSeccion: '',
                currentPage: 1,
                studentsPerPage: 10,
                isModalOpen: false,
                editingStudent: null,
                saving: false,
                formData: {
                    id: '',
                    dni: '',
                    nombres: '',
                    apellidos: '',
                    grado: '',
                    seccion: '',
                    edad: '',
                    colegio: '',
                    apoderado_nombre: '',
                    apoderado_telefono: '',
                    observaciones: ''
                },
                
                get filteredStudents() {
                    let filtered = this.studentsData;
                    
                    if (this.searchTerm) {
                        const term = this.searchTerm.toLowerCase();
                        filtered = filtered.filter(student => 
                            student.dni.toLowerCase().includes(term) ||
                            student.nombres.toLowerCase().includes(term) ||
                            student.apellidos.toLowerCase().includes(term) ||
                            (student.colegio && student.colegio.toLowerCase().includes(term)) ||
                            (student.apoderado_nombre && student.apoderado_nombre.toLowerCase().includes(term))
                        );
                    }
                    
                    if (this.filterGrado) {
                        filtered = filtered.filter(student => student.grado === this.filterGrado);
                    }
                    
                    if (this.filterSeccion) {
                        filtered = filtered.filter(student => student.seccion === this.filterSeccion);
                    }
                    
                    return filtered;
                },
                
                get paginatedStudents() {
                    const startIndex = (this.currentPage - 1) * this.studentsPerPage;
                    return this.filteredStudents.slice(startIndex, startIndex + this.studentsPerPage);
                },
                
                get totalPages() {
                    return Math.ceil(this.filteredStudents.length / this.studentsPerPage);
                },
                
                get showingFrom() {
                    return (this.currentPage - 1) * this.studentsPerPage + 1;
                },
                
                get showingTo() {
                    const end = this.showingFrom + this.studentsPerPage - 1;
                    return Math.min(end, this.filteredStudents.length);
                },
                
                debounceSearch() {
                    this.currentPage = 1;
                },
                
                openModal(student = null) {
                    this.editingStudent = student;
                    if (student) {
                        this.formData = { ...student };
                    } else {
                        this.formData = {
                            id: '',
                            dni: '',
                            nombres: '',
                            apellidos: '',
                            grado: '',
                            seccion: '',
                            edad: '',
                            colegio: '',
                            apoderado_nombre: '',
                            apoderado_telefono: '',
                            observaciones: ''
                        };
                    }
                    this.isModalOpen = true;
                },
                
                closeModal() {
                    this.isModalOpen = false;
                    this.editingStudent = null;
                    this.saving = false;
                    this.formData = {
                        id: '',
                        dni: '',
                        nombres: '',
                        apellidos: '',
                        grado: '',
                        seccion: '',
                        edad: '',
                        colegio: '',
                        apoderado_nombre: '',
                        apoderado_telefono: '',
                        observaciones: ''
                    };
                },
                
                async saveStudent() {
                    if (!this.formData.dni || !this.formData.nombres || !this.formData.apellidos || !this.formData.grado) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campos obligatorios',
                            text: 'Por favor, complete los campos DNI, Nombres, Apellidos y Grado',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }

                    // Validar DNI (solo números, 8 dígitos)
                    if (!/^\d{8}$/.test(this.formData.dni)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'DNI inválido',
                            text: 'El DNI debe contener exactamente 8 dígitos numéricos',
                            confirmButtonColor: '#667eea'
                        });
                        return;
                    }
                    
                    if (this.saving) return;
                    this.saving = true;
                    
                    Swal.fire({
                        title: this.editingStudent ? 'Actualizando estudiante...' : 'Guardando estudiante...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    try {
                        let url, method;
                        
                        if (this.editingStudent) {
                            url = `/admin/students/${this.formData.id}`;
                            method = 'PUT';
                        } else {
                            url = '{{ route("students.store") }}';
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
                            let errorMessage = 'Error al guardar el estudiante';
                            
                            if (result.errors) {
                                const firstError = Object.values(result.errors)[0][0];
                                errorMessage = firstError;
                            } else if (result.message) {
                                errorMessage = result.message;
                            }
                            
                            throw new Error(errorMessage);
                        }
                        
                        if (result.success) {
                            if (this.editingStudent) {
                                // Actualizar en la lista
                                const index = this.studentsData.findIndex(s => s.id === this.editingStudent.id);
                                if (index !== -1) {
                                    this.studentsData[index] = result.student;
                                }
                            } else {
                                // Agregar a la lista
                                this.studentsData.unshift(result.student);
                            }
                            
                            await Swal.fire({
                                icon: 'success',
                                title: this.editingStudent ? '¡Estudiante actualizado!' : '¡Estudiante agregado!',
                                text: result.message,
                                confirmButtonColor: '#667eea'
                            });
                            
                            this.closeModal();
                        } else {
                            throw new Error(result.message || 'Error al guardar el estudiante');
                        }
                        
                    } catch (error) {
                        console.error('Error completo:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar el estudiante: ' + error.message,
                            confirmButtonColor: '#667eea'
                        });
                    } finally {
                        this.saving = false;
                    }
                },
                
                editStudent(student) {
                    this.openModal(student);
                },
                
                async deleteStudent(student) {
                    const result = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: `Vas a eliminar a ${student.nombres} ${student.apellidos}. Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#667eea',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });
                    
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Eliminando estudiante...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        
                        try {
                            const response = await fetch(`/admin/students/${student.id}`, {
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
                            
                            const result = await response.json();
                            
                            if (result.success) {
                                const index = this.studentsData.findIndex(s => s.id === student.id);
                                if (index !== -1) {
                                    this.studentsData.splice(index, 1);
                                }
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Estudiante eliminado!',
                                    text: result.message,
                                    confirmButtonColor: '#667eea'
                                });
                            } else {
                                throw new Error(result.message || 'Error al eliminar');
                            }
                            
                        } catch (error) {
                            console.error('Error eliminando:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar el estudiante: ' + error.message,
                                confirmButtonColor: '#667eea'
                            });
                        }
                    }
                },
                
                exportToPDF() {
                    if (this.filteredStudents.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No hay datos',
                            text: 'No hay estudiantes para exportar',
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
                        doc.text('Lista de Estudiantes - LECTOEVAL', 105, 15, { align: 'center' });
                        
                        doc.setFontSize(10);
                        doc.setTextColor(100, 100, 100);
                        doc.text(`Generado el: ${new Date().toLocaleDateString()}`, 105, 22, { align: 'center' });
                        
                        const tableData = this.filteredStudents.map(student => [
                            student.dni,
                            student.apellidos,
                            student.nombres,
                            `${student.grado}° Grado`,
                            student.seccion ? `Sección ${student.seccion}` : '—',
                            student.edad || '—'
                        ]);
                        
                        doc.autoTable({
                            startY: 30,
                            head: [['DNI', 'Apellidos', 'Nombres', 'Grado', 'Sección', 'Edad']],
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
                        
                        doc.save(`estudiantes_lectoeval_${new Date().toISOString().split('T')[0]}.pdf`);
                        Swal.close();
                    }, 1000);
                },
                
                exportToExcel() {
                    if (this.filteredStudents.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No hay datos',
                            text: 'No hay estudiantes para exportar',
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
                        const data = this.filteredStudents.map(student => ({
                            'DNI': student.dni,
                            'Apellidos': student.apellidos,
                            'Nombres': student.nombres,
                            'Grado': `${student.grado}° Grado`,
                            'Sección': student.seccion ? `Sección ${student.seccion}` : '',
                            'Edad': student.edad || '',
                            'Colegio': student.colegio || '',
                            'Apoderado': student.apoderado_nombre || '',
                            'Teléfono Apoderado': student.apoderado_telefono || '',
                            'Observaciones': student.observaciones || ''
                        }));
                        
                        const ws = XLSX.utils.json_to_sheet(data);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Estudiantes');
                        XLSX.writeFile(wb, `estudiantes_lectoeval_${new Date().toISOString().split('T')[0]}.xlsx`);
                        Swal.close();
                    }, 1000);
                },
                
                async loadStudents() {
                    try {
                        const response = await fetch('/api/students');
                        if (response.ok) {
                            this.studentsData = await response.json();
                        } else {
                            console.error('Error cargando estudiantes:', response.status);
                            this.studentsData = [];
                        }
                    } catch (error) {
                        console.error('Error cargando estudiantes:', error);
                        this.studentsData = [];
                    }
                },
                
                init() {
                    this.loadStudents();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js no está cargado');
                return;
            }
            
            Alpine.data('studentApp', studentApp);
        });
    </script>
</x-app-layout>