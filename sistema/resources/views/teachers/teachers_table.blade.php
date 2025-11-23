<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Docente</th>
                <th>Información de Contacto</th>
                <th>DNI</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="teachers-table-body">
            @forelse($teachers as $teacher)
            <tr data-teacher-id="{{ $teacher->id }}">
                <td>
                    <div class="d-flex align-items-center">
                        <div class="teacher-avatar me-3">
                            {{ $teacher->iniciales }}
                        </div>
                        <div>
                            <strong>{{ $teacher->nombres }} {{ $teacher->apellidos }}</strong>
                            <br>
                            <small class="text-muted">Registrado: {{ $teacher->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <i class="fas fa-envelope me-1 text-muted"></i>
                        <small>{{ $teacher->correo ?? 'No registrado' }}</small>
                    </div>
                    <div>
                        <i class="fas fa-phone me-1 text-muted"></i>
                        <small>{{ $teacher->telefono ?? 'No registrado' }}</small>
                    </div>
                </td>
                <td>
                    <span class="badge dni-badge">{{ $teacher->dni }}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-edit action-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editTeacherModal"
                            data-teacher-id="{{ $teacher->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-delete action-btn"
                            onclick="confirmDelete({{ $teacher->id }}, '{{ $teacher->nombres }} {{ $teacher->apellidos }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fas fa-users fa-2x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron docentes</h5>
                    <p class="text-muted">
                        @if(request()->has('search') && !empty(request('search')))
                            No hay resultados para "{{ request('search') }}"
                        @else
                            No hay docentes registrados en el sistema
                        @endif
                    </p>
                    @if(!request()->has('search') || empty(request('search')))
                    <button class="btn btn-academic" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Primer Docente
                    </button>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>