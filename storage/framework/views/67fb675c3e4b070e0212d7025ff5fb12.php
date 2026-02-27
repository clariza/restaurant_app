<?php $__env->startSection('content'); ?>
<style>
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 1rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    /* Estilos mejorados para botones */
    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .btn-view {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #2563eb;
    }

    .btn-edit {
        background-color: #10b981;
        color: white;
        border: 1px solid #059669;
    }

    .btn-delete {
        background-color: #ef4444;
        color: white;
        border: 1px solid #dc2626;
    }

    .btn-close {
        background-color: #8b5cf6;
        color: white;
        border: 1px solid #7c3aed;
    }

    .btn-print {
        background-color: #6b7280;
        color: white;
        border: 1px solid #4b5563;
    }

    .btn-excel {
        background-color: #10b981;
        color: white;
        border: 1px solid #059669;
    }

    .btn-pdf {
        background-color: #ef4444;
        color: white;
        border: 1px solid #dc2626;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .reports-section {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .reports-section:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .reports-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .reports-header {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .reports-header i {
        font-size: 1.5rem;
        color: #6b7280;
        background: #f3f4f6;
        padding: 0.625rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .reports-section:hover .reports-header i {
        background: #e5e7eb;
        color: #374151;
    }

    .reports-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
        letter-spacing: -0.01em;
    }


    .reports-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
        line-height: 1.6;
        position: relative;
        z-index: 2;
        padding-left: 3rem;
    }



    .btn-report {
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-report::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-report:hover::before {
        left: 100%;
    }

    .btn-report-excel {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .btn-report-excel:hover {
        background: #059669;
        border-color: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        color: white;
    }

    .btn-report-excel:hover,
    .btn-report-pdf:hover {
        border-color: rgba(255, 255, 255, 0.3);
    }


    .btn-report-pdf {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    .btn-report-pdf:hover {
        background: #dc2626;
        border-color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        color: white;
    }

    .btn-report i {
        font-size: 1rem;
        transition: transform 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .btn-report:hover i {
        transform: scale(1.1);
    }

    .btn-report-alt {
        background: #f9fafb;
        color: #374151;
        border-color: #e5e7eb;
    }

    .btn-report:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-report:active {
        transform: translateY(0);
    }

    .btn-report-alt:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #111827;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .input-group label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .label-hint {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 400;
}
    .input-group input {
        padding: 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    /* Estilos mejorados para el modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        padding: 2rem;
    }

    .modal-overlay.active .modal-container {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.5rem 0;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.75rem;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.2s;
        padding: 0.5rem;
        margin-left: 1rem;
    }

    .modal-close:hover {
        color: #1f2937;
    }

    .modal-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }


    .section-container {
    padding: 1.25rem;
    background: linear-gradient(to bottom, #f8fafc, #ffffff);
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    height: 100%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}


    .denominations-section {
        display: flex;
        flex-direction: column;
    }

    .denomination-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.25rem;
        text-align: center;
    }

    .total-row {
        background-color: #f1f5f9;
        font-weight: 500;
    }

    .table-container {
        overflow-x: auto;
        flex: 1;
    }

    .denominations-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .denominations-table th {
        background-color: #f1f5f9;
        padding: 0.75rem;
        font-weight: 500;
        color: #334155;
    }

    .denominations-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .denominations-table tr:last-child td {
        border-bottom: none;
    }

    .closure-form-section {
        display: flex;
        flex-direction: column;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .modal-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    /* Estilos mejorados para la sección de gastos */
    .expenses-section {
        margin-bottom: 1.5rem;
    }

    .expenses-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .expense-actions {
        flex: 0 0 auto;
        width: 40px;
    }

    .expenses-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .expense-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
        width: 100%;
    }

    .expense-field {
        flex: 1;
        min-width: 0;
    }

    .expense-input-container {
        flex: 1;
        min-width: 0;
    }

    .expense-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-actions {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }

    .save-btn {
        background-color: #10b981;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .save-btn:hover {
        background-color: #059669;
    }

    .expense-input:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 2px #bfdbfe;
    }

    .add-expense-btn {
        background-color: #e2e8f0;
        color: #475569;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        cursor: pointer;
        font-size: 0.875rem;
        transition: background-color 0.2s;
    }

    .add-expense-btn:hover {
        background-color: #cbd5e1;
    }

    .remove-expense-btn {
        background-color: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 4px;
        padding: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        width: 36px;
        transition: background-color 0.2s;
    }

    .remove-expense-btn:hover {
        background-color: #fecaca;
    }

    .denomination-input {
        width: 70px;
        text-align: center;
    }

    .expense-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        align-items: center;
    }

    .expense-input,
    .denomination-input {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        height: calc(1.5em + 0.5rem + 2px);
    }

    .denominations-section .table-container {
        margin-top: 0.5rem;
    }

    .denominations-table {
        width: 100%;
        font-size: 0.875rem;
        border-collapse: separate;
        border-spacing: 0;
    }

    .denominations-table th {
        background-color: #f8f9fa;
        padding: 0.375rem 0.5rem;
        font-weight: 500;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }

    .denominations-table td {
        padding: 0.375rem 0.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #dee2e6;
    }

    .denomination-input {
        width: 70px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        text-align: center;
        height: calc(1.5em + 0.5rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.2rem;
    }

    .denominations-table .text-right {
        text-align: right;
    }

    .total-row {
        background-color: #f8f9fa;
        font-weight: 500;
    }

    .closure-form-section .input-group {
        margin-bottom: 0.75rem;
    }

    .closure-form-section label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        display: block;
    }
    .form-control {
    width: 100%;
    padding: 0.625rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-control[readonly] {
    background-color: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
    border-style: dashed;
}
.closure-internal-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.95);
    background: white;
    z-index: 10002;
    display: none;
    flex-direction: column;
    border-radius: 0.75rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    width: 95%;
    max-width: 900px;
    max-height: 85vh;
    overflow: hidden;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.closure-internal-modal.active {
    display: flex;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}
.closure-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 10001;
    backdrop-filter: blur(2px);
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.closure-overlay.active {
    display: block !important;
    opacity: 1;
}
.closure-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(to bottom, #f8fafc, #ffffff);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}
.closure-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.closure-title::before {
    content: '💰';
    font-size: 1.75rem;
}
.closure-close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}
.closure-close-btn:hover {
    background: #fee2e2;
    color: #dc2626;
    transform: rotate(90deg);
}
.closure-scroll-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.closure-scroll-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.closure-scroll-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
.closure-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: start;
}
.closure-scroll-content {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    max-height: calc(85vh - 90px);
}
.closure-scroll-content::-webkit-scrollbar {
    width: 8px;
}
    .closure-form-section .form-control {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        height: calc(1.5em + 0.5rem + 2px);
    }

    .closure-form-section .form-control[readonly] {
        background-color: #f8f9fa;
    }

    .form-actions {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }

    .save-btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .denomination-input {
            width: 60px;
        }

        .denominations-table th,
        .denominations-table td {
            padding: 0.25rem 0.375rem;
        }

        .filters-panel {
            padding: 1rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
            gap: 0.875rem;
        }

        .filters-actions {
            flex-direction: column;
            width: 100%;
            gap: 0.625rem;
        }

        .btn-filter,
        .btn-clear {
            width: 100%;
            justify-content: center;
        }


        .reports-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            position: relative;
            z-index: 2;
            padding-left: 3rem;
        }

    }

    @media (max-width: 768px) {
        .reports-section {
            padding: 1.5rem;
        }

        .reports-description,
        .reports-buttons {
            padding-left: 0;
        }

        .reports-buttons {
            flex-direction: column;
            width: 100%;
        }

        .btn-report {
            width: 100%;
            justify-content: center;
        }

        .reports-header h3 {
            font-size: 1rem;
        }

        .reports-description {
            font-size: 0.8125rem;
            margin-bottom: 1rem;
        }
    }


    @media (max-width: 480px) {
        .filters-panel {
            padding: 0.75rem;
            margin-bottom: 1rem;
        }

        .filters-grid {
            gap: 0.75rem;
        }

        .filter-group label {
            font-size: 0.75rem;
        }

        .filter-group select,
        .filter-group input {
            padding: 0.4rem 0.625rem;
            font-size: 0.8125rem;
        }

        .btn-filter,
        .btn-clear {
            padding: 0.625rem 1rem;
            font-size: 0.8125rem;
        }
    }

    .filters-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
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

    .filter-group input:invalid {
        border-color: #f87171;
    }

    .btn-filter.loading {
        position: relative;
        pointer-events: none;
    }

    .btn-filter.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .btn-filter:disabled,
    .btn-clear:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .filter-group label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .filter-group select {
        max-width: 100%;
        overflow: hidden;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        background-color: white;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filters-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .btn-filter,
    .btn-clear {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
        white-space: nowrap;
        text-decoration: none;
        box-sizing: border-box;
    }

    .btn-filter {
        background-color: #3b82f6;
        color: white;
    }

    .btn-filter:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }

    .btn-clear {
        background-color: #6b7280;
        color: white;
    }

    .btn-clear:hover {
        background-color: #4b5563;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(107, 114, 128, 0.3);
    }

    @media (max-width: 1200px) {
        .filters-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .filters-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 1024px) {
        .filters-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .filters-actions {
            justify-content: stretch;
        }

        .btn-filter,
        .btn-clear {
            flex: 1;
            justify-content: center;
        }

        .btn-report {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: 1px solid;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-report::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
        }

        .btn-report:hover::after {
            width: 300px;
            height: 300px;
        }



    }
</style>

<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4 text-[#203363]">Lista de Cierres</h2>

    <!-- Sección de Reportes -->
    <div class="reports-section">
        <div class="reports-header">
            <i class="fas fa-chart-bar"></i>
            <h3>Reportes de Caja Chica</h3>
        </div>
        <p class="reports-description">
            Genere reportes completos de caja chica en formato Excel o PDF. Los filtros aplicados se incluirán en los reportes.
        </p>
        <div class="reports-buttons">
            <a href="<?php echo e(route('petty-cash.export.excel', request()->query())); ?>"
                class="btn-report btn-report-excel"
                title="Descargar reporte en Excel">
                <i class="fas fa-file-excel"></i>
                <span>Exportar a Excel</span>
            </a>
            <a href="<?php echo e(route('petty-cash.export.pdf', request()->query())); ?>"
                class="btn-report btn-report-pdf"
                target="_blank"
                title="Descargar reporte en PDF">
                <i class="fas fa-file-pdf"></i>
                <span>Exportar a PDF</span>
            </a>
        </div>
    </div>

    <!-- Panel de Filtros -->
    <div class="filters-panel">
        <form method="GET" action="<?php echo e(route('petty-cash.index')); ?>" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="user_id">Cajero</label>
                    <select id="user_id" name="user_id">
                        <option value="">Todos los cajeros</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>"
                            <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                            <?php echo e($user->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="status">Estado</label>
                    <select id="status" name="status">
                        <option value="">Todos los estados</option>
                        <option value="open" <?php echo e(request('status') == 'open' ? 'selected' : ''); ?>>
                            Abierta
                        </option>
                        <option value="closed" <?php echo e(request('status') == 'closed' ? 'selected' : ''); ?>>
                            Cerrada
                        </option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">Fecha desde</label>
                    <input type="date"
                        id="date_from"
                        name="date_from"
                        value="<?php echo e(request('date_from')); ?>"
                        max="<?php echo e(date('Y-m-d')); ?>">
                </div>

                <div class="filter-group">
                    <label for="date_to">Fecha hasta</label>
                    <input type="date"
                        id="date_to"
                        name="date_to"
                        value="<?php echo e(request('date_to')); ?>"
                        max="<?php echo e(date('Y-m-d')); ?>">
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn-filter" id="btnFilter">
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="<?php echo e(route('petty-cash.index')); ?>" class="btn-clear">
                        <i class="fas fa-times"></i>
                        <span>Limpiar</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Mensajes de alerta -->
    <?php if(session('warning')): ?>
    <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?php echo e(session('warning')); ?></span>
        <button onclick="closeOpenPettyCash()" class="ml-2 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
            Cerrar caja abierta
        </button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?php echo e(session('error')); ?></span>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?php echo e(session('success')); ?></span>
    </div>
    <?php endif; ?>

    <!-- Tabla de cierres -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Fecha</th>
                    <th class="p-2 text-left">Cajero</th>
                    <th class="p-2 text-right">Monto Actual</th>
                    <th class="p-2 text-left">Estado</th>
                    <th class="p-2 text-left">Acciones</th>
                    <th class="p-2 text-left">Reporte</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pettyCashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pettyCash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2 text-left"><?php echo e($pettyCash->date); ?></td>
                    <td class="p-2 text-left"><?php echo e($pettyCash->user->name ?? 'N/A'); ?></td>
                    <td class="p-2 text-right">$<?php echo e(number_format($totalSales - $totalExpenses, 2)); ?></td>
                    <td class="p-2 text-left">
                        <span class="px-2 py-1 rounded-full text-xs 
                            <?php echo e($pettyCash->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); ?>">
                            <?php echo e($pettyCash->status === 'open' ? 'Abierta' : 'Cerrada'); ?>

                        </span>
                    </td>
                    <td class="p-2 text-left space-x-1">
                        <a href="<?php echo e(route('petty-cash.show', $pettyCash)); ?>"
                            class="btn-action btn-view">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="<?php echo e(route('petty-cash.edit', $pettyCash)); ?>"
                            class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="<?php echo e(route('petty-cash.destroy', $pettyCash)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit"
                                class="btn-action btn-delete"
                                onclick="return confirm('¿Estás seguro de eliminar esta caja chica?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                        <?php if($pettyCash->status === 'open'): ?>
                        <button onclick="openModal('<?php echo e($pettyCash->id); ?>')"
                            class="btn-action btn-close">
                            <i class="fas fa-lock"></i> Cerrar
                        </button>
                        <?php endif; ?>
                    </td>
                    <td class="p-2 text-left">
                        <?php if($pettyCash->status === 'closed'): ?>
                        <a href="<?php echo e(route('petty-cash.print', $pettyCash)); ?>"
                            target="_blank"
                            class="btn-action btn-print">
                            <i class="fas fa-print"></i> PDF
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        No se encontraron registros de caja chica
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if($pettyCashes->hasPages()): ?>
    <div class="mt-4">
        <?php echo e($pettyCashes->appends(request()->query())->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- Modal de cierre -->
<div id="modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Cierre de Caja Chica</h3>
            <button onclick="closeModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-content">
            <!-- Sección de Gastos -->
            <div class="expenses-section">
                <div class="expenses-header">
                    <h4 class="font-medium">Registro de Gastos</h4>
                    <button type="button" class="btn btn-secondary btn-sm add-expense-btn" onclick="addExpense()">
                        <i class="fas fa-plus mr-1"></i> Agregar Gasto
                    </button>
                </div>
                <div class="expenses-container" id="expensesContainer">
                    <!-- Las filas de gastos se agregarán dinámicamente -->
                </div>
            </div>

            <!-- Sección de Cierre -->
            <div class="closure-grid">
                <!-- Tabla de denominaciones -->
                <div class="denominations-section">
                    <div class="section-container">
                        <h4 class="section-title">Conteo de Efectivo</h4>
                        <div class="table-container">
                            <table class="denominations-table">
                                <thead>
                                    <tr>
                                        <th class="text-left">Denominación</th>
                                        <th>Cantidad</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = [0.5, 1, 2, 5, 10, 20, 50, 100, 200]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denominacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="text-left">$<?php echo e(number_format($denominacion, 2)); ?></td>
                                        <td>
                                            <input type="number" min="0" class="form-control form-control-sm denomination-input"
                                                data-denominacion="<?php echo e($denominacion); ?>" placeholder="0">
                                        </td>
                                        <td class="text-right">
                                            <span class="subtotal">$0.00</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="total-row">
                                        <td colspan="2" class="text-right">Total Efectivo:</td>
                                        <td class="text-right">
                                            <span id="total">$0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Formulario de cierre -->
                <div class="closure-form-section">
                    <div class="section-container">
                        <h4 class="section-title">Resumen de Cierre</h4>
                        <div class="form-grid">
                            <div class="input-group mb-2">
                                <label for="total-gastos" class="small">Total Gastos</label>
                                <input type="number" id="total-gastos" class="form-control form-control-sm"
                                    value="<?php echo e($totalExpenses); ?>" step="0.01" readonly 
                                    data-gastos-bd="<?php echo e($totalExpenses); ?>">
                            </div>
                            <div class="input-group mb-2">
                                <label for="total-efectivo" class="small">Total Efectivo</label>
                                <input type="number" id="total-efectivo" class="form-control form-control-sm"
                                    value="0" step="0.01" readonly>
                            </div>
                            <div class="input-group mb-2">
                                <label for="ventas-qr" class="small">Ventas QR</label>
                                <input type="number" id="ventas-qr" class="form-control form-control-sm"
                                    value="<?php echo e($totalSalesQR); ?>" step="0.01">
                            </div>
                            <div class="input-group mb-2">
                                <label for="ventas-tarjeta" class="small">Ventas Tarjeta</label>
                                <input type="number" id="ventas-tarjeta" class="form-control form-control-sm"
                                    value="<?php echo e($totalSalesCard); ?>" step="0.01">
                            </div>
                            <div class="form-actions">
                               <button type="button" 
                                    class="btn btn-primary btn-sm save-btn" 
                                    onclick="saveClosure()"
                                    id="btn-save-closure">
                                <i class="fas fa-save mr-1"></i> Guardar Cierre
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para cierre -->
<form id="closureForm" action="<?php echo e(route('petty-cash.save-closure')); ?>" method="POST" class="hidden">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="petty_cash_id" id="petty_cash_id">
    <input type="hidden" name="total_sales_cash" id="total_sales_cash" value="0">
    <input type="hidden" name="total_sales_qr" id="total_sales_qr" value="<?php echo e($totalSalesQR); ?>">
    <input type="hidden" name="total_sales_card" id="total_sales_card" value="<?php echo e($totalSalesCard); ?>">
    <input type="hidden" name="total_expenses" id="total_expenses" value="<?php echo e($totalExpenses); ?>">
</form>

<!-- ========================================
     SCRIPTS - IMPORTANTE: Solo configuración
     ======================================== -->
<script src="<?php echo e(asset('js/petty-cash-index.js')); ?>"></script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\laravel_clary\restaurant_app\resources\views/petty_cash/index.blade.php ENDPATH**/ ?>