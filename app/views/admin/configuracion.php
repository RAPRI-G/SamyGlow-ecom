<?php
// app/views/admin/configuracion.php
// SOLO el contenido específico, sin estructura HTML
?>

<main class="flex-1 overflow-y-auto p-6">
    <!-- Sección de Configuración -->
    <div class="content-section active" id="configuracion">
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex border-b">
                <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-600 hover:bg-gray-50 active" data-tab="usuarios">
                    <i class="fas fa-user-shield mr-2"></i>Usuarios Admin
                </button>
            </div>
        </div>

        <!-- Contenido -->
        <div id="tab-content">
            <!-- Usuarios Administradores -->
            <div class="tab-panel active" id="usuarios-panel">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Lista de Usuarios -->
                    <div class="lg:col-span-2">
                        <div class="card">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-bold text-gray-800">Usuarios Administradores</h2>
                                <button class="btn btn-primary" onclick="abrirModalNuevoUsuario()">
                                    <i class="fas fa-user-plus mr-2"></i>Nuevo Usuario
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="tabla-usuarios">
                                        <?php if (!empty($usuarios)): ?>
                                            <?php foreach ($usuarios as $usuario): ?>
                                                <tr>
                                                    <td class="px-6 py-4">
                                                        <div class="font-medium"><?= htmlspecialchars($usuario['username']) ?></div>
                                                        <div class="text-sm text-gray-500"><?= date('Y-m-d', strtotime($usuario['created_at'])) ?></div>
                                                    </td>
                                                    <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($usuario['correo']) ?></td>
                                                    <td class="px-6 py-4">
                                                        <?php if ($usuario['activo']): ?>
                                                            <span class="status-badge status-activa">Activo</span>
                                                        <?php else: ?>
                                                            <span class="status-badge status-inactiva">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="editarUsuario(<?= $usuario['id'] ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-900" onclick="eliminarUsuario(<?= $usuario['id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                                    No hay usuarios administradores registrados
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="space-y-6">
                        <div class="card">
                            <h3 class="section-title">Estadísticas</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Usuarios</span>
                                    <span class="font-bold" id="total-usuarios"><?= count($usuarios ?? []) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Usuarios Activos</span>
                                    <span class="font-bold text-green-600" id="usuarios-activos">
                                        <?= count(array_filter($usuarios ?? [], fn($u) => $u['activo'])) ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Última Auditoría</span>
                                    <span class="font-bold">Hace 2 días</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <h3 class="section-title">Recomendaciones</h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <i class="fas fa-shield-alt text-green-500 mt-1 mr-2"></i>
                                    <div>
                                        <p class="text-sm font-medium">Contraseñas seguras</p>
                                        <p class="text-xs text-gray-500">Todos los usuarios tienen contraseñas fuertes</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-yellow-500 mt-1 mr-2"></i>
                                    <div>
                                        <p class="text-sm font-medium">Rotación de contraseñas</p>
                                        <p class="text-xs text-gray-500">Recomendado cambiar cada 90 días</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Nuevo Usuario -->
<div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="modal-nuevo-usuario">
    <div class="bg-white rounded-lg w-full max-w-md mx-4">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Nuevo Usuario</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="cerrarModal('modal-nuevo-usuario')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4">
            <form id="form-nuevo-usuario">
                <div class="form-group">
                    <label for="nuevo-username">Usuario</label>
                    <input type="text" id="nuevo-username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="nuevo-email">Correo</label>
                    <input type="email" id="nuevo-email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="nuevo-password">Contraseña</label>
                    <input type="password" id="nuevo-password" class="form-control" required>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal('modal-nuevo-usuario')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="modal-editar-usuario">
    <div class="bg-white rounded-lg w-full max-w-md mx-4">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Editar Usuario</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="cerrarModal('modal-editar-usuario')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="contenido-modal-usuario">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>

<style>
    /* ============================================= */
    /* 🎨 ESTILOS ESPECÍFICOS DE CONFIGURACIÓN */
    /* ============================================= */

    .sidebar {
        background: linear-gradient(180deg, #f472b6 0%, #a78bfa 100%);
        color: white;
    }

    .active-menu {
        background: rgba(255, 255, 255, 0.2);
        border-right: 4px solid white;
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .submenu.open {
        max-height: 500px;
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .tab-button.active {
        background-color: #f472b6;
        color: white;
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    .metric-card {
        background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
        color: white;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-activa {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactiva {
        background-color: #fef3c7;
        color: #d97706;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #f472b6;
        box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.1);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
    }

    .btn-primary {
        background: #f472b6;
        color: white;
    }

    .btn-primary:hover {
        background: #ec4899;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.toggle-slider {
        background-color: #f472b6;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(26px);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #374151;
        border-bottom: 2px solid #f472b6;
        padding-bottom: 0.5rem;
    }

    /* Loading states */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }

    .loading-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #ec4899;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Notificaciones */
    .custom-notification {
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-radius: 12px;
        z-index: 10001 !important;
        position: fixed;
        top: 100px;
        right: 20px;
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<script>
    // =============================================
    // 🎯 CONFIGURACIÓN INICIAL
    // =============================================

    document.addEventListener('DOMContentLoaded', function() {
        inicializarApp();
    });

    function inicializarApp() {
        // Tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.remove('active');
                });

                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId + '-panel').classList.add('active');
            });
        });

        // Formulario nuevo usuario
        document.getElementById('form-nuevo-usuario').addEventListener('submit', function(e) {
            e.preventDefault();
            crearUsuario();
        });
    }

    // =============================================
    // 🛠️ FUNCIONES UTILITARIAS
    // =============================================

    function mostrarLoading(mensaje = 'Procesando...') {
        // Remover loading existente
        ocultarLoading();

        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 shadow-xl flex items-center space-x-4">
                <div class="loading-spinner"></div>
                <span class="text-gray-700 font-medium">${mensaje}</span>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    }

    function ocultarLoading() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    function mostrarNotificacion(mensaje, tipo = 'info') {
        // Remover notificaciones existentes
        document.querySelectorAll('.custom-notification').forEach(notif => {
            if (document.body.contains(notif)) {
                document.body.removeChild(notif);
            }
        });

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            warning: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };

        const notificacion = document.createElement('div');
        notificacion.className = `custom-notification fixed top-20 right-4 p-4 rounded-lg shadow-lg z-[10001] ${
            tipo === 'success' ? 'bg-green-500 text-white' :
            tipo === 'error' ? 'bg-red-500 text-white' :
            tipo === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;

        notificacion.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icons[tipo]} mr-3"></i>
                <span class="font-medium">${mensaje}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notificacion);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (document.body.contains(notificacion)) {
                notificacion.style.opacity = '0';
                notificacion.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(notificacion)) {
                        document.body.removeChild(notificacion);
                    }
                }, 300);
            }
        }, 5000);
    }

    // =============================================
    // 👥 FUNCIONES DE GESTIÓN DE USUARIOS
    // =============================================

    function abrirModalNuevoUsuario() {
        document.getElementById('modal-nuevo-usuario').classList.remove('hidden');
    }

    function cerrarModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    async function crearUsuario() {
        const username = document.getElementById('nuevo-username').value;
        const email = document.getElementById('nuevo-email').value;
        const password = document.getElementById('nuevo-password').value;

        if (password.length < 6) {
            mostrarNotificacion('La contraseña debe tener al menos 6 caracteres', 'error');
            return;
        }

        try {
            mostrarLoading('Creando usuario...');

            const response = await fetch('index.php?view=api-crear-usuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: username,
                    correo: email,
                    password: password
                })
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Usuario creado exitosamente', 'success');
                cerrarModal('modal-nuevo-usuario');
                document.getElementById('form-nuevo-usuario').reset();

                // Recargar la página para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al crear usuario');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al crear usuario: ' + error.message, 'error');
        }
    }

    async function editarUsuario(usuarioId) {
        try {
            mostrarLoading('Cargando usuario...');

            const response = await fetch(`index.php?view=api-listar-usuarios`);
            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                const usuario = result.data.find(u => u.id == usuarioId);
                if (usuario) {
                    mostrarModalEditarUsuario(usuario);
                } else {
                    throw new Error('Usuario no encontrado');
                }
            } else {
                throw new Error(result.error || 'Error al cargar usuarios');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al cargar usuario: ' + error.message, 'error');
        }
    }

    function mostrarModalEditarUsuario(usuario) {
        const html = `
            <form id="form-editar-usuario" class="space-y-4" onsubmit="guardarCambiosUsuario(event, ${usuario.id})">
                <div class="form-group">
                    <label for="edit-username">Usuario</label>
                    <input type="text" id="edit-username" class="form-control" value="${usuario.username}" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Correo</label>
                    <input type="email" id="edit-email" class="form-control" value="${usuario.correo}" required>
                </div>
                <div class="form-group">
                    <label for="edit-activo">Estado</label>
                    <select id="edit-activo" class="form-control">
                        <option value="1" ${usuario.activo ? 'selected' : ''}>Activo</option>
                        <option value="0" ${!usuario.activo ? 'selected' : ''}>Inactivo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-password">Nueva Contraseña (opcional)</label>
                    <input type="password" id="edit-password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal('modal-editar-usuario')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        `;

        document.getElementById('contenido-modal-usuario').innerHTML = html;
        document.getElementById('modal-editar-usuario').classList.remove('hidden');
    }

    async function eliminarUsuario(usuarioId) {
        if (!confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            mostrarLoading('Eliminando usuario...');

            // Para debug: mostrar la URL que se está llamando
            console.log('URL a llamar:', 'index.php?view=api-eliminar-usuario');

            const response = await fetch('index.php?view=api-eliminar-usuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: usuarioId
                })
            });

            // Para debug: ver la respuesta cruda
            const rawResponse = await response.text();
            console.log('Respuesta cruda:', rawResponse);

            // Intentar parsear como JSON
            let result;
            try {
                result = JSON.parse(rawResponse);
            } catch (parseError) {
                console.error('Error parseando JSON:', parseError);
                console.error('Respuesta recibida:', rawResponse.substring(0, 200));
                throw new Error('La respuesta del servidor no es JSON válido: ' + rawResponse.substring(0, 100));
            }

            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Usuario eliminado exitosamente', 'success');

                // Recargar la página para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al eliminar usuario');
            }
        } catch (error) {
            ocultarLoading();
            mostrarNotificacion('❌ Error al eliminar usuario: ' + error.message, 'error');
        }
    }

    async function guardarCambiosUsuario(e, usuarioId) {
        e.preventDefault();

        const username = document.getElementById('edit-username').value;
        const email = document.getElementById('edit-email').value;
        const activo = document.getElementById('edit-activo').value;
        const password = document.getElementById('edit-password').value;

        try {
            mostrarLoading('Actualizando usuario...');

            const data = {
                id: usuarioId,
                username: username,
                correo: email,
                activo: parseInt(activo)
            };

            if (password) {
                if (password.length < 6) {
                    mostrarNotificacion('La contraseña debe tener al menos 6 caracteres', 'error');
                    return;
                }
                data.password = password;
            }

            const response = await fetch('index.php?view=api-actualizar-usuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            ocultarLoading();

            if (result.success) {
                mostrarNotificacion('✅ Usuario actualizado exitosamente', 'success');
                cerrarModal('modal-editar-usuario');

                // Recargar la página para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al actualizar usuario');
            }
        } catch (error) {
            ocultarLoading();
        }
        mostrarNotificacion('❌ Error al actualizar usuario: ' + error.message, 'error');
    }
</script>