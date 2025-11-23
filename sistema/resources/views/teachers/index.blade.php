<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <div class="max-w-7xl mx-auto p-4 sm:p-6" x-data="teacherApp()">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800" style="font-family: 'Comic Sans MS', cursive;">Nuestros Docentes</h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">Gestiona la información de todos los profesores en el sistema</p>
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
                    <i class="fas fa-plus-circle mr-2"></i> Agregar Docente
                </button>
            </div>
        </div>

        <!-- Panel de Búsqueda y Filtros -->
        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6 transition duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" x-model="searchTerm" @input="debounceSearch()" placeholder="Buscar docentes por DNI, nombres, apellidos o correo..." class="w-full bg-gray-50 border-0 rounded-2xl py-3 px-5 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300">
                        <div class="absolute right-4 top-3 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Docentes -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition duration-300 hover:shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                        <tr>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">DNI</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Apellidos</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Nombres</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Correo</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Teléfono</th>
                            <th class="text-left p-3" style="font-family: 'Comic Sans MS', cursive;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="teacher in paginatedTeachers" :key="teacher.id">
                            <tr class="border-b border-gray-200 hover:bg-purple-50 transition duration-200">
                                <td class="p-3 font-mono" x-text="teacher.dni"></td>
                                <td class="p-3 font-semibold" x-text="teacher.apellidos"></td>
                                <td class="p-3" x-text="teacher.nombres"></td>
                                <td class="p-3">
                                    <template x-if="teacher.correo">
                                        <a :href="'mailto:' + teacher.correo" class="text-blue-600 hover:text-blue-800" x-text="teacher.correo"></a>
                                    </template>
                                    <template x-if="!teacher.correo">
                                        <span class="text-gray-400 italic">Sin correo</span>
                                    </template>
                                </td>
                                <td class="p-3">
                                    <template x-if="teacher.telefono">
                                        <a :href="'tel:' + teacher.telefono" class="text-green-600 hover:text-green-800" x-text="teacher.telefono"></a>
                                    </template>
                                    <template x-if="!teacher.telefono">
                                        <span class="text-gray-400 italic">Sin teléfono</span>
                                    </template>
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <button @click="editTeacher(teacher)" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </button>
                                        <button @click="deleteTeacher(teacher)" class="bg-red-500 hover:bg-red-600 text-white text-xs py-1.5 px-3 rounded-lg flex items-center justify-center transition duration-300">
                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredTeachers.length === 0">
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-chalkboard-teacher text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg" style="font-family: 'Comic Sans MS', cursive;">No se encontraron docentes</p>
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
                    Mostrando <span x-text="showingFrom"></span> a <span x-text="showingTo"></span> de <span x-text="filteredTeachers.length"></span> docentes
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

        <!-- Modal para Agregar/Editar Docente -->
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
                        <h5 class="text-xl sm:text-2xl" style="font-family: 'Comic Sans MS', cursive;" x-text="editingTeacher ? 'Editar Docente' : 'Agregar Nuevo Docente'"></h5>
                        <button type="button" @click="closeModal()" class="text-white text-xl sm:text-2xl hover:text-yellow-200 transition duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" x-model="formData.id">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                            <input type="text" x-model="formData.dni" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Ingrese DNI" maxlength="12">
                            <div class="text-xs text-gray-500 mt-1">Ingrese el DNI del docente (8 dígitos)</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                            <input type="text" x-model="formData.nombres" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Nombres del docente">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                            <input type="text" x-model="formData.apellidos" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" required placeholder="Apellidos del docente">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" x-model="formData.correo" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="correo@ejemplo.com">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" x-model="formData.telefono" class="w-full border border-gray-300 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-300" placeholder="+51 987654321" maxlength="20">
                            <div class="text-xs text-gray-500 mt-1">Ej: +51 987654321</div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition duration-300 text-sm">
                        Cancelar
                    </button>
                    <button type="button" @click="saveTeacher()" :disabled="saving" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-2 px-4 rounded-xl shadow-md text-sm transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="editingTeacher ? 'Actualizar' : 'Guardar'"></span> Docente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function teacherApp() {
            return {
                teachersData: [],
                searchTerm: '',
                currentPage: 1,
                teachersPerPage: 10,
                isModalOpen: false,
                editingTeacher: null,
                saving: false,
                formData: {
                    id: '',
                    dni: '',
                    nombres: '',
                    apellidos: '',
                    correo: '',
                    telefono: ''
                },
                
                get filteredTeachers() {
                    let filtered = this.teachersData;
                    
                    if (this.searchTerm) {
                        const term = this.searchTerm.toLowerCase();
                        filtered = filtered.filter(teacher => 
                            teacher.dni.toLowerCase().includes(term) ||
                            teacher.nombres.toLowerCase().includes(term) ||
                            teacher.apellidos.toLowerCase().includes(term) ||
                            (teacher.correo && teacher.correo.toLowerCase().includes(term)) ||
                            (teacher.telefono && teacher.telefono.toLowerCase().includes(term))
                        );
                    }
                    
                    return filtered;
                },
                
                get paginatedTeachers() {
                    const startIndex = (this.currentPage - 1) * this.teachersPerPage;
                    return this.filteredTeachers.slice(startIndex, startIndex + this.teachersPerPage);
                },
                
                get totalPages() {
                    return Math.ceil(this.filteredTeachers.length / this.teachersPerPage);
                },
                
                get showingFrom() {
                    return (this.currentPage - 1) * this.teachersPerPage + 1;
                },
                
                get showingTo() {
                    const end = this.showingFrom + this.teachersPerPage - 1;
                    return Math.min(end, this.filteredTeachers.length);
                },
                
                debounceSearch() {
                    // Búsqueda en tiempo real ya que los datos están cargados
                    this.currentPage = 1;
                },
                
                openModal(teacher = null) {
                    this.editingTeacher = teacher;
                    if (teacher) {
                        this.formData = { ...teacher };
                    } else {
                        this.formData = {
                            id: '',
                            dni: '',
                            nombres: '',
                            apellidos: '',
                            correo: '',
                            telefono: ''
                        };
                    }
                    this.isModalOpen = true;
                },
                
                closeModal() {
                    this.isModalOpen = false;
                    this.editingTeacher = null;
                    this.saving = false;
                    this.formData = {
                        id: '',
                        dni: '',
                        nombres: '',
                        apellidos: '',
                        correo: '',
                        telefono: ''
                    };
                },
                
                async saveTeacher() {
                    if (!this.formData.dni || !this.formData.nombres || !this.formData.apellidos) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campos obligatorios',
                            text: 'Por favor, complete los campos DNI, Nombres y Apellidos',
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
                        title: this.editingTeacher ? 'Actualizando docente...' : 'Guardando docente...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    try {
                        let url, method;
                        
                        if (this.editingTeacher) {
                            url = `/admin/teachers/${this.formData.id}`;
                            method = 'PUT';
                        } else {
                            url = '{{ route("teachers.store") }}';
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
                            let errorMessage = 'Error al guardar el docente';
                            
                            if (result.errors) {
                                const firstError = Object.values(result.errors)[0][0];
                                errorMessage = firstError;
                            } else if (result.message) {
                                errorMessage = result.message;
                            }
                            
                            throw new Error(errorMessage);
                        }
                        
                        if (result.success) {
                            if (this.editingTeacher) {
                                // Actualizar en la lista
                                const index = this.teachersData.findIndex(t => t.id === this.editingTeacher.id);
                                if (index !== -1) {
                                    this.teachersData[index] = result.teacher;
                                }
                            } else {
                                // Agregar a la lista
                                this.teachersData.unshift(result.teacher);
                            }
                            
                            await Swal.fire({
                                icon: 'success',
                                title: this.editingTeacher ? '¡Docente actualizado!' : '¡Docente agregado!',
                                text: result.message,
                                confirmButtonColor: '#667eea'
                            });
                            
                            this.closeModal();
                        } else {
                            throw new Error(result.message || 'Error al guardar el docente');
                        }
                        
                    } catch (error) {
                        console.error('Error completo:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar el docente: ' + error.message,
                            confirmButtonColor: '#667eea'
                        });
                    } finally {
                        this.saving = false;
                    }
                },
                
                editTeacher(teacher) {
                    this.openModal(teacher);
                },
                
                async deleteTeacher(teacher) {
                    const result = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: `Vas a eliminar al docente ${teacher.nombres} ${teacher.apellidos}. Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#667eea',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });
                    
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Eliminando docente...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        
                        try {
                            const response = await fetch(`/admin/teachers/${teacher.id}`, {
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
                                const index = this.teachersData.findIndex(t => t.id === teacher.id);
                                if (index !== -1) {
                                    this.teachersData.splice(index, 1);
                                }
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Docente eliminado!',
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
                                text: 'No se pudo eliminar el docente: ' + error.message,
                                confirmButtonColor: '#667eea'
                            });
                        }
                    }
                },
                
                exportToPDF() {
                    if (this.filteredTeachers.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No hay datos',
                            text: 'No hay docentes para exportar',
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
                        doc.text('Lista de Docentes - LECTOEVAL', 105, 15, { align: 'center' });
                        
                        doc.setFontSize(10);
                        doc.setTextColor(100, 100, 100);
                        doc.text(`Generado el: ${new Date().toLocaleDateString()}`, 105, 22, { align: 'center' });
                        
                        const tableData = this.filteredTeachers.map(teacher => [
                            teacher.dni,
                            teacher.apellidos,
                            teacher.nombres,
                            teacher.correo || 'Sin correo',
                            teacher.telefono || 'Sin teléfono'
                        ]);
                        
                        doc.autoTable({
                            startY: 30,
                            head: [['DNI', 'Apellidos', 'Nombres', 'Correo', 'Teléfono']],
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
                        
                        doc.save(`docentes_lectoeval_${new Date().toISOString().split('T')[0]}.pdf`);
                        Swal.close();
                    }, 1000);
                },
                
                exportToExcel() {
                    if (this.filteredTeachers.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No hay datos',
                            text: 'No hay docentes para exportar',
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
                        const data = this.filteredTeachers.map(teacher => ({
                            'DNI': teacher.dni,
                            'Apellidos': teacher.apellidos,
                            'Nombres': teacher.nombres,
                            'Correo Electrónico': teacher.correo || '',
                            'Teléfono': teacher.telefono || ''
                        }));
                        
                        const ws = XLSX.utils.json_to_sheet(data);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Docentes');
                        XLSX.writeFile(wb, `docentes_lectoeval_${new Date().toISOString().split('T')[0]}.xlsx`);
                        Swal.close();
                    }, 1000);
                },
                
                async loadTeachers() {
                    try {
                        const response = await fetch('{{ route("teachers.api.index") }}');
                        if (response.ok) {
                            this.teachersData = await response.json();
                        } else {
                            console.error('Error cargando docentes:', response.status);
                            // Cargar datos de ejemplo si hay error
                            this.teachersData = [];
                        }
                    } catch (error) {
                        console.error('Error cargando docentes:', error);
                        this.teachersData = [];
                    }
                },
                
                init() {
                    this.loadTeachers();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js no está cargado');
                return;
            }
            
            Alpine.data('teacherApp', teacherApp);
        });
    </script>
</x-app-layout>