{{-- Modal de Configuración de Mesas --}}
<div id="tables-config-modal" class="tables-config-modal">
    <div class="tables-config-container">
        <div class="tables-config-header">
            <h2>
                <i class="fas fa-table"></i>
                Configuración de Mesas
            </h2>
            <button class="tables-config-close" onclick="closeTablesConfigModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="tables-config-content">
            <!-- Mensaje de éxito -->
            <div id="config-success-message" class="success-message">
                <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
                <span id="success-message-text">Configuración guardada exitosamente</span>
            </div>

            <!-- Toggle de habilitación de mesas -->
            <div id="toggle-container" class="toggle-container">
                <label class="toggle-label">
                    <div class="toggle-info">
                        <div class="toggle-title">Gestión de Mesas</div>
                        <div class="toggle-description">
                            Habilita la asignación de pedidos a mesas específicas
                        </div>
                    </div>
                    <input type="checkbox" id="tables-enabled-input" class="toggle-input">
                    <div class="toggle-switch"></div>
                </label>
            </div>

            <!-- Sección de gestión de mesas -->
            <div id="tables-management-section" class="tables-section">
                <div class="tables-section-header">
                    <div class="tables-section-title">
                        <i class="fas fa-cog"></i> Gestión de Mesas
                    </div>
                    <div class="state-badge" id="tables-count">0 mesas</div>
                </div>

                <!-- Botones de acción superior -->
                <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                    <button class="bulk-apply-btn" onclick="openCreateTableModal()" style="flex: 1; min-width: 140px;">
                        <i class="fas fa-plus"></i>
                        Crear Mesa
                    </button>
                    <button class="bulk-apply-btn warning" onclick="openBulkStateModal()" style="flex: 1; min-width: 140px;">
                        <i class="fas fa-sync-alt"></i>
                        Cambiar Todas
                    </button>
                </div>

                <!-- Tabla de mesas -->
                <div class="tables-table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tables-tbody">
                            <!-- Las mesas se cargarán dinámicamente desde BD -->
                        </tbody>
                    </table>
                </div>

                <!-- Estado vacío -->
                <div id="empty-state" class="empty-state" style="display: none;">
                    <i class="fas fa-table"></i>
                    <p>No hay mesas configuradas</p>
                    <small>Crea una nueva mesa para comenzar</small>
                </div>
            </div>

            <!-- Botones de acción inferior -->
            <div class="action-buttons">
                <button class="btn btn-cancel" onclick="closeTablesConfigModal()">
                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
                <button class="btn btn-save" id="save-tables-config" onclick="saveTablesConfig()">
                    <i class="fas fa-save"></i>
                    Guardar Configuración
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar mesa -->
<div id="create-table-modal" class="tables-config-modal">
    <div class="tables-config-container" style="max-width: 400px;">
        <div class="tables-config-header">
            <h2>
                <i class="fas fa-plus-circle"></i>
                <span id="create-table-title">Crear Nueva Mesa</span>
            </h2>
            <button class="tables-config-close" onclick="closeCreateTableModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tables-config-content">
            <form id="create-table-form" onsubmit="handleCreateTable(event)">
                <input type="hidden" id="edit-table-id" value="">
                
                <div class="form-group">
                    <label class="form-label">Número de Mesa</label>
                    <input type="text" id="table-number-input" class="form-input" placeholder="Ej: 1, 2, A1, VIP1..." required>
                    <small style="display: block; margin-top: 4px; color: var(--text-secondary); font-size: 0.85rem;">
                        Puede usar números o letras
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select id="table-state-input" class="form-select" required>
                        <option value="Disponible">✓ Disponible</option>
                        <option value="Ocupada">● Ocupada</option>
                        <option value="Reservada">◐ Reservada</option>
                        <option value="No Disponible">✗ No Disponible</option>
                    </select>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-cancel" onclick="closeCreateTableModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cambio masivo de estado -->
<div id="bulk-state-modal" class="tables-config-modal">
    <div class="tables-config-container" style="max-width: 500px;">
        <div class="tables-config-header">
            <h2>
                <i class="fas fa-sync-alt"></i>
                Cambiar Estado de Todas las Mesas
            </h2>
            <button class="tables-config-close" onclick="closeBulkStateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tables-config-content">
            <!-- Advertencia -->
            <div class="warning-box">
                <div class="warning-box-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    ¡Atención!
                </div>
                <div class="warning-box-text">
                    Esta acción cambiará el estado de <strong>todas las mesas</strong> registradas en el sistema.
                </div>
            </div>

            <!-- Estadísticas actuales -->
            <div class="stats-container">
                <p style="font-weight: 600; color: var(--primary-color); margin: 0 0 12px 0; font-size: 0.95rem;">
                    Estado actual de las mesas:
                </p>
                <div id="bulk-stats-content" class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-dot green"></span>
                        <span>Disponible: <strong id="stat-disponible">0</strong></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-dot red"></span>
                        <span>Ocupada: <strong id="stat-ocupada">0</strong></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-dot yellow"></span>
                        <span>Reservada: <strong id="stat-reservada">0</strong></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-dot gray"></span>
                        <span>No Disponible: <strong id="stat-no-disponible">0</strong></span>
                    </div>
                </div>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-color); font-size: 0.875rem;">
                    Total de mesas: <strong id="stat-total">0</strong>
                </div>
            </div>

            <form id="bulk-state-form" onsubmit="handleBulkStateChange(event)">
                <div class="form-group">
                    <label class="form-label">Seleccione el nuevo estado:</label>
                    <select id="bulk-state-select" class="form-select" required>
                        <option value="">-- Seleccione un estado --</option>
                        <option value="Disponible">✓ Disponible</option>
                        <option value="Ocupada">● Ocupada</option>
                        <option value="Reservada">◐ Reservada</option>
                        <option value="No Disponible">✗ No Disponible</option>
                    </select>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-cancel" onclick="closeBulkStateModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-save warning">
                        <i class="fas fa-check"></i>
                        Aplicar a Todas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Principal de Pagos --}}
<div id="payment-modal" class="payment-modal hidden">
    <div class="payment-modal-overlay" onclick="closePaymentModal()"></div>
    <div class="payment-modal-container">
        <div class="payment-modal-header">
            <h2>Procesar Pedido</h2>
            <button class="payment-modal-close" onclick="closePaymentModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="payment-modal-content">
            <!-- Navegación por pasos (3 pasos ahora) -->
            <div class="step-navigation">
                <div class="step-item active" data-step="1">Tipo de Pedido</div>
                <div class="step-item" data-step="2">Método de Pago</div>
                <div class="step-item" data-step="3">Detalles del Cliente</div>
            </div>

            <!-- Paso 1: Tipo de Pedido -->
            <div class="step-content active" id="step-1">
                <div class="order-type-section">
                    <h3>Selecciona el Tipo de Pedido</h3>
                    <div class="order-type-buttons">
                        <button class="order-type-btn selected" data-type="comer-aqui">
                            <i class="fas fa-utensils"></i>Comer aquí
                        </button>
                        <button class="order-type-btn" data-type="para-llevar">
                            <i class="fas fa-shopping-bag"></i>Retiro por Delivery
                        </button>
                        <button class="order-type-btn" data-type="recoger">
                            <i class="fas fa-box"></i>Retiro del Local
                        </button>
                    </div>
                </div>

                <!-- Selección de Mesa (solo para "Comer aquí") -->
                <div class="table-selection hidden" id="modal-table-selection">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="margin: 0;">Selecciona una Mesa</h4>
        <button onclick="openTablesConfigModal()" class="tables-config-btn" type="button">
            <i class="fas fa-cog"></i>
            <span>Config</span>
        </button>
    </div>
    <!-- Mensaje cuando las mesas están deshabilitadas -->
     <div id="tables-disabled-message" class="hidden" style="padding: 16px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px; color: #92400e;">
            <i class="fas fa-info-circle" style="font-size: 1.2rem;"></i>
            <div>
                <p style="margin: 0; font-weight: 600;">Gestión de mesas deshabilitada</p>
                <small style="display: block; margin-top: 4px; font-size: 0.85rem;">
                    Los pedidos para comer aquí no requieren asignación de mesa.
                </small>
            </div>
        </div>
    </div>
    <div id="table-loading" class="text-center py-4 text-gray-500">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        Verificando estado de mesas...
    </div>
    <div class="table-grid hidden" id="table-grid">
        <!-- Las mesas se cargarán dinámicamente aquí -->
    </div>
    <div id="table-error" class="hidden text-center py-4 text-red-500">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <span id="table-error-message">Error al cargar las mesas</span>
        <button onclick="loadModalTables()" class="ml-2 text-sm underline hover:no-underline">
            Intentar de nuevo
        </button>
    </div>
                </div>

                <!-- Selección de Delivery (solo para "Para llevar") -->
                <div class="delivery-selection hidden" id="modal-delivery-selection">
                    <h4>Servicio de Delivery</h4>
                    <select id="modal-delivery-service" class="form-select">
                        <option value="">Seleccione un servicio de delivery</option>
                    </select>
                </div>

                <!-- Notas para Recoger (solo para "Recoger") -->
                <div class="delivery-selection hidden" id="modal-pickup-notes">
                    <h4>
                        <i class="fas fa-clipboard"></i>
                        Notas del Pedido
                    </h4>
                    <textarea 
                        id="modal-pickup-notes-text" 
                        class="form-input" 
                        placeholder="Agregar instrucciones especiales, nombre del cliente, hora estimada de recojo, etc."
                        rows="4"
                        style="resize: vertical; min-height: 100px;"
                    ></textarea>
                    <small style="display: block; margin-top: 8px; color: var(--text-secondary); font-size: 0.85rem;">
                        <i class="fas fa-info-circle"></i>
                        Opcional: Agrega cualquier información relevante para el pedido
                    </small>
                </div>

                <div class="step-actions">
                    <button class="step-btn prev" disabled>Anterior</button>
                    <button class="step-btn next" onclick="nextStep()">Siguiente</button>
                </div>
            </div>

            <!-- Paso 2: Métodos de Pago -->
    <div class="step-content" id="step-2">
    <div class="payment-summary">
        <h3>Métodos de Pago</h3>
        <div class="total-display">
            Total: $<span id="order-total">0.00</span>
        </div>

        <button class="add-payment-btn" onclick="addPaymentRow()">
            <i class="fas fa-plus-circle"></i>
            Agregar método de pago
        </button>

      

        <div class="payment-rows-container" id="payment-rows-container">
            <!-- Las filas de pago se agregarán aquí dinámicamente -->
        </div>
    </div>

    <div class="step-actions">
        <button class="step-btn prev" onclick="prevStep()">Anterior</button>
        <button class="step-btn next" onclick="nextStep()">Siguiente</button>
    </div>
    </div>
           <!-- Paso 3: Detalles del Cliente  -->
            <div class="step-content" id="step-3">
                <div class="customer-details-section">
                    <h3>
                        <i class="fas fa-user-circle"></i>
                        Información del Cliente
                    </h3>

                    <!-- Resumen del Pedido -->
                    <div class="order-summary-card">
                        <h4>
                            <i class="fas fa-receipt"></i>
                            Resumen del Pedido
                        </h4>
                        <div id="step3-order-summary">
                            <!-- Se llenará dinámicamente -->
                        </div>
                        <div class="summary-total">
                            Total a Pagar: $<span id="step3-order-total">0.00</span>
                        </div>
                    </div>

                    <!-- Detalles de Pago -->
                    <div class="payment-details-card" id="step3-payment-details">
                        <h4>
                            <i class="fas fa-credit-card"></i>
                            Detalles de Pago
                        </h4>
                        <div id="step3-payment-methods">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>

                    <!-- Formulario de Datos del Cliente -->
    <div class="customer-form-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4>
                <i class="fas fa-id-card"></i>
                Datos del Cliente
            </h4>
            <div style="display: flex; gap: 8px;">
                <button onclick="openClientsConfigModal()" 
                        class="clients-config-btn" 
                        type="button"
                        style="background: #203363; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
                    <i class="fas fa-users"></i>
                    <span>Gestionar Clientes</span>
                </button>
                <button onclick="saveCurrentClientToDatabase()" 
                    class="clients-config-btn" 
                    type="button"
                    id="save-current-client-btn"
                    style="background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
                    <i class="fas fa-save"></i>
                    <span>Guardar Cliente</span>
                </button>
        </div>
    </div>
    
    <form id="modal-customer-details-form">
        <!-- Campo Nombre - SIEMPRE VISIBLE -->
        <div class="form-group">
            <label for="modal-customer-name" class="form-label required">
                Nombre Completo
            </label>
            <input 
                type="text" 
                id="modal-customer-name" 
                name="customer_name" 
                class="form-input" 
                placeholder="Ej: Juan Pérez"
                required
            >
            <small style="display: block; margin-top: 4px; color: #6b7280; font-size: 0.8rem;">
                <i class="fas fa-info-circle"></i>
                Ingresa el nombre completo del cliente
            </small>
        </div>

        <!-- SECCIÓN DESPLEGABLE UNIFICADA: Información Adicional -->
        <div class="collapsible-section">
            <button type="button" 
                    class="collapsible-header" 
                    onclick="toggleCollapsibleSection('additional-info-section')">
                <div class="collapsible-title">
                    <i class="fas fa-plus-circle"></i>
                    Agregar Información Adicional (Opcional)
                </div>
                <i class="fas fa-chevron-down collapsible-icon" id="additional-info-section-icon"></i>
            </button>
            <div class="collapsible-content" id="additional-info-section">
                
                <!-- Subsección: Información de Contacto -->
                <div class="subsection-container">
                    {{-- <div class="subsection-header">
                        <i class="fas fa-phone"></i>
                        <span>Información de Contacto</span>
                    </div> --}}
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="modal-customer-email" class="form-label">
                                Correo Electrónico
                            </label>
                            <input 
                                type="email" 
                                id="modal-customer-email" 
                                name="customer_email" 
                                class="form-input" 
                                placeholder="ejemplo@correo.com"
                            >
                        </div>

                        <div class="form-group">
                            <label for="modal-customer-phone" class="form-label">
                                Teléfono
                            </label>
                            <input 
                                type="tel" 
                                id="modal-customer-phone" 
                                name="customer_phone" 
                                class="form-input" 
                                placeholder="+591 7xxxxxxx"
                            >
                        </div>
                    </div>
                </div>

                <!-- Subsección: Documento de Identidad -->
                <div class="subsection-container">
                    {{-- <div class="subsection-header">
                        <i class="fas fa-id-card"></i>
                        <span>Documento de Identidad</span>
                    </div> --}}
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="modal-customer-doc-type" class="form-label">
                                Tipo de Documento
                            </label>
                            <select id="modal-customer-doc-type" name="customer_doc_type" class="form-select">
                                <option value="CI">CI - Carnet de Identidad</option>
                                <option value="NIT">NIT</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="modal-customer-doc-number" class="form-label">
                                Número de Documento
                            </label>
                            <input 
                                type="text" 
                                id="modal-customer-doc-number" 
                                name="customer_doc_number" 
                                class="form-input" 
                                placeholder="Ej: 1234567"
                                style="font-family: monospace;"
                            >
                        </div>
                    </div>
                </div>

                <!-- Subsección: Ubicación -->
                <div class="subsection-container">
                    <div class="subsection-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Ubicación</span>
                    </div>
                    <div class="form-group">
                        <label for="modal-customer-address" class="form-label">
                            Dirección
                        </label>
                        <input 
                            type="text" 
                            id="modal-customer-address" 
                            name="customer_address" 
                            class="form-input" 
                            placeholder="Ej: Calle 123, Zona Centro"
                        >
                    </div>

                    <div class="form-group">
                        <label for="modal-customer-city" class="form-label">
                            Ciudad
                        </label>
                        <input 
                            type="text" 
                            id="modal-customer-city" 
                            name="customer_city" 
                            class="form-input" 
                            placeholder="Ej: La Paz"
                        >
                    </div>
                </div>

                <!-- ✅ NUEVA SUBSECCIÓN: Información Personal -->
                <div class="subsection-container">
                    {{-- <div class="subsection-header">
                        <i class="fas fa-birthday-cake"></i>
                        <span>Información Personal</span>
                    </div> --}}
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="modal-customer-birthday" class="form-label">
                                <i class="fas fa-calendar-day" style="margin-right: 6px; color: #6380a6;"></i>
                                Fecha de Cumpleaños
                            </label>
                            <input 
                                type="date" 
                                id="modal-customer-birthday" 
                                name="customer_birthday" 
                                class="form-input birthday-input"
                                placeholder="dd/mm/aaaa"
                                max="{{ date('Y-m-d') }}"
                            >
                            <small style="display: block; margin-top: 6px; color: #6b7280; font-size: 0.75rem;">
                                <i class="fas fa-gift" style="color: #f59e0b; margin-right: 4px;"></i>
                                Útil para enviar promociones o descuentos especiales
                            </small>
                        </div>

                        <!-- Campo adicional: Edad (calculada automáticamente) -->
                        <div class="form-group">
                            <label for="modal-customer-age" class="form-label">
                                <i class="fas fa-user-clock" style="margin-right: 6px; color: #6380a6;"></i>
                                Edad
                            </label>
                            <input 
                                type="text" 
                                id="modal-customer-age" 
                                name="customer_age" 
                                class="form-input age-display" 
                                placeholder="Se calcula automáticamente"
                                readonly
                                style="background: #f1f5f9; cursor: not-allowed;"
                            >
                            <small style="display: block; margin-top: 6px; color: #6b7280; font-size: 0.75rem;">
                                <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                                Se actualiza al seleccionar fecha de cumpleaños
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Notas Adicionales - SIEMPRE VISIBLE -->
        <div class="form-group">
            <label for="modal-customer-notes" class="form-label">
                Notas Adicionales
            </label>
            <textarea 
                id="modal-customer-notes" 
                name="customer_notes" 
                class="form-input" 
                rows="3"
                placeholder="Alguna solicitud especial o detalle adicional..."
            ></textarea>
        </div>

        <!-- Indicador de cliente guardado -->
        <div id="client-saved-indicator" style="display: none; background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px; border-radius: 8px; margin-top: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Cliente guardado en la base de datos</strong>
                    <br>
                    <small id="saved-client-info">ID: <span id="saved-client-id"></span></small>
                </div>
            </div>
        </div>
    </form>
</div>
                </div>

                <div class="step-actions">
                    <button class="step-btn prev" onclick="prevStep()">Anterior</button>
                    <button class="step-btn confirm" onclick="confirmAndProcessOrder()">
                        <i class="fas fa-check-circle"></i>
                        Confirmar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Modal de Configuración de Clientes --}}
<div id="clients-config-modal" class="tables-config-modal">
    <div class="tables-config-container">
        <div class="tables-config-header">
            <h2>
                <i class="fas fa-users"></i>
                Gestión de Clientes
            </h2>
            <button class="tables-config-close" onclick="closeClientsConfigModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="tables-config-content">
            <!-- Mensaje de éxito -->
            <div id="clients-success-message" class="success-message">
                <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
                <span id="clients-success-text">Cliente guardado exitosamente</span>
            </div>

            <!-- Botones de acción superior -->
            <div style="display: flex; gap: 12px; margin-bottom: 20px;">
                <button class="bulk-apply-btn" onclick="openCreateClientModal()" style="flex: 1;">
                    <i class="fas fa-plus"></i>
                    Crear Cliente
                </button>
                <button class="bulk-apply-btn warning" onclick="refreshClientsList()" style="flex: 1;">
                    <i class="fas fa-sync-alt"></i>
                    Actualizar Lista
                </button>
            </div>

            <!-- Barra de búsqueda -->
            <div style="margin-bottom: 20px;">
                <input type="text" 
                       id="clients-search" 
                       placeholder="Buscar por nombre, documento o teléfono..."
                       onkeyup="filterClients()"
                       style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <!-- Tabla de clientes -->
            <div class="tables-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clients-tbody">
                        <!-- Los clientes se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- Estado vacío -->
            <div id="clients-empty-state" class="empty-state" style="display: none;">
                <i class="fas fa-users"></i>
                <p>No hay clientes registrados</p>
                <small>Crea un nuevo cliente para comenzar</small>
            </div>

            <!-- Botones de acción inferior -->
            <div class="action-buttons">
                <button class="btn btn-cancel" onclick="closeClientsConfigModal()">
                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para crear/editar cliente --}}
<div id="create-client-modal" class="tables-config-modal">
    <div class="tables-config-container" style="max-width: 600px;">
        <div class="tables-config-header">
            <h2>
                <i class="fas fa-plus-circle"></i>
                <span id="create-client-title">Crear Nuevo Cliente</span>
            </h2>
            <button class="tables-config-close" onclick="closeCreateClientModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tables-config-content">
            <form id="create-client-form" onsubmit="handleCreateClient(event)">
                <input type="hidden" id="edit-client-id" value="">
                
                <!-- Información Personal -->
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 12px 0; color: #203363; font-size: 0.9rem; font-weight: 600;">
                        Información Personal
                    </h4>
                    <div class="form-group">
                        <label class="form-label">Nombre <span style="color: red;">*</span></label>
                        <input type="text" id="client-name-input" class="form-input" placeholder="Ej: Juan" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido <span style="color: red;">*</span></label>
                        <input type="text" id="client-lastname-input" class="form-input" placeholder="Ej: Pérez" required>
                    </div>
                </div>

                <!-- Documento -->
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 12px 0; color: #203363; font-size: 0.9rem; font-weight: 600;">
                        Documento de Identidad
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label class="form-label">Tipo de Documento <span style="color: red;">*</span></label>
                            <select id="client-doc-type-input" class="form-select" required>
                                <option value="CI">CI - Carnet de Identidad</option>
                                <option value="NIT">NIT</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Número de Documento</label>
                            <input type="text" id="client-doc-number-input" class="form-input" placeholder="Ej: 1234567">
                        </div>
                    </div>
                </div>

                <!-- Contacto -->
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 12px 0; color: #203363; font-size: 0.9rem; font-weight: 600;">
                        Información de Contacto
                    </h4>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" id="client-phone-input" class="form-input" placeholder="Ej: +591 71234567">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" id="client-email-input" class="form-input" placeholder="Ej: cliente@email.com">
                    </div>
                </div>

                <!-- Ubicación -->
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 12px 0; color: #203363; font-size: 0.9rem; font-weight: 600;">
                        Ubicación
                    </h4>
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <input type="text" id="client-address-input" class="form-input" placeholder="Ej: Calle 123, Zona Centro">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ciudad</label>
                        <input type="text" id="client-city-input" class="form-input" placeholder="Ej: La Paz">
                    </div>
                </div>

                <!-- Notas y Estado -->
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                    <h4 style="margin: 0 0 12px 0; color: #203363; font-size: 0.9rem; font-weight: 600;">
                        Información Adicional
                    </h4>
                    <div class="form-group">
                        <label class="form-label">Notas</label>
                        <textarea id="client-notes-input" class="form-input" rows="3" placeholder="Observaciones adicionales"></textarea>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="client-active-input" checked style="width: 16px; height: 16px;">
                            <span style="font-size: 0.9rem; color: #203363;">Cliente activo</span>
                        </label>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-cancel" onclick="closeCreateClientModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Script de inicialización --}}
<script>
// Inicializar el estado de las mesas desde el backend
window.tablesConfigState = {
    tables: [],
    isLoading: false,
    tablesEnabled: {{ $settings->tables_enabled ? 'true' : 'false' }}
};

console.log('✅ Estado inicial de mesas:', window.tablesConfigState.tablesEnabled);
</script>
<script>
// Variable global para controlar el estado de las mesas
window.tablesManagementEnabled = {{ $settings->tables_enabled ? 'true' : 'false' }};

console.log('🔧 Estado de gestión de mesas:', window.tablesManagementEnabled);
// Función para abrir el modal de configuración desde el modal de pago
function openTablesConfigModalFromPayment() {
    console.log('🔧 Abriendo modal de configuración de mesas desde modal de pago');
    const configModal = document.getElementById('tables-config-modal');
    if (configModal) {
        configModal.classList.add('show');
        // NO cerrar el modal de pago, mantenerlo abierto en el fondo
    } else {
        console.error('❌ Modal tables-config-modal no encontrado');
    }
}
function closeTablesConfigModal() {
    console.log('✅ Cerrando modal de configuración');
    const configModal = document.getElementById('tables-config-modal');
    const paymentModal = document.getElementById('payment-modal');
    
    if (configModal) {
        configModal.classList.remove('show');
    }
    
    // ASEGURAR que el modal de pago permanezca visible
    if (paymentModal) {
        paymentModal.classList.remove('hidden');
        paymentModal.style.display = 'flex';
        console.log('✅ Modal de pago permanece abierto');
    }
}
// Interceptar el guardado de configuración para actualizar el estado
async function saveTablesConfig() {
    console.log('💾 Guardando configuración de mesas...');
    
    const toggleInput = document.getElementById('tables-enabled-input');
    const saveBtn = document.getElementById('save-tables-config');
    const successMessage = document.getElementById('config-success-message');
    const paymentModal = document.getElementById('payment-modal');
    
    if (!toggleInput || !saveBtn) {
        console.error('❌ Elementos del formulario no encontrados');
        return;
    }
    
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    saveBtn.classList.add('btn-loading');
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('tables_enabled', toggleInput.checked ? '1' : '0');
    
    const tableRows = document.querySelectorAll('#tables-tbody tr');
    const tablesData = [];
    
    tableRows.forEach(row => {
        const tableId = row.dataset.tableId;
        const stateSelect = row.querySelector('.table-state-select');
        if (tableId && stateSelect) {
            tablesData.push({
                id: tableId,
                state: stateSelect.value
            });
        }
    });
    
    formData.append('tables', JSON.stringify(tablesData));
    
    fetch('/settings/update', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(async data => {
        if (data.success) {
            // ✅ CRÍTICO: Actualizar estado INMEDIATAMENTE
            const newState = toggleInput.checked;
            window.tablesManagementEnabled = newState;
            
            if (!window.tablesConfigState) {
                window.tablesConfigState = {};
            }
            window.tablesConfigState.tablesEnabled = newState;
            
            // 🔥 Resetear TODAS las banderas
            window._tableVisibilityChecked = false;
            window._modalTablesLoaded = false;
            window._orderTypeButtonsConfigured = false;
            _handlingTableVisibility = false;
            _lastHandlingTime = 0;
            
            console.log('✅ Estado actualizado INMEDIATAMENTE:', {
                newState: newState,
                tablesManagementEnabled: window.tablesManagementEnabled,
                tablesConfigState: window.tablesConfigState.tablesEnabled
            });
            
            // Mostrar mensaje de éxito
            if (successMessage) {
                const messageText = successMessage.querySelector('#success-message-text');
                if (messageText) {
                    messageText.textContent = data.message || 'Configuración guardada exitosamente';
                }
                successMessage.classList.add('show');
                setTimeout(() => {
                    successMessage.classList.remove('show');
                }, 3000);
            }
            
            // 🔥 SECCIÓN CORREGIDA: Actualizar modal de pago si está abierto
            setTimeout(async () => {
                const selectedButton = document.querySelector('.order-type-btn.selected');
                
                // ✅ Solo actualizar si "Comer aquí" está seleccionado
                if (selectedButton && selectedButton.dataset.type === 'comer-aqui') {
                    console.log('🔄 Actualizando visibilidad ANTES de cerrar modal...');
                    await handleTableSelectionVisibility(true);
                }
                
                // Cerrar el modal de configuración
                closeTablesConfigModal();
                
                // Reconfigurar botones
                setupOrderTypeButtons();
                
                // ✅ CRÍTICO: Si modal de pago está abierto, actualizar contenido
                if (paymentModal && !paymentModal.classList.contains('hidden')) {
                    console.log('🔄 Modal de pago abierto, actualizando contenido...');
                    
                    const tableSelection = document.getElementById('modal-table-selection');
                    
                    if (newState) {
                        // ✅ Mesas HABILITADAS: Mostrar grid
                        console.log('✅ Mesas habilitadas: mostrando grid');
                        
                        if (tableSelection) {
                            tableSelection.classList.remove('hidden');
                        }
                        
                        // Ocultar mensaje de deshabilitado
                        const disabledMessage = document.getElementById('tables-disabled-message');
                        if (disabledMessage) {
                            disabledMessage.classList.add('hidden');
                        }
                        
                        // Mostrar y cargar grid
                        const tableGrid = document.getElementById('table-grid');
                        if (tableGrid) {
                            tableGrid.classList.remove('hidden');
                            // Forzar recarga de mesas
                            window._modalTablesLoaded = false;
                            await loadModalTables(true);
                        }
                        
                    } else {
                        // ✅ Mesas DESHABILITADAS: Mostrar mensaje
                        console.log('❌ Mesas deshabilitadas: mostrando mensaje');
                        
                        // ✅ MANTENER tableSelection VISIBLE (contiene el botón)
                        if (tableSelection) {
                            tableSelection.classList.remove('hidden');
                        }
                        
                        // ✅ MOSTRAR mensaje de deshabilitado
                        const disabledMessage = document.getElementById('tables-disabled-message');
                        if (disabledMessage) {
                            disabledMessage.classList.remove('hidden');
                            console.log('✅ Mensaje de mesas deshabilitadas mostrado');
                        }
                        
                        // ✅ OCULTAR grid de mesas
                        const tableGrid = document.getElementById('table-grid');
                        if (tableGrid) {
                            tableGrid.classList.add('hidden');
                            console.log('✅ Grid de mesas ocultado');
                        }
                        
                        // ✅ Ocultar loading si existe
                        const tableLoading = document.getElementById('table-loading');
                        if (tableLoading) {
                            tableLoading.classList.add('hidden');
                        }
                    }
                    
                    // ✅ Asegurar que el modal de pago permanezca visible
                    paymentModal.classList.remove('hidden');
                    paymentModal.style.display = 'flex';
                    
                    console.log('✅ Actualización completada, verificando estado final...');
                    
                    // Verificación final
                    setTimeout(() => {
                        const finalState = {
                            tableSelection: !document.getElementById('modal-table-selection')?.classList.contains('hidden'),
                            disabledMessage: !document.getElementById('tables-disabled-message')?.classList.contains('hidden'),
                            tableGrid: !document.getElementById('table-grid')?.classList.contains('hidden'),
                            configButton: !!document.querySelector('.tables-config-btn')
                        };
                        console.log('🔍 Estado final de elementos:', finalState);
                    }, 100);
                }
            }, 500);
            
        } else {
            throw new Error(data.message || 'Error al guardar la configuración');
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Error al guardar la configuración: ' + error.message);
        
        if (paymentModal) {
            paymentModal.classList.remove('hidden');
            paymentModal.style.display = 'flex';
        }
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        saveBtn.classList.remove('btn-loading');
    });
}

// Función para verificar si las mesas están habilitadas
function areTablesEnabled() {
    return window.tablesManagementEnabled === true;
}

// Variable global para controlar ejecuciones múltiples
let _handlingTableVisibility = false;
let _lastHandlingTime = 0;
const DEBOUNCE_DELAY = 300; // milisegundos

async function handleTableSelectionVisibility(forceUpdate = false) {
    const now = Date.now();
    
    if (!forceUpdate) {
        if (_handlingTableVisibility || (now - _lastHandlingTime < DEBOUNCE_DELAY)) {
            console.log('⏸️ handleTableSelectionVisibility bloqueado');
            return;
        }
    }
    
    _handlingTableVisibility = true;
    _lastHandlingTime = now;
    
    try {
        const tableSelection = document.getElementById('modal-table-selection');
        const tableGrid = document.getElementById('table-grid');
        const tablesDisabledMessage = document.getElementById('tables-disabled-message');
        const tableLoading = document.getElementById('table-loading');
        
        if (!tableSelection) {
            console.warn('⚠️ modal-table-selection no encontrado');
            return;
        }
        tableSelection.classList.remove('hidden');
        // Mostrar loading
        if (tableLoading) {
            tableLoading.classList.remove('hidden');
        }
        if (tableGrid) tableGrid.classList.add('hidden');
        if (tablesDisabledMessage) tablesDisabledMessage.classList.add('hidden');
        

        console.log('🔄 Verificando estado de mesas...');
        console.log('📊 Estado ANTES de verificar:', {
            tablesManagementEnabled: window.tablesManagementEnabled,
            tablesConfigState: window.tablesConfigState?.tablesEnabled
        });
        
        // Verificar estado desde el servidor
        const tablesEnabled = await checkTablesEnabledStatus();
        
        console.log('🔍 Estado DESPUÉS de verificar:', {
            tablesEnabled: tablesEnabled,
            tablesManagementEnabled: window.tablesManagementEnabled,
            tablesConfigState: window.tablesConfigState?.tablesEnabled
        });
        
        // Ocultar loading
        if (tableLoading) {
            tableLoading.classList.add('hidden');
        }
        
        // Mostrar contenido según el estado
        if (tablesEnabled) {
            console.log('✅ MOSTRANDO GRID DE MESAS');
            
            if (tableGrid) {
                tableGrid.classList.remove('hidden');
                console.log('   ✓ table-grid mostrado');
            }
            
            if (tablesDisabledMessage) {
                tablesDisabledMessage.classList.add('hidden');
                console.log('   ✓ mensaje deshabilitado ocultado');
            }
            
            // Forzar recarga de mesas
            window._modalTablesLoaded = false;
            await loadModalTables(true);
            
        } else {
            console.log('❌ MOSTRANDO MENSAJE DE MESAS DESHABILITADAS');
            
            if (tableGrid) {
                tableGrid.classList.add('hidden');
                console.log('   ✓ table-grid ocultado');
            }
            
            if (tablesDisabledMessage) {
                tablesDisabledMessage.classList.remove('hidden');
                console.log('   ✓ mensaje deshabilitado mostrado');
            }
        }
        
        console.log('✅ Visibilidad actualizada');
        
    } catch (error) {
        console.error('❌ Error en handleTableSelectionVisibility:', error);
        const tableLoading = document.getElementById('table-loading');
        if (tableLoading) tableLoading.classList.add('hidden');
    } finally {
        setTimeout(() => {
            _handlingTableVisibility = false;
        }, DEBOUNCE_DELAY);
    }
}
function setupOrderTypeButtons() {
    const orderTypeButtons = document.querySelectorAll('.order-type-btn');
    
    console.log('🔧 Configurando botones de tipo de pedido...');
    
    orderTypeButtons.forEach(button => {
        // 🔥 NO CLONAR - Remover listeners antiguos de forma diferente
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        newButton.addEventListener('click', async function() {
            console.log('🖱️ Click en botón:', this.dataset.type);
            
            // Remover selección de todos los botones
            document.querySelectorAll('.order-type-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            // Agregar selección al botón clickeado
            this.classList.add('selected');
            
            const orderType = this.dataset.type;
            const tableSelection = document.getElementById('modal-table-selection');
            const deliverySelection = document.getElementById('modal-delivery-selection');
            const pickupNotes = document.getElementById('modal-pickup-notes');
            
            // Ocultar todas las secciones primero
            if (tableSelection) tableSelection.classList.add('hidden');
            if (deliverySelection) deliverySelection.classList.add('hidden');
            if (pickupNotes) pickupNotes.classList.add('hidden');
            
            // Mostrar la sección correspondiente
            if (orderType === 'comer-aqui') {
                if (tableSelection) {
                    tableSelection.classList.remove('hidden');
                    console.log('🍽️ Tipo "Comer aquí" seleccionado');
                    
                    // 🔥 SIEMPRE verificar estado actual (sin usar bandera)
                    _handlingTableVisibility = false;
                    _lastHandlingTime = 0;
                    await handleTableSelectionVisibility(true); // Forzar actualización
                }
            } else if (orderType === 'para-llevar') {
                if (deliverySelection) {
                    deliverySelection.classList.remove('hidden');
                    if (typeof loadDeliveryServices === 'function') {
                        loadDeliveryServices();
                    }
                }
            } else if (orderType === 'recoger') {
                if (pickupNotes) {
                    pickupNotes.classList.remove('hidden');
                }
            }
        });
    });
    
    console.log('✅ Botones configurados correctamente');
}
async function checkTablesEnabledStatus() {
    try {
        console.log('🔍 Verificando estado de mesas desde el servidor...');
        
        // 🔥 Agregar timestamp para evitar caché del navegador
        const timestamp = Date.now();
        const response = await fetch(`/settings/tables-status?t=${timestamp}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al obtener estado de mesas');
        }
        
        const data = await response.json();
        const tablesEnabled = data.tables_enabled || false;
        
        // ✅ Actualizar TODAS las variables globales
        window.tablesManagementEnabled = tablesEnabled;
        
        if (!window.tablesConfigState) {
            window.tablesConfigState = {};
        }
        window.tablesConfigState.tablesEnabled = tablesEnabled;
        
        console.log('✅ Estado de mesas obtenido del servidor:', {
            tablesEnabled: tablesEnabled,
            source: 'backend (sin caché)'
        });
        
        return tablesEnabled;
        
    } catch (error) {
        console.error('❌ Error al verificar estado de mesas:', error);
        
        const fallbackValue = window.tablesConfigState?.tablesEnabled || 
                             window.tablesManagementEnabled || 
                             false;
        
        console.log('⚠️ Usando valor fallback:', fallbackValue);
        return fallbackValue;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Inicializando sistema de validación de mesas...');
    
    // Configurar botones de tipo de pedido
    setupOrderTypeButtons();
    
    // Verificar estado inicial si "Comer aquí" está seleccionado
    setTimeout(async () => {
        const selectedButton = document.querySelector('.order-type-btn.selected');
        const tableSelection = document.getElementById('modal-table-selection');
        
        if (selectedButton && 
            selectedButton.dataset.type === 'comer-aqui' && 
            tableSelection && 
            !tableSelection.classList.contains('hidden')) {
            
            console.log('🔄 Inicialización: "Comer aquí" seleccionado, verificando estado...');
           // await handleTableSelectionVisibility();
        }
    }, 300);
});
// Modificar la función que maneja el cambio de tipo de pedido
// Busca la función existente y reemplázala con esta versión mejorada:
document.addEventListener('DOMContentLoaded', function() {
    const orderTypeButtons = document.querySelectorAll('.order-type-btn');
    
    orderTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover selección de todos los botones
            orderTypeButtons.forEach(btn => btn.classList.remove('selected'));
            
            // Agregar selección al botón clickeado
            this.classList.add('selected');
            
            const orderType = this.dataset.type;
            const tableSelection = document.getElementById('modal-table-selection');
            const deliverySelection = document.getElementById('modal-delivery-selection');
            const pickupNotes = document.getElementById('modal-pickup-notes');
            
            // Ocultar todas las secciones primero
            if (tableSelection) tableSelection.classList.add('hidden');
            if (deliverySelection) deliverySelection.classList.add('hidden');
            if (pickupNotes) pickupNotes.classList.add('hidden');
            
            // Mostrar la sección correspondiente según el tipo de pedido
            if (orderType === 'comer-aqui') {
                if (tableSelection) {
                    tableSelection.classList.remove('hidden');
                    
                    // ✅ CRÍTICO: Verificar estado de mesas CADA VEZ que se muestra la sección
                    console.log('🔄 Tipo "Comer aquí" seleccionado, verificando estado de mesas...');
                   // handleTableSelectionVisibility();
                }
                console.log('🍽️ Tipo de pedido: Comer aquí');
            } else if (orderType === 'para-llevar') {
                if (deliverySelection) {
                    deliverySelection.classList.remove('hidden');
                    loadDeliveryServices();
                }
                console.log('🚚 Tipo de pedido: Para llevar (Delivery)');
            } else if (orderType === 'recoger') {
                if (pickupNotes) {
                    pickupNotes.classList.remove('hidden');
                }
                console.log('📦 Tipo de pedido: Recoger del local');
            }
        });
    });
    
    // ✅ INICIALIZAR visibilidad al cargar la página
    const selectedButton = document.querySelector('.order-type-btn.selected');
    if (selectedButton && selectedButton.dataset.type === 'comer-aqui') {
        console.log('🔄 Inicializando visibilidad de mesas al cargar página...');
        setTimeout(() => {
          //  handleTableSelectionVisibility();
        }, 200);
    }
});

// Modificar la función nextStep para validar según el estado de las mesas
function nextStep() {
    const currentStep = document.querySelector('.step-content.active');
    const currentStepNumber = parseInt(currentStep.id.split('-')[1]);
    
    // Validación del Paso 1
    if (currentStepNumber === 1) {
        const selectedOrderType = document.querySelector('.order-type-btn.selected');
        if (!validateStep1()) {
            return;
        }
        if (!selectedOrderType) {
            alert('Por favor selecciona un tipo de pedido');
            return;
        }
        
        const orderType = selectedOrderType.dataset.type;
        
        // Validación para "Comer aquí"
        if (orderType === 'comer-aqui') {
            // ✅ VERIFICAR SI LAS MESAS ESTÁN HABILITADAS ANTES DE VALIDAR
            const tablesEnabled = window.tablesConfigState?.tablesEnabled || 
                                  window.tablesManagementEnabled || 
                                  false;
            
            console.log('🔍 Validando paso 1:', {
                orderType: 'comer-aqui',
                tablesEnabled: tablesEnabled
            });
            
            // Solo validar mesa si las mesas están habilitadas
            if (tablesEnabled) {
                const selectedTable = document.querySelector('.table-btn.selected');
                if (!selectedTable) {
                    alert('Por favor selecciona una mesa');
                    return;
                }
                console.log('✅ Mesa seleccionada:', selectedTable.dataset.tableId);
            } else {
                console.log('⚠️ Mesas deshabilitadas - omitiendo validación de mesa');
            }
        }
        
        // Validación para "Para llevar"
        if (orderType === 'para-llevar') {
            const deliveryService = document.getElementById('modal-delivery-service');
            if (!deliveryService || !deliveryService.value) {
                alert('Por favor selecciona un servicio de delivery');
                return;
            }
            console.log('✅ Servicio de delivery seleccionado:', deliveryService.value);
        }
        
        // Para "Recoger" no hay validación requerida
        if (orderType === 'recoger') {
            console.log('✅ Tipo de pedido: Recoger (sin validaciones adicionales)');
        }
    }
    
    // Validación del Paso 2 (Métodos de Pago)
    if (currentStepNumber === 2) {
        const paymentRows = document.querySelectorAll('.payment-row');
        
        if (paymentRows.length === 0) {
            alert('Por favor agrega al menos un método de pago');
            return;
        }
        
        let isValid = true;
        paymentRows.forEach(row => {
            const method = row.querySelector('.payment-type');
            const amount = row.querySelector('.total-paid');
            
            if (!method || !method.value || !amount || !amount.value) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            alert('Por favor completa todos los métodos de pago');
            return;
        }
        
        updateStep3Summary();
    }
    
    // Avanzar al siguiente paso
    const nextStepNumber = currentStepNumber + 1;
    if (nextStepNumber <= 3) {
        currentStep.classList.remove('active');
        document.getElementById(`step-${nextStepNumber}`).classList.add('active');
        
        document.querySelectorAll('.step-item').forEach(item => {
            const stepNum = parseInt(item.dataset.step);
            if (stepNum === nextStepNumber) {
                item.classList.add('active');
            } else if (stepNum < nextStepNumber) {
                item.classList.add('completed');
                item.classList.remove('active');
            } else {
                item.classList.remove('active', 'completed');
            }
        });
        
        console.log(`✅ Avanzando al paso ${nextStepNumber}`);
    }
}
// Función para actualizar el resumen en el paso 3
function updateStep3Summary() {
    const orderSummary = document.getElementById('step3-order-summary');
    const paymentDetails = document.getElementById('step3-payment-methods');
    
    if (!orderSummary || !paymentDetails) return;
    
    // Obtener tipo de pedido
    const selectedOrderType = document.querySelector('.order-type-btn.selected');
    const orderType = selectedOrderType ? selectedOrderType.dataset.type : '';
    
    let orderTypeText = '';
    if (orderType === 'comer-aqui') orderTypeText = '🍽️ Comer aquí';
    else if (orderType === 'para-llevar') orderTypeText = '🚚 Para llevar (Delivery)';
    else if (orderType === 'recoger') orderTypeText = '📦 Recoger del local';
    
    // Construir resumen del pedido
    let summaryHTML = `
        <div class="summary-item">
            <span class="summary-item-label">Tipo de Pedido:</span>
            <span class="summary-item-value">${orderTypeText}</span>
        </div>
    `;
    
    // Agregar información de mesa solo si está habilitada y seleccionada
     if (orderType === 'comer-aqui') {
        const tablesEnabled = window.tablesConfigState?.tablesEnabled || 
                              window.tablesManagementEnabled || 
                              false;
        
        if (tablesEnabled) {
            const selectedTable = document.querySelector('.table-btn.selected');
            if (selectedTable) {
                summaryHTML += `
                    <div class="summary-item">
                        <span class="summary-item-label">Mesa:</span>
                        <span class="summary-item-value">Mesa ${selectedTable.textContent.trim()}</span>
                    </div>
                `;
            }
        } else {
            summaryHTML += `
                <div class="summary-item">
                    <span class="summary-item-label">Mesa:</span>
                    <span class="summary-item-value" style="color: #94a3b8; font-style: italic;">Sin asignación de mesa</span>
                </div>
            `;
        }
    }
    
    // Agregar información de delivery
    if (orderType === 'para-llevar') {
        const deliveryService = document.getElementById('modal-delivery-service');
        if (deliveryService && deliveryService.value) {
            const selectedOption = deliveryService.options[deliveryService.selectedIndex];
            summaryHTML += `
                <div class="summary-item">
                    <span class="summary-item-label">Servicio de Delivery:</span>
                    <span class="summary-item-value">${selectedOption.text}</span>
                </div>
            `;
        }
    }
    
    orderSummary.innerHTML = summaryHTML;
    
    // Construir resumen de métodos de pago
    const paymentRows = document.querySelectorAll('.payment-row');
    let paymentHTML = '';
    let total = 0;
    
    paymentRows.forEach(row => {
        const methodSelect = row.querySelector('.payment-method-select');
        const amountInput = row.querySelector('.payment-amount-input');
        
        if (methodSelect && amountInput && methodSelect.value && amountInput.value) {
            const methodName = methodSelect.options[methodSelect.selectedIndex].text;
            const amount = parseFloat(amountInput.value);
            total += amount;
            
            paymentHTML += `
                <div class="payment-method-item">
                    <div class="payment-method-name">
                        <div class="payment-method-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <span>${methodName}</span>
                    </div>
                    <div class="payment-method-amount">$${amount.toFixed(2)}</div>
                </div>
            `;
        }
    });
    
    paymentDetails.innerHTML = paymentHTML;
    
    // Actualizar total
    const step3Total = document.getElementById('step3-order-total');
    if (step3Total) {
        step3Total.textContent = total.toFixed(2);
    }
}

console.log('✅ Lógica de mesas deshabilitadas implementada correctamente');
// Interceptar clics en el overlay del modal de configuración
document.addEventListener('DOMContentLoaded', function() {
    const configModal = document.getElementById('tables-config-modal');
    
    if (configModal) {
        // Prevenir que el clic en el overlay cierre el modal de pago
        configModal.addEventListener('click', function(e) {
            // Solo cerrar si se hace clic directamente en el modal (overlay)
            if (e.target === configModal) {
                e.stopPropagation();
                closeTablesConfigModal();
            }
        });
        
        console.log('✅ Event listener del modal de configuración configurado');
    }
    
   // Manejar la tecla ESC para cerrar SOLO el modal de configuración
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const configModal = document.getElementById('tables-config-modal');
            const paymentModal = document.getElementById('payment-modal');
            
            // Si el modal de configuración está abierto, cerrarlo
            if (configModal && configModal.classList.contains('show')) {
                e.preventDefault();
                e.stopPropagation();
                closeTablesConfigModal();
                
                // Asegurar que el modal de pago permanezca visible
                if (paymentModal) {
                    paymentModal.classList.remove('hidden');
                    paymentModal.style.display = 'flex';
                }
            }
            // Si solo el modal de pago está abierto, cerrarlo
            else if (paymentModal && !paymentModal.classList.contains('hidden')) {
                closePaymentModal();
            }
        }
    });
});
// Asegurar que el modal de pago tenga un z-index menor al de configuración
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = document.getElementById('payment-modal');
    const configModal = document.getElementById('tables-config-modal');
    
    if (paymentModal) {
        paymentModal.style.zIndex = '1000';
    }
    
    if (configModal) {
        configModal.style.zIndex = '1100';
    }
    
    console.log('✅ Z-index de modales configurado correctamente');
});
// Sobrescribir cualquier función que pueda estar cerrando el modal de pago
window.addEventListener('load', function() {
    // Guardar la función original de cerrar modal de pago
    const originalClosePaymentModal = window.closePaymentModal;
    
    // Sobrescribir con una versión que verifica si el modal de config está abierto
    window.closePaymentModal = function() {
        const configModal = document.getElementById('tables-config-modal');
        
        // Si el modal de configuración está abierto, NO cerrar el modal de pago
        if (configModal && configModal.classList.contains('show')) {
            console.log('🚫 Bloqueado cierre del modal de pago (modal de config abierto)');
            return;
        }
        
        // Si el modal de config NO está abierto, permitir cerrar normalmente
        if (typeof originalClosePaymentModal === 'function') {
            originalClosePaymentModal();
        }
    };
    
    console.log('✅ Función closePaymentModal sobrescrita para proteger el modal');
});
(async function() {
    try {
        const response = await fetch('/settings/tables-status?t=' + Date.now(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Cache-Control': 'no-cache'
            }
        });
        
        const data = await response.json();
        const tablesEnabled = data.tables_enabled || false;
        
        // Inicializar TODAS las variables globales con el mismo valor
        window.tablesConfigState = {
            tables: [],
            isLoading: false,
            tablesEnabled: tablesEnabled
        };
        
        window.tablesManagementEnabled = tablesEnabled;
        
        console.log('✅ Estado inicial de mesas sincronizado:', {
            tablesEnabled: tablesEnabled,
            source: 'blade + fetch'
        });
        
    } catch (error) {
        console.error('❌ Error al sincronizar estado inicial:', error);
        
        // Fallback al valor del servidor
        const fallbackValue = {{ $settings->tables_enabled ? 'true' : 'false' }};
        
        window.tablesConfigState = {
            tables: [],
            isLoading: false,
            tablesEnabled: fallbackValue
        };
        
        window.tablesManagementEnabled = fallbackValue;
        
        console.log('⚠️ Usando valor fallback:', fallbackValue);
    }
})();

</script>

<style>
    /* ============================================
   ESTILOS PARA CAMPO DE CUMPLEAÑOS
   ============================================ */

.birthday-input {
    position: relative !important;
}

.birthday-input::-webkit-calendar-picker-indicator {
    cursor: pointer !important;
    opacity: 0.6 !important;
    transition: opacity 0.2s ease !important;
}

.birthday-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1 !important;
}

.age-display {
    font-weight: 600 !important;
    color: #203363 !important;
    text-align: center !important;
    font-size: 1.1rem !important;
}

/* Indicador visual cuando hay cumpleaños próximo */
.birthday-indicator {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 4px 10px !important;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
    color: #92400e !important;
    border-radius: 12px !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    margin-top: 6px !important;
    animation: pulse 2s ease-in-out infinite !important;
}

.birthday-indicator i {
    color: #f59e0b !important;
    font-size: 0.9rem !important;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.85;
        transform: scale(1.02);
    }
}

/* Estilos para el input de fecha en diferentes navegadores */
.birthday-input::-webkit-inner-spin-button,
.birthday-input::-webkit-outer-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}

.birthday-input[type="date"] {
    position: relative !important;
}

.birthday-input[type="date"]::-webkit-datetime-edit {
    padding: 0 !important;
}

.birthday-input[type="date"]::-webkit-datetime-edit-fields-wrapper {
    padding: 0 !important;
}

/* Estilo cuando el cumpleaños es hoy */
.birthday-today {
    border: 2px solid #f59e0b !important;
    background: #fffbeb !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
}
    .clients-config-btn {
    background: #203363;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    font-size: 0.85rem;
}

.clients-config-btn:hover {
    background: #2d437c;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(32, 51, 99, 0.2);
}
    #table-grid,
#tables-disabled-message,
#table-loading {
    transition: opacity 0.2s ease, visibility 0.2s ease;
}
#table-grid.hidden,
#tables-disabled-message.hidden,
#table-loading.hidden {
    opacity: 0;
    visibility: hidden;
    height: 0;
    overflow: hidden;
}
    /* Estilos para items de método de pago en Paso 3 */
#payment-modal .payment-method-item {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 14px 16px !important;
    background: #f8fafc !important;
    border-radius: 8px !important;
    border: 1px solid var(--border-color) !important;
    transition: all 0.2s ease !important;
    margin-bottom: 12px !important;
}

#payment-modal .payment-method-item:hover {
    background: white !important;
    border-color: var(--primary-color) !important;
    box-shadow: 0 2px 8px rgba(32, 51, 99, 0.08) !important;
}

#payment-modal .payment-method-name {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    font-size: 0.95rem !important;
    color: var(--text-primary) !important;
    font-weight: 500 !important;
}

#payment-modal .payment-method-icon {
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: var(--primary-color) !important;
    color: white !important;
    border-radius: 8px !important;
    font-size: 1rem !important;
}

#payment-modal .payment-method-amount {
    font-size: 1.1rem !important;
    color: var(--success-color) !important;
    font-weight: 700 !important;
}
             /* Estilos para el modal de configuración de mesas */
         #payment-modal .customer-details-section {
            display: flex !important;
            flex-direction: column !important;
            gap: 20px !important;
        }
        #payment-modal .customer-details-section > h3 {
            margin: 0 0 4px 0 !important;
            font-size: 1.4rem !important;
            font-weight: 700 !important;
            color: var(--primary-color) !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding-bottom: 16px !important;
            border-bottom: 2px solid var(--border-color) !important;
        }
        #payment-modal .customer-details-section > h3 i {
            font-size: 1.3rem !important;
            color: var(--secondary-color) !important;
        }
         #payment-modal .order-summary-card,
        #payment-modal .payment-details-card,
        #payment-modal .customer-form-card {
            animation: slideInUp 0.4s ease-out !important;
        }
         #payment-modal .customer-form-card {
            animation-delay: 0.3s !important;
        }
         #payment-modal .order-summary-card {
            animation-delay: 0.1s !important;
        }
        #payment-modal .payment-details-card {
            animation-delay: 0.2s !important;
        }
        #payment-modal .order-summary-card:hover,
        #payment-modal .payment-details-card:hover,
        #payment-modal .customer-form-card:hover {
            box-shadow: 0 4px 16px rgba(32, 51, 99, 0.1) !important;
            border-color: var(--tertiary-color) !important;
        }
          #payment-modal .order-summary-card h4,
        #payment-modal .payment-details-card h4,
        #payment-modal .customer-form-card h4 {
            margin: 0 0 20px 0 !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding-bottom: 12px !important;
            border-bottom: 2px solid #f0f4f8 !important;
        }
        #payment-modal .order-summary-card h4 i,
        #payment-modal .payment-details-card h4 i,
        #payment-modal .customer-form-card h4 i {
            color: var(--secondary-color) !important;
            font-size: 1rem !important;
        }
        #payment-modal #step3-order-summary {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            margin-bottom: 16px !important;
        }
         #payment-modal #step3-order-summary .summary-item {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 12px 16px !important;
            background: #f8fafc !important;
            border-radius: 8px !important;
            border-left: 3px solid var(--tertiary-color) !important;
            transition: all 0.2s ease !important;
        }
        #payment-modal #step3-order-summary .summary-item:hover {
            background: #f0f4f8 !important;
            border-left-color: var(--primary-color) !important;
            transform: translateX(2px) !important;
        }
        #payment-modal #step3-order-summary .summary-item-label {
            font-size: 0.9rem !important;
            color: var(--text-secondary) !important;
            font-weight: 500 !important;
        }
         #payment-modal #step3-order-summary .summary-item-value {
            font-size: 0.95rem !important;
            color: var(--primary-color) !important;
            font-weight: 600 !important;
        }
        #payment-modal .summary-total {
            margin-top: 16px !important;
            padding: 16px 20px !important;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            color: white !important;
            border-radius: 10px !important;
            font-size: 1.2rem !important;
            font-weight: 700 !important;
            text-align: center !important;
            box-shadow: 0 4px 12px rgba(32, 51, 99, 0.2) !important;
            letter-spacing: 0.5px !important;
        }
        #payment-modal .summary-total span {
            font-size: 1.4rem !important;
            margin-left: 8px !important;
        }
        #payment-modal #step3-payment-methods {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
        }
         #payment-modal #step3-payment-methods .payment-method-item {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 14px 16px !important;
            background: #f8fafc !important;
            border-radius: 8px !important;
            border: 1px solid var(--border-color) !important;
            transition: all 0.2s ease !important;
        }
        #payment-modal #step3-payment-methods .payment-method-item:hover {
            background: white !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 2px 8px rgba(32, 51, 99, 0.08) !important;
        }
         #payment-modal #step3-payment-methods .payment-method-name {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 0.9rem !important;
            color: var(--text-primary) !important;
            font-weight: 500 !important;
        }
         #payment-modal #step3-payment-methods .payment-method-icon {
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: var(--primary-color) !important;
            color: white !important;
            border-radius: 6px !important;
            font-size: 0.9rem !important;
        }
        #payment-modal #modal-customer-details-form {
            display: flex !important;
            flex-direction: column !important;
            gap: 20px !important;
        }
         #payment-modal .form-group-row {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 16px !important;
        }
        #payment-modal .customer-form-card .form-group {
            margin-bottom: 0 !important;
        }
        #payment-modal .customer-form-card .form-label {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
            margin-bottom: 8px !important;
        }
                #payment-modal .customer-form-card .form-label.required::after {
            content: '*' !important;
            color: var(--error-color) !important;
            font-size: 1rem !important;
            margin-left: 2px !important;
        }

        #payment-modal #step3-payment-methods .payment-method-amount {
            font-size: 1rem !important;
            color: var(--primary-color) !important;
            font-weight: 700 !important;
        }
        #payment-modal .customer-form-card .form-input {
            width: 100% !important;
            padding: 12px 14px !important;
            border: 2px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 0.95rem !important;
            color: var(--text-primary) !important;
            transition: all 0.3s ease !important;
            background: #fafbfc !important;
        }
        #payment-modal .customer-form-card .form-input:focus {
            outline: none !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 4px rgba(32, 51, 99, 0.08) !important;
            background: white !important;
        }
        #payment-modal .customer-form-card .form-input::placeholder {
            color: #94a3b8 !important;
            font-size: 0.9rem !important;
        }
        #payment-modal .customer-form-card textarea.form-input {
            resize: vertical !important;
            min-height: 100px !important;
            font-family: inherit !important;
            line-height: 1.6 !important;
        }
         #payment-modal .customer-form-card .form-input:invalid:not(:placeholder-shown) {
            border-color: var(--error-color) !important;
            background: #fef2f2 !important;
        }
        #payment-modal .customer-form-card .form-input:valid:not(:placeholder-shown) {
            border-color: var(--success-color) !important;
        }
         #step-3 .step-actions {
            margin-top: 32px !important;
            padding-top: 24px !important;
            border-top: 2px solid var(--border-color) !important;
        }

        #step-3 .step-btn.confirm {
            background: linear-gradient(135deg, var(--success-color), #059669) !important;
            color: white !important;
            font-size: 1rem !important;
            padding: 14px 32px !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
            transition: all 0.3s ease !important;
        }

        #step-3 .step-btn.confirm:hover:not(:disabled) {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4) !important;
        }

        #step-3 .step-btn.confirm i {
            font-size: 1.1rem !important;
        }
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #tables-config-modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 1100 !important; 
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            backdrop-filter: blur(6px) !important;
            background: rgba(0, 0, 0, 0.5) !important;
            animation: modalFadeIn 0.3s ease-out !important;
        }

        #tables-config-modal.show {
            display: flex !important;
        }

        .tables-config-container {
        position: relative !important;
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3) !important;
        max-width: 700px !important;
        width: 90% !important;
        max-height: 85vh !important;
        overflow: hidden !important;
        z-index: 1101 !important;
        animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        .table-state-badge {
        display: inline-block !important;
        padding: 4px 12px !important;
        border-radius: 12px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        }
        .table-state-badge.disponible {
        background: #d1fae5 !important;
        color: #065f46 !important;
        }
        .table-state-badge.ocupada {
        background: #fee2e2 !important;
        color: #991b1b !important;
        }
        .table-state-badge.reservada {
        background: #fef3c7 !important;
        color: #92400e !important;
        }   
        .table-state-badge.no-disponible {
        background: #f1f5f9 !important;
        color: #475569 !important;
        }
        .table-actions {
        display: flex !important;
        gap: 12px !important;
        align-items: center !important;
        }

        .table-action-btn {
        background: none !important;
        border: none !important;
        cursor: pointer !important;
        padding: 6px 10px !important;
        border-radius: 6px !important;
        transition: all 0.2s ease !important;
        font-size: 0.85rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        }

        .table-action-btn.edit {
        color: #203363 !important;
        }

        .table-action-btn.edit:hover {
        background: #f0f9ff !important;
        }

        .table-action-btn.delete {
        color: #dc2626 !important;
        }

        .table-action-btn.delete:hover {
        background: #fee2e2 !important;
        }

     .tables-config-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: var(--primary-color);
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        
         .tables-config-header h2 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tables-config-close {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tables-config-close:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.05) !important;
        }

         .tables-config-content {
            padding: 24px;
            max-height: calc(85vh - 80px);
            overflow-y: auto;
        }
        .tables-config-content::-webkit-scrollbar {
            width: 6px !important;
        }

        .tables-config-content::-webkit-scrollbar-track {
            background: var(--background-gray);
        }

        .tables-config-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .toggle-container {
            background: var(--background-light);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .toggle-container.active {
            background: #f0f9ff !important;
            border-color: #203363 !important;
        }
        .toggle-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
         .toggle-info {
            flex: 1 !important;
        }
       .toggle-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .toggle-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

         .toggle-switch {
            position: relative;
            width: 56px;
            height: 28px;
            background: #cbd5e1;
            border-radius: 28px;
            transition: background 0.3s ease;
            cursor: pointer;
        }


        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
         .toggle-input:checked + .toggle-switch {
            background: var(--primary-color);
        }

        .toggle-input:checked + .toggle-switch::after {
            transform: translateX(28px);
        }

        .toggle-input {
            display: none !important;
        }
       .tables-section {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 20px;
            margin-bottom: 20px;
            display: none;
        }
       

        .tables-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border-color);
        }
        .tables-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
         .state-badge {
            background: #f0f9ff;
            color: var(--primary-color);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .tables-table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .tables-table-container table {
            width: 100%;
            border-collapse: collapse;
        }
         .tables-table-container thead {
            background: var(--primary-color);
            color: white;
        }
        .tables-table-container th {
            padding: 12px 16px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .tables-table-container tbody {
            max-height: 350px;
            overflow-y: auto;
        }
        .tables-table-container td {
            padding: 12px 16px;
            color: var(--primary-color);
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-color);
        }
        .tables-table-container tr:hover {
            background: var(--background-light);
        }
        .tables-table-container tr:last-child td {
            border-bottom: none;
        }

        .tables-table-container::-webkit-scrollbar {
        width: 6px !important;
        }

        .tables-table-container::-webkit-scrollbar-track {
        background: #f1f5f9 !important;
        border-radius: 3px !important;
        }

        .tables-table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 3px !important;
        }
        #tables-tbody tr {
        border-bottom: 1px solid #e2e8f0 !important;
        transition: background 0.2s ease !important;
        }
        #tables-tbody td {
            padding: 12px 16px !important;
            color: #203363 !important;
            font-size: 0.9rem !important;
        }

        #tables-tbody tr:hover {
            background: #f8fafc !important;
        }

        #tables-tbody tr:last-child {
            border-bottom: none !important;
        }

         .bulk-actions {
            background: #fef3c7 !important;
            border: 1px solid #fde68a !important;
            border-radius: 8px !important;
            padding: 16px !important;
            margin-bottom: 20px !important;
        }
         .bulk-actions-title {
            font-weight: 600 !important;
            color: #92400e !important;
            margin-bottom: 12px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-size: 0.95rem !important;
        }
          .bulk-select-container {
            display: flex !important;
            gap: 8px !important;
        }
        
        .bulk-select {
            flex: 1 !important;
            padding: 8px 12px !important;
            border: 1px solid #fbbf24 !important;
            border-radius: 6px !important;
            background: white !important;
            font-size: 0.9rem !important;
            color: #92400e !important;
            cursor: pointer !important;
        }
        .bulk-select:focus {
            outline: none !important;
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.1) !important;
        }
        
        .bulk-apply-btn:hover {
            background: #d97706 !important;
            transform: translateY(-1px) !important;
        }
        .bulk-apply-btn:disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
         /* Lista de mesas */
        .tables-list {
            max-height: 300px !important;
            overflow-y: auto !important;
            padding-right: 8px !important;
        }

        .tables-list::-webkit-scrollbar {
            width: 6px !important;
        }

        .tables-list::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-radius: 3px !important;
        }

        .tables-list::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 3px !important;
        }
        .table-item {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 12px 16px !important;
            margin-bottom: 8px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
        }
        .table-item:hover {
            border-color: #203363 !important;
            background: #f0f9ff !important;
        }
        .table-item-info {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            flex: 1 !important;
        }
        .table-number {
            font-weight: 600 !important;
            color: #203363 !important;
            font-size: 0.95rem !important;
        }
         .table-state-select {
            padding: 6px 10px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 0.85rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }
         .table-state-select.changed {
            border-color: #f59e0b !important;
            background: #fef3c7 !important;
        }
         .empty-state {
            text-align: center !important;
            padding: 40px 20px !important;
            color: #64748b !important;
        }
        .empty-state i {
            font-size: 3rem !important;
            color: #cbd5e1 !important;
            margin-bottom: 12px !important;
        }

        .empty-state p {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
        }

        .empty-state small {
            font-size: 0.85rem !important;
            color: #94a3b8 !important;
        }

        .tables-section.show {
            display: block !important;
            animation: slideDown 0.3s ease-out !important;
        }
        .info-box {
            background: #fef3c7 !important;
            border-left: 4px solid #f59e0b !important;
            padding: 16px !important;
            border-radius: 8px !important;
            margin-bottom: 20px !important;
            display: none !important;
        }

        .info-box.show {
            display: block !important;
            animation: slideDown 0.3s ease-out !important;
        }

        .info-box-title {
            font-weight: 600 !important;
            color: #92400e !important;
            margin-bottom: 8px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .info-box-text {
            font-size: 0.875rem !important;
            color: #b45309 !important;
            line-height: 1.5 !important;
        }
        .action-buttons {
            display: flex !important;
            gap: 12px !important;
            margin-top: 24px !important;
        }
        .btn {
            flex: 1 !important;
            padding: 12px 20px !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
        }
        .btn-cancel {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }
         .btn-cancel:hover {
            background: #e2e8f0 !important;
        }
         .btn-save {
            background: #203363 !important;
            color: white !important;
        }
         .btn-save:hover {
            background: #2d437c !important;
            transform: translateY(-1px) !important;
        }
        .btn-save:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
        .success-message {
            background: #d1fae5 !important;
            border: 1px solid #6ee7b7 !important;
            color: #065f46 !important;
            padding: 12px 16px !important;
            border-radius: 8px !important;
            margin-bottom: 16px !important;
            display: none !important;
            align-items: center !important;
            gap: 10px !important;
            animation: slideDown 0.3s ease-out !important;
        }

        .success-message.show {
            display: flex !important;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-loading {
            pointer-events: none !important;
            opacity: 0.7 !important;
        }
        .spinner {
            animation: spin 1s linear infinite !important;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
           @media (max-width: 640px) {
            .tables-config-container {
                width: 95% !important;
                margin: 10px !important;
            }

            .tables-config-header h2 {
                font-size: 1.2rem !important;
            }

            .action-buttons {
                flex-direction: column !important;
            }
             .bulk-select-container {
                flex-direction: column !important;
            }
        }
        :root {
            --primary-color: #203363;
            --primary-hover: #2d437c;
            --secondary-color: #6380a6;
            --tertiary-color: #a4b6ce;
            --background-light: #f8fafc;
            --background-gray: #f1f5f9;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
        }

        .tables-config-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1100;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
            background: rgba(0, 0, 0, 0.5);
            animation: modalFadeIn 0.3s ease-out;
        }
        .tables-config-modal.show {
            display: flex;
        }
        .tables-config-container {
            position: relative;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            width: 90%;
            max-height: 85vh;
            overflow: hidden;
            z-index: 1101;
            animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
         
        #payment-modal * {
            box-sizing: border-box !important;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        #payment-modal.payment-modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 1000 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            backdrop-filter: blur(4px) !important;
            animation: modalFadeIn 0.3s ease-out !important;
        }

        #payment-modal.payment-modal.hidden {
            display: none !important;
        }

       @keyframes modalFadeIn {
            from { opacity: 0; backdrop-filter: blur(0px); }
            to { opacity: 1; backdrop-filter: blur(6px); }
        }


        #payment-modal .payment-modal-overlay {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(32, 51, 99, 0.3) !important;
        }

        #payment-modal .payment-modal-container {
            position: relative !important;
            background: white !important;
            border-radius: var(--border-radius-lg) !important;
            box-shadow: var(--shadow-heavy) !important;
            max-width: 850px !important;
            width: 90% !important;
            max-height: 90vh !important;
            overflow: hidden !important;
            z-index: 1001 !important;
            animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }

        @keyframes modalSlideIn {
            from { transform: translateY(-30px) scale(0.98); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        #payment-modal .payment-modal-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 20px 24px !important;
            background: var(--primary-color) !important;
            color: white !important;
            position: relative !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        #payment-modal .payment-modal-header h2 {
            margin: 0 !important;
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: white !important;
        }

        #payment-modal .payment-modal-close {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            font-size: 1.1rem !important;
            cursor: pointer !important;
            color: white !important;
            padding: 6px !important;
            border-radius: var(--border-radius-sm) !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px !important;
            height: 32px !important;
        }

        #payment-modal .payment-modal-close:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.05) !important;
        }

        #payment-modal .payment-modal-content {
            padding: 24px !important;
            max-height: calc(90vh - 120px) !important;
            overflow-y: auto !important;
            background: var(--background-light) !important;
        }

        #payment-modal .payment-modal-content::-webkit-scrollbar {
            width: 6px !important;
        }

        #payment-modal .payment-modal-content::-webkit-scrollbar-track {
            background: var(--background-gray) !important;
            border-radius: 3px !important;
        }

        #payment-modal .payment-modal-content::-webkit-scrollbar-thumb {
            background: var(--tertiary-color) !important;
            border-radius: 3px !important;
        }

        #payment-modal .payment-modal-content::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color) !important;
        }

        #payment-modal .step-navigation {
            display: flex !important;
            margin-bottom: 24px !important;
            border-bottom: 1px solid var(--border-color) !important;

        }

        #payment-modal .step-item {
            flex: 1 !important;
            text-align: center !important;
            padding: 12px 0 !important;
            cursor: pointer !important;
            position: relative !important;
            transition: all 0.3s ease !important;
            font-weight: 500 !important;
            color: var(--text-secondary) !important;
        }

        #payment-modal .step-item.active {
            color: var(--primary-color) !important;
            font-weight: 600 !important;
        }

        #payment-modal .step-item.active::after {
            content: '' !important;
            position: absolute !important;
            bottom: -1px !important;
            left: 0 !important;
            width: 100% !important;
            height: 3px !important;
            background: var(--primary-color) !important;
            border-radius: 3px 3px 0 0 !important;
        }

        #payment-modal .step-item.completed {
            color: var(--success-color) !important;
        }

        #payment-modal .step-content {
            display: none !important;
        }

        #payment-modal .step-content.active {
            display: block !important;
        }

        /* Paso 1: Tipo de Pedido y Mesa */
        #payment-modal .order-type-section {
            background: white !important;
            border-radius: var(--border-radius-md) !important;
            border: 1px solid var(--border-color) !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            box-shadow: var(--shadow-light) !important;
        }

        #payment-modal .order-type-section h3 {
            margin: 0 0 16px 0 !important;
            font-size: 1.2rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        #payment-modal .order-type-section h3::before {
            content: '🍽️' !important;
            font-size: 1.1rem !important;
        }

        #payment-modal .order-type-buttons {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: 12px !important;
            margin-bottom: 20px !important;
        }

        #payment-modal .order-type-btn {
            padding: 12px 16px !important;
            border-radius: var(--border-radius-md) !important;
            font-weight: 500 !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease !important;
            border: 1px solid var(--border-color) !important;
            background: white !important;
            color: var(--text-primary) !important;
            text-align: center !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
        }

        #payment-modal .order-type-btn:hover {
            border-color: var(--primary-color) !important;
            transform: translateY(-1px) !important;
        }

        #payment-modal .order-type-btn.selected {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        #payment-modal .table-selection {
            background: white !important;
            border-radius: var(--border-radius-md) !important;
            border: 1px solid var(--border-color) !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            box-shadow: var(--shadow-light) !important;
            display: block !important;
        }

        #payment-modal .table-selection.hidden {
            display: none !important;
        }

        #payment-modal .table-selection h4 {
            margin: 0 0 12px 0 !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
        }

        #payment-modal #table-loading,
        #payment-modal #table-error {
            font-size: 0.9rem !important;
            margin: 12px 0 !important;
            padding: 12px !important;
            border-radius: var(--border-radius-md) !important;
        }

        #payment-modal #table-loading {
            background: var(--background-light) !important;
            color: var(--text-secondary) !important;
            border: 1px solid var(--border-color) !important;
        }

        #payment-modal #table-error {
            background: #fee2e2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
        }

        #payment-modal #table-error button {
            color: #dc2626 !important;
            text-decoration: underline !important;
            background: none !important;
            border: none !important;
            cursor: pointer !important;
            font-size: inherit !important;
            padding: 0 !important;
        }

        #payment-modal #table-error button:hover {
            text-decoration: none !important;
        }

        #payment-modal .table-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)) !important;
            gap: 10px !important;
        }

        #payment-modal .table-btn {
            padding: 12px 8px !important;
            border-radius: var(--border-radius-md) !important;
            border: 1px solid var(--border-color) !important;
            background: white !important;
            color: var(--text-primary) !important;
            text-align: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            font-size: 0.9rem !important;
        }

        #payment-modal .table-btn:hover:not(:disabled) {
            border-color: var(--primary-color) !important;
            transform: translateY(-1px) !important;
        }

        #payment-modal .table-btn.selected {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        #payment-modal .table-btn.occupied {
            background: #fee2e2 !important;
            color: #dc2626 !important;
            border-color: #fecaca !important;
            cursor: not-allowed !important;
        }

        #payment-modal .table-btn.reserved {
            background: #fef3c7 !important;
            color: #d97706 !important;
            border-color: #fde68a !important;
            cursor: not-allowed !important;
        }

        #payment-modal .table-btn:disabled {
            opacity: 0.7 !important;
            transform: none !important;
        }

        #payment-modal .payment-summary {
            background: white !important;
            border-radius: var(--border-radius-md) !important;
            border: 1px solid var(--border-color) !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            box-shadow: var(--shadow-light) !important;
        }

        #payment-modal .payment-summary h3 {
            margin: 0 0 16px 0 !important;
            font-size: 1.2rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
        }

        #payment-modal .total-display {
            font-size: 1.3rem !important;
            font-weight: 700 !important;
            color: var(--primary-color) !important;
            text-align: center !important;
            padding: 12px !important;
            background: #f0f4f8 !important;
            border-radius: var(--border-radius-md) !important;
            margin-bottom: 16px !important;
        }

        #payment-modal .add-payment-btn {
            width: 100% !important;
            padding: 12px 16px !important;
            background: var(--success-color) !important;
            color: white !important;
            border: none !important;
            border-radius: var(--border-radius-md) !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            cursor: pointer !important;
            margin-bottom: 16px !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
        }

        #payment-modal .add-payment-btn:hover {
            background: #059669 !important;
            transform: translateY(-2px) !important;
        }

        #payment-modal .payment-rows-container {
            max-height: 300px !important;
            overflow-y: auto !important;
            padding-right: 8px !important;
        }

        #payment-modal .payment-row {
            background: white !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius-md) !important;
            padding: 16px !important;
            margin-bottom: 12px !important;
            box-shadow: var(--shadow-light) !important;
            position: relative !important;
        }

        #payment-modal .payment-row-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 12px !important;
            padding-bottom: 8px !important;
            border-bottom: 1px solid var(--border-color) !important;
        }

        #payment-modal .payment-row-remove {
            background: var(--error-color) !important;
            color: white !important;
            border: none !important;
            border-radius: 50% !important;
            width: 24px !important;
            height: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            font-size: 0.8rem !important;
        }

        #payment-modal .payment-form {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 12px !important;
        }

        #payment-modal .form-group {
            margin-bottom: 12px !important;
        }

        #payment-modal .form-group.full-width {
            grid-column: 1 / -1 !important;
        }

        #payment-modal .form-label {
            display: block !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin-bottom: 4px !important;
        }

        #payment-modal .form-select,
        #payment-modal .form-input {
            width: 100% !important;
            padding: 10px 12px !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius-sm) !important;
            font-size: 0.9rem !important;
            transition: all 0.2s ease !important;
        }

        #payment-modal .form-select:focus,
        #payment-modal .form-input:focus {
            outline: none !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 2px rgba(32, 51, 99, 0.1) !important;
        }

        #payment-modal .payment-amounts {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: 12px !important;
            margin-top: 12px !important;
        }

        /* Navegación entre pasos */
        #payment-modal .step-actions {
            display: flex !important;
            justify-content: space-between !important;
            margin-top: 24px !important;
            padding-top: 20px !important;
            border-top: 1px solid var(--border-color) !important;
        }

        #payment-modal .step-btn {
            padding: 12px 24px !important;
            border: none !important;
            border-radius: var(--border-radius-md) !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        #payment-modal .step-btn.prev {
            background: var(--background-gray) !important;
            color: var(--text-primary) !important;
        }

        #payment-modal .step-btn.next {
            background: var(--primary-color) !important;
            color: white !important;
        }

        #payment-modal .step-btn.confirm {
            background: var(--success-color) !important;
            color: white !important;
        }

        #payment-modal .step-btn:disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }

        #payment-modal .fas, 
        #payment-modal .fa, 
        #payment-modal .far, 
        #payment-modal .fab {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900 !important;
            display: inline-block !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            line-height: 1 !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            #payment-modal .form-group-row {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }
             #payment-modal .customer-details-section > h3 {
                font-size: 1.2rem !important;
            }
            #payment-modal .order-summary-card,
            #payment-modal .payment-details-card,
            #payment-modal .customer-form-card {
                padding: 20px !important;
            }
            #payment-modal .summary-total {
                font-size: 1.1rem !important;
            }
             #payment-modal .summary-total span {
                font-size: 1.2rem !important;
            }

            #step-3 .step-btn.confirm {
                padding: 12px 24px !important;
                font-size: 0.95rem !important;
            }
            #payment-modal .payment-modal-container {
                width: 95% !important;
                max-width: none !important;
                margin: 10px !important;
                max-height: 95vh !important;
            }
            
            #payment-modal .payment-modal-header {
                padding: 16px 20px !important;
            }
            
            #payment-modal .payment-modal-header h2 {
                font-size: 1.3rem !important;
            }
            
            #payment-modal .payment-modal-content {
                padding: 20px !important;
                max-height: calc(95vh - 100px) !important;
            }
            
            #payment-modal .order-type-buttons {
                grid-template-columns: 1fr !important;
                gap: 8px !important;
            }
            
            #payment-modal .table-grid {
                grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)) !important;
                gap: 8px !important;
            }
            
            #payment-modal .payment-form {
                grid-template-columns: 1fr !important;
            }
            
            #payment-modal .payment-amounts {
                grid-template-columns: 1fr !important;
            }
            
            #payment-modal .step-actions {
                flex-direction: column !important;
                gap: 12px !important;
            }
            
            #payment-modal .step-btn {
                width: 100% !important;
            }
        }

        @media (max-width: 480px) {
             #payment-modal .customer-details-section > h3 {
                font-size: 1.1rem !important;
                padding-bottom: 12px !important;
            }
            
            #payment-modal .order-summary-card h4,
            #payment-modal .payment-details-card h4,
            #payment-modal .customer-form-card h4 {
                font-size: 1rem !important;
            }

            #payment-modal .customer-form-card .form-input {
                padding: 10px 12px !important;
                font-size: 0.9rem !important;
            }

            #payment-modal #step3-order-summary .summary-item {
                padding: 10px 12px !important;
            }

            #payment-modal .summary-total {
                font-size: 1rem !important;
                padding: 14px 16px !important;
            }
            #payment-modal .payment-modal-container {
                width: 98% !important;
                margin: 5px !important;
                border-radius: var(--border-radius-md) !important;
                max-height: 98vh !important;
            }
            
            #payment-modal .payment-modal-header {
                padding: 14px 16px !important;
            }
            
            #payment-modal .payment-modal-header h2 {
                font-size: 1.2rem !important;
            }
            
            #payment-modal .payment-modal-content {
                padding: 16px !important;
                max-height: calc(98vh - 90px) !important;
            }
            
            #payment-modal .order-type-section,
            #payment-modal .table-selection,
            #payment-modal .payment-summary {
                padding: 16px !important;
            }
            
            #payment-modal .table-btn {
                padding: 10px 6px !important;
                font-size: 0.8rem !important;
            }
        }

        .delivery-selection {
            background: white !important;
            border-radius: var(--border-radius-md) !important;
            border: 1px solid var(--border-color) !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            box-shadow: var(--shadow-light) !important;
        }

        .delivery-selection.hidden {
            display: none !important;
        }

        .delivery-selection h4 {
            margin: 0 0 12px 0 !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: var(--primary-color) !important;
        }

        #modal-table-selection.hidden,
        #modal-delivery-selection.hidden,
        #modal-pickup-notes.hidden {
            display: none !important;
        }

        /* Mostrar secciones visibles */
        #modal-table-selection:not(.hidden),
        #modal-delivery-selection:not(.hidden),
        #modal-pickup-notes:not(.hidden) {
            display: block !important;
        }

        /* Estilos para el select de delivery y textarea */
        #modal-delivery-service,
        #modal-pickup-notes-text {
            width: 100% !important;
            padding: 12px !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius-md) !important;
            font-size: 0.95rem !important;
            background: white !important;
            transition: border-color 0.2s ease !important;
        }

        #modal-delivery-service:focus,
        #modal-pickup-notes-text:focus {
            outline: none !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 2px rgba(32, 51, 99, 0.1) !important;
        }

        /* Estilos para opciones del select */
        #modal-delivery-service option {
            padding: 8px !important;
            font-size: 0.95rem !important;
        }

        /* Mensaje cuando no hay servicio seleccionado */
        #modal-delivery-service:invalid {
            color: var(--text-secondary) !important;
        }

        #modal-delivery-service option[value=""] {
            color: var(--text-secondary) !important;
            font-style: italic !important;
        }

        /* Estilos para estados de carga y error en delivery */
        .delivery-loading {
            text-align: center !important;
            padding: 16px !important;
            color: var(--text-secondary) !important;
            font-size: 0.9rem !important;
        }

        .delivery-error {
            text-align: center !important;
            padding: 16px !important;
            color: var(--error-color) !important;
            font-size: 0.9rem !important;
            background: #fee2e2 !important;
            border-radius: var(--border-radius-md) !important;
            margin-top: 8px !important;
        }

        /* Animaciones para transiciones suaves */
        #modal-table-selection,
        #modal-delivery-selection {
            transition: all 0.3s ease !important;
        }

        /* Responsive para el select de delivery */
        @media (max-width: 768px) {
            #modal-delivery-service {
                font-size: 0.9rem !important;
                padding: 10px !important;
            }
        }

        /* Validación visual para delivery requerido */
         #modal-delivery-service.required:invalid {
            border-color: var(--error-color) !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1) !important;
        }
        .stat-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 8px;
}

.stat-dot.green {
    background: #10b981;
}

.stat-dot.red {
    background: #ef4444;
}

.stat-dot.yellow {
    background: #f59e0b;
}

.stat-dot.gray {
    background: #6b7280;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.stat-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: var(--text-primary);
}

.warning-box {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.warning-box-title {
    font-weight: 600;
    color: #92400e;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.warning-box-text {
    font-size: 0.875rem;
    color: #b45309;
    line-height: 1.5;
}

.stats-container {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 8px;
}

.form-input,
.form-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(32, 51, 99, 0.1);
}

.bulk-apply-btn {
    padding: 10px 16px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.bulk-apply-btn:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
}

.bulk-apply-btn.warning {
    background: #f59e0b;
}

.bulk-apply-btn.warning:hover {
    background: #d97706;
}

@media (max-width: 640px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
#tables-disabled-message {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
/* ✅ Asegurar que el mensaje y el botón siempre sean visibles cuando la sección está visible */
/* Estilos para el botón de guardar cliente actual */
#save-current-client-btn {
    background: #10b981;
    color: white;
}

#save-current-client-btn:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

#save-current-client-btn:disabled {
    background: #6b7280;
    cursor: not-allowed;
    transform: none;
}

/* Indicador de cliente guardado */
#client-saved-indicator {
    animation: slideInDown 0.4s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Estilo para campos opcionales */
.form-label::after {
    content: '';
}

.form-label.required::after {
    content: ' *';
    color: #ef4444;
}

/* Secciones del formulario */
#modal-customer-details-form > div[style*="background: #f8fafc"] {
    transition: all 0.2s ease;
}

#modal-customer-details-form > div[style*="background: #f8fafc"]:hover {
    background: #f1f5f9 !important;
}
/* ============================================
   ESTILOS PARA SECCIONES DESPLEGABLES
   ============================================ */

.collapsible-section {
    margin-bottom: 16px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
}

.collapsible-section:hover {
    border-color: #cbd5e1 !important;
}

.collapsible-header {
    width: 100% !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 14px 16px !important;
    background: #f8fafc !important;
    border: none !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    text-align: left !important;
}

.collapsible-header:hover {
    background: #f1f5f9 !important;
}

.collapsible-header.active {
    background: #e0f2fe !important;
    border-bottom: 1px solid #e5e7eb !important;
}

.collapsible-title {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    color: #203363 !important;
}

.collapsible-title i {
    color: #6380a6 !important;
    font-size: 1rem !important;
}

.collapsible-icon {
    color: #6b7280 !important;
    font-size: 0.85rem !important;
    transition: transform 0.3s ease !important;
}

.collapsible-icon.rotated {
    transform: rotate(180deg) !important;
}

.collapsible-content {
    max-height: 0 !important;
    overflow: hidden !important;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: white !important;
}

.collapsible-content.open {
    max-height: 500px !important;
    padding: 20px 16px !important;
    border-top: 1px solid #e5e7eb !important;
}

/* Animación suave para el contenido */
.collapsible-content > * {
    opacity: 0 !important;
    transform: translateY(-10px) !important;
    transition: opacity 0.3s ease, transform 0.3s ease !important;
}

.collapsible-content.open > * {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Responsive para secciones desplegables */
@media (max-width: 768px) {
    .collapsible-header {
        padding: 12px 14px !important;
    }
    
    .collapsible-title {
        font-size: 0.9rem !important;
    }
    
    .collapsible-content.open {
        padding: 16px 14px !important;
        max-height: 600px !important;
    }
}

@media (max-width: 480px) {
    .collapsible-title {
        font-size: 0.85rem !important;
    }
    
    .collapsible-title i {
        font-size: 0.9rem !important;
    }
}
    </style>