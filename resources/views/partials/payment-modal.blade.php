<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal de Pagos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
             /* Estilos para el modal de configuración de mesas */
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
            max-width: 500px !important;
            width: 90% !important;
            max-height: 85vh !important;
            overflow: hidden !important;
            z-index: 1101 !important;
            animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }

        .tables-config-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 20px 24px !important;
            background: #203363 !important;
            color: white !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .tables-config-header h2 {
            margin: 0 !important;
            font-size: 1.4rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .tables-config-close {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            width: 32px !important;
            height: 32px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .tables-config-close:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: scale(1.05) !important;
        }

        .tables-config-content {
            padding: 24px !important;
            max-height: calc(85vh - 80px) !important;
            overflow-y: auto !important;
        }
        .tables-config-content::-webkit-scrollbar {
            width: 6px !important;
        }

        .tables-config-content::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
        }

        .tables-config-content::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 3px !important;
        }

        .toggle-container {
            background: #f8fafc !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 20px !important;
            margin-bottom: 24px !important;
            transition: all 0.3s ease !important;
        }

        .toggle-container.active {
            background: #f0f9ff !important;
            border-color: #203363 !important;
        }
        .toggle-label {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            cursor: pointer !important;
        }
         .toggle-info {
            flex: 1 !important;
        }
        .toggle-title {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #203363 !important;
            margin-bottom: 4px !important;
        }

        .toggle-description {
            font-size: 0.875rem !important;
            color: #64748b !important;
        }

        .toggle-switch {
            position: relative !important;
            width: 56px !important;
            height: 28px !important;
            background: #cbd5e1 !important;
            border-radius: 28px !important;
            transition: background 0.3s ease !important;
            cursor: pointer !important;
        }

        .toggle-switch::after {
            content: '' !important;
            position: absolute !important;
            top: 2px !important;
            left: 2px !important;
            width: 24px !important;
            height: 24px !important;
            background: white !important;
            border-radius: 50% !important;
            transition: transform 0.3s ease !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        }
         .toggle-input:checked + .toggle-switch {
            background: #203363 !important;
        }

        .toggle-input:checked + .toggle-switch::after {
            transform: translateX(28px) !important;
        }

        .toggle-input {
            display: none !important;
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
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

            .tables-config-content {
                padding: 20px !important;
            }

            .action-buttons {
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
            --shadow-light: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-heavy: 0 10px 25px rgba(0, 0, 0, 0.15);
            --border-radius-sm: 6px;
            --border-radius-md: 8px;
            --border-radius-lg: 12px;
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
            to { opacity: 1; backdrop-filter: blur(4px); }
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
    </style>
</head>
<body>

    {{-- <button onclick="openPaymentModal()" style="margin: 20px; padding: 10px 20px; background: #203363; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Abrir Modal de Pagos
    </button> --}}
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

                <!-- Acciones masivas -->
                <div class="bulk-actions">
                    <div class="bulk-actions-title">
                        <i class="fas fa-sync-alt"></i>
                        Cambiar Estado de Todas las Mesas
                    </div>
                    <div class="bulk-select-container">
                        <select id="bulk-state-select" class="bulk-select">
                            <option value="">Seleccione un estado</option>
                            <option value="Disponible">✓ Disponible</option>
                            <option value="Ocupada">● Ocupada</option>
                            <option value="Reservada">◐ Reservada</option>
                            <option value="No Disponible">✗ No Disponible</option>
                        </select>
                        <button class="bulk-apply-btn" onclick="applyBulkStateChange()">
                            <i class="fas fa-check"></i>
                            Aplicar
                        </button>
                    </div>
                </div>

                <!-- Lista de mesas -->
                <div id="tables-list" class="tables-list">
                    <!-- Las mesas se cargarán dinámicamente aquí -->
                </div>

                <!-- Estado vacío -->
                <div id="empty-state" class="empty-state" style="display: none;">
                    <i class="fas fa-table"></i>
                    <p>No hay mesas configuradas</p>
                    <small>Las mesas se crean desde la sección de configuración principal</small>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="action-buttons">
                <button class="btn btn-cancel" onclick="closeTablesConfigModal()">
                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
                <button class="btn btn-save" id="save-tables-config" onclick="saveAllChanges()">
                    <i class="fas fa-save"></i>
                    Guardar Todos los Cambios
                </button>
            </div>
        </div>
    </div>
</div>


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
                <!-- Navegación por pasos -->
                <div class="step-navigation">
                    <div class="step-item active" data-step="1">Tipo de Pedido</div>
                    <div class="step-item" data-step="2">Método de Pago</div>
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
                                <i class="fas fa-shopping-bag"></i>Recojo por Delivery
                            </button>
                            <button class="order-type-btn" data-type="recoger">
                                <i class="fas fa-box"></i>Recoger
                            </button>
                        </div>
                    </div>

                    <!-- Selección de Mesa (solo para "Comer aquí") -->
                    <div class="table-selection hidden" id="modal-table-selection">
                        <h4>
                            <span>Selecciona una Mesa</span>
                           <!-- ✅ AHORA (Funciona) -->
   <button onclick="openTablesConfigModal()" class="tables-config-btn" type="button">
       <i class="fas fa-cog"></i>
       <span>Config</span>
   </button>
                        </h4>
                        <div id="table-loading" class="hidden text-center py-4 text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Cargando mesas...
                        </div>
                        <div class="table-grid" id="table-grid">
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
                            <!-- Las opciones se cargarán dinámicamente -->
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
                        <button class="step-btn confirm" onclick="processPayment()">Procesar Pago</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let originalTablesEnabled = false;
        // Variables globales
        let currentStep = 1;
        let selectedOrderType = 'comer-aqui';
        let selectedTable = null;
        let paymentRows = [];
        
        window.paymentModalState = {
            currentStep: 1,
            selectedOrderType: 'comer-aqui',
            selectedTable: null,
            paymentRows: []
        };
        // Abrir modal de configuración de mesas
    function openTablesConfigModal() {
        console.log('🔧 Abriendo modal de configuración de mesas...');
    
        const modal = document.getElementById('tables-config-modal');
        if (!modal) {
            console.error('❌ No se encontró el modal de configuración');
            return;
        }
    
    // Cargar el estado actual
    loadCurrentTablesConfig();
    
    // Mostrar el modal
    modal.classList.add('show');
    
    // Ocultar mensaje de éxito
    document.getElementById('config-success-message').classList.remove('show');
    
    console.log('✅ Modal de configuración de mesas abierto');
}
// Cerrar modal de configuración de mesas
function closeTablesConfigModal() {
    const modal = document.getElementById('tables-config-modal');
    if (modal) {
        modal.classList.remove('show');
    }
    console.log('✅ Modal de configuración cerrado');
}
async function loadCurrentTablesConfig() {
    try {
        // Obtener el estado desde window.tablesEnabled (ya disponible en el sistema)
        const currentState = window.tablesEnabled || false;
        
        console.log('📋 Estado actual de mesas:', currentState);
        
        // Actualizar el toggle
        const toggleInput = document.getElementById('tables-enabled-input');
        const toggleContainer = document.getElementById('toggle-container');
        const infoBox = document.getElementById('tables-info-box');
        
        if (toggleInput) {
            toggleInput.checked = currentState;
            originalTablesEnabled = currentState;
            
            // Actualizar clases visuales
            if (currentState) {
                toggleContainer.classList.add('active');
                infoBox.classList.add('show');
            } else {
                toggleContainer.classList.remove('active');
                infoBox.classList.remove('show');
            }
        }
        
    } catch (error) {
        console.error('Error al cargar configuración:', error);
    }
}
// Guardar configuración
async function saveTablesConfig() {
    const toggleInput = document.getElementById('tables-enabled-input');
    const saveBtn = document.getElementById('save-tables-config');
    const successMessage = document.getElementById('config-success-message');
    
    if (!toggleInput) return;
    
    const newState = toggleInput.checked;
    
    // Validar si hubo cambios
    if (newState === originalTablesEnabled) {
        console.log('ℹ️ No hay cambios que guardar');
        closeTablesConfigModal();
        return;
    }
    
    // Mostrar estado de carga
    const originalBtnText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner spinner"></i> Guardando...';
    saveBtn.classList.add('btn-loading');
    saveBtn.disabled = true;
    
    try {
        console.log('💾 Guardando configuración:', newState);
        
        // Preparar datos para enviar
        const formData = new FormData();
        formData.append('tables_enabled', newState ? '1' : '0');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || window.csrfToken);
        
        // Enviar al servidor
        const response = await fetch('/settings/update', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error al guardar la configuración');
        }
        
        console.log('✅ Configuración guardada exitosamente');
        
        // Actualizar estado global
        window.tablesEnabled = newState;
        originalTablesEnabled = newState;
        
        // Mostrar mensaje de éxito
        successMessage.classList.add('show');
        
        // Ocultar mensaje después de 2 segundos y cerrar modal
        setTimeout(() => {
            successMessage.classList.remove('show');
            closeTablesConfigModal();
            
            // Recargar las mesas si están habilitadas y estamos en el modal de pago
            if (newState && typeof loadModalTables === 'function') {
                console.log('🔄 Recargando mesas en el modal de pago...');
                loadModalTables();
            }
            
            // Actualizar visibilidad de secciones en el modal de pago
            if (typeof updateModalSectionsVisibility === 'function') {
                updateModalSectionsVisibility();
            }
        }, 2000);
        
    } catch (error) {
        console.error('❌ Error al guardar configuración:', error);
        alert('Error: ' + error.message);
    } finally {
        // Restaurar botón
        saveBtn.innerHTML = originalBtnText;
        saveBtn.classList.remove('btn-loading');
        saveBtn.disabled = false;
    }
}
        // Funciones básicas del modal
        function openPaymentModal() {
            console.log('🚀 Abriendo modal de pagos...');

            const modal = document.getElementById('payment-modal');
            if (!modal) {
                console.error('❌ No se encontró el modal');
                return;
            }

            modal.classList.remove('hidden');
            loadOrderData();

            // Inicializar modal
            setTimeout(() => {
                initializeModal();
            }, 50);
        }

        function closePaymentModal() {
            const modal = document.getElementById('payment-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
            resetModal();
        }

        function loadOrderData() {
            // Cargar datos del pedido actual
            const order = JSON.parse(localStorage.getItem('order')) || [];
            const total = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const totalElement = document.getElementById('order-total');
            if (totalElement) {
                totalElement.textContent = total.toFixed(2);
            }
        }

        function updateModalSectionsVisibility() {
            const tableSelection = document.getElementById('modal-table-selection');
            const deliverySelection = document.getElementById('modal-delivery-selection');
            const pickupNotes = document.getElementById('modal-pickup-notes');
            
            console.log('🔄 Actualizando visibilidad en modal, tipo:', window.paymentModalState.selectedOrderType);
            
            // Ocultar todas las secciones primero
            if (tableSelection) tableSelection.classList.add('hidden');
            if (deliverySelection) deliverySelection.classList.add('hidden');
            if (pickupNotes) pickupNotes.classList.add('hidden');
            
            // Mostrar secciones según el tipo de pedido
            switch(window.paymentModalState.selectedOrderType) {
                case 'comer-aqui':
                    if (tableSelection) {
                        console.log('✅ Mostrando selección de mesas en modal');
                        tableSelection.classList.remove('hidden');
                        loadModalTables();
                    }
                    break;
                    
                case 'para-llevar':
                    if (deliverySelection) {
                        console.log('✅ Mostrando selección de delivery en modal');
                        deliverySelection.classList.remove('hidden');
                        loadDeliveryServices();
                    }
                    break;
                    
                case 'recoger':
                    if (pickupNotes) {
                        console.log('✅ Mostrando notas para recoger en modal');
                        pickupNotes.classList.remove('hidden');
                        loadPickupNotes();
                    }
                    break;
            }
        }
        
        function updateTableSelectionVisibility() {
            const tableSelection = document.getElementById('table-selection');
            if (!tableSelection) {
                console.error('❌ No se encontró table-selection');
                return;
            }
            
            console.log('🔄 Actualizando visibilidad de mesas, tipo:', selectedOrderType);
            
            if (selectedOrderType === 'comer-aqui') {
                console.log('✅ Mostrando selección de mesas');
                tableSelection.classList.remove('hidden');
                // Cargar mesas dinámicamente desde el servidor
                loadModalTables();
            } else {
                console.log('❌ Ocultando selección de mesas');
                tableSelection.classList.add('hidden');
                selectedTable = null;
                // Deseleccionar cualquier mesa seleccionada
                document.querySelectorAll('.table-btn.selected').forEach(btn => {
                    btn.classList.remove('selected');
                });
            }
        }

        function loadDeliveryServices() {
            const deliverySelect = document.getElementById('modal-delivery-service');
            if (!deliverySelect) return;
        
            // En una implementación real, esto vendría del servidor
            const deliveryServices = [
                { name: 'Delivery Express' },
                { name: 'Rápido Delivery' },
                { name: 'Food Delivery' }
            ];
        
            deliverySelect.innerHTML = '<option value="">Seleccione un servicio de delivery</option>';
            deliveryServices.forEach(service => {
                const option = document.createElement('option');
                option.value = service.name;
                option.textContent = service.name;
                deliverySelect.appendChild(option);
            });
        
            // Seleccionar el servicio guardado si existe
            const savedService = localStorage.getItem('deliveryService');
            if (savedService) {
                deliverySelect.value = savedService;
            }
        
            // Reemplazar elemento para evitar múltiples listeners
            const newSelect = deliverySelect.cloneNode(true);
            deliverySelect.parentNode.replaceChild(newSelect, deliverySelect);
        
            // Configurar evento change
            newSelect.addEventListener('change', function() {
                if (this.value) {
                    localStorage.setItem('deliveryService', this.value);
                } else {
                    localStorage.removeItem('deliveryService');
                }
            });
        }

        function loadPickupNotes() {
            const notesTextarea = document.getElementById('modal-pickup-notes-text');
            if (!notesTextarea) return;
            
            // Cargar las notas guardadas si existen
            const savedNotes = localStorage.getItem('pickupNotes');
            if (savedNotes) {
                notesTextarea.value = savedNotes;
            }
            
            // Reemplazar elemento para evitar múltiples listeners
            const newTextarea = notesTextarea.cloneNode(true);
            notesTextarea.parentNode.replaceChild(newTextarea, notesTextarea);
            
            // Configurar evento input para guardar automáticamente
            newTextarea.addEventListener('input', function() {
                if (this.value.trim()) {
                    localStorage.setItem('pickupNotes', this.value);
                } else {
                    localStorage.removeItem('pickupNotes');
                }
            });
        }

        async function loadModalTables() {
            const tableGrid = document.getElementById('table-grid');
            const loadingElement = document.getElementById('table-loading');
            const errorElement = document.getElementById('table-error');
            const errorMessage = document.getElementById('table-error-message');
            
            if (!tableGrid) {
                console.error('❌ No se encontró table-grid');
                return;
            }
            
            // Mostrar loading
            if (loadingElement) loadingElement.classList.remove('hidden');
            if (errorElement) errorElement.classList.add('hidden');
            tableGrid.innerHTML = '';
            
            try {
                console.log('🔄 Cargando mesas desde el servidor...');
                
                // Hacer la petición al servidor
                const response = await fetch('/tables/available');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (!data.success || !data.data) {
                    throw new Error(data.message || 'No se pudieron obtener las mesas');
                }
                
                const tables = data.data;
                console.log('✅ Mesas obtenidas:', tables);
                
                // Limpiar el grid
                tableGrid.innerHTML = '';
                
                if (tables.length === 0) {
                    tableGrid.innerHTML = '<div class="col-span-full text-center text-gray-500">No hay mesas configuradas</div>';
                    return;
                }
                
                // Crear botones para cada mesa
                tables.forEach(table => {
                    const button = document.createElement('button');
                    button.className = 'table-btn';
                    button.dataset.tableId = table.id;
                    button.dataset.tableNumber = table.number;
                    button.dataset.status = table.state.toLowerCase().replace(' ', '-');
                    button.textContent = `Mesa ${table.number}`;
                    
                    // Aplicar estilos según el estado
                    switch(table.state) {
                        case 'Disponible':
                            button.addEventListener('click', function() {
                                selectTable(this);
                            });
                            break;
                        case 'Ocupada':
                            button.classList.add('occupied');
                            button.disabled = true;
                            button.title = 'Mesa ocupada';
                            break;
                        case 'Reservada':
                            button.classList.add('reserved');
                            button.disabled = true;
                            button.title = 'Mesa reservada';
                            break;
                        case 'No Disponible':
                            button.classList.add('occupied');
                            button.disabled = true;
                            button.title = 'Mesa no disponible';
                            break;
                        default:
                            button.classList.add('occupied');
                            button.disabled = true;
                            button.title = `Estado: ${table.state}`;
                            break;
                    }
                    
                    tableGrid.appendChild(button);
                });
                
                console.log(`✅ ${tables.length} mesas cargadas en el modal`);
                
            } catch (error) {
                console.error('❌ Error al cargar mesas:', error);
                if (errorMessage) errorMessage.textContent = error.message;
                if (errorElement) errorElement.classList.remove('hidden');
                tableGrid.innerHTML = '<div class="col-span-full text-center text-red-500">Error al cargar las mesas</div>';
            } finally {
                // Ocultar loading
                if (loadingElement) loadingElement.classList.add('hidden');
            }
        }
        
        function setupTableButtons() {
            // Esta función ahora es llamada automáticamente por loadModalTables()
            // Los event listeners se agregan directamente cuando se crean los botones
            console.log('✅ Botones de mesa configurados dinámicamente');
        }

        function selectTable(tableElement) {
    console.log('✅ Mesa seleccionada:', tableElement.dataset.tableNumber);
    
    // Deseleccionar mesa anterior
    document.querySelectorAll('#payment-modal .table-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });
    
    // Seleccionar nueva mesa
    tableElement.classList.add('selected');
    
    // Actualizar el estado global del modal
    window.paymentModalState.selectedTable = {
        id: tableElement.dataset.tableId,
        number: tableElement.dataset.tableNumber
    };
    
    // CRÍTICO: Actualizar localStorage inmediatamente
    localStorage.setItem('tableNumber', tableElement.dataset.tableId);
    
    console.log('📋 Mesa guardada en localStorage:', {
        id: tableElement.dataset.tableId,
        number: tableElement.dataset.tableNumber
    });
}

        // Configurar botones de tipo de pedido
        function initializeModal() {
            console.log('🔧 Inicializando modal...');
            
            // Sincronizar con el sistema principal PRIMERO
            syncWithMainSystem();
            
            // Configurar botones de tipo de pedido
            document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
                // Crear nuevo elemento para evitar listeners duplicados
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.addEventListener('click', function() {
                    handleOrderTypeSelection(this);
                });
            });

            // Configurar navegación por tabs
            document.querySelectorAll('#payment-modal .step-item').forEach(item => {
                const newItem = item.cloneNode(true);
                item.parentNode.replaceChild(newItem, item);
                
                newItem.addEventListener('click', function() {
                    const step = parseInt(this.getAttribute('data-step'));
                    goToStep(step);
                });
            });
            
            console.log('✅ Modal inicializado correctamente');
        }

        function handleOrderTypeSelection(btnElement) {
            console.log('📝 Tipo de pedido seleccionado en modal...');
            
            // Deseleccionar botón anterior
            document.querySelectorAll('#payment-modal .order-type-btn').forEach(b => {
                b.classList.remove('selected');
            });
            
            // Seleccionar nuevo botón
            btnElement.classList.add('selected');
            
            // Actualizar estado del modal
            const selectedType = btnElement.dataset.type;
            window.paymentModalState.selectedOrderType = selectedType;
            
            console.log('📋 selectedOrderType actualizado a:', selectedType);
            
            // Convertir y actualizar el sistema principal
            let orderTypeName = '';
            switch(selectedType) {
                case 'comer-aqui':
                    orderTypeName = 'Comer aquí';
                    break;
                case 'para-llevar':
                    orderTypeName = 'Para llevar';
                    break;
                case 'recoger':
                    orderTypeName = 'Recoger';
                    break;
            }
            
            // Actualizar sistema principal
            localStorage.setItem('orderType', orderTypeName);
            const orderTypeInput = document.getElementById('order-type');
            if (orderTypeInput) {
                orderTypeInput.value = orderTypeName;
            }
            
            // Limpiar datos irrelevantes
            if (selectedType !== 'comer-aqui') {
                localStorage.removeItem('tableNumber');
                window.paymentModalState.selectedTable = null;
            }
            
            if (selectedType !== 'para-llevar') {
                localStorage.removeItem('deliveryService');
            }
            
            if (selectedType !== 'recoger') {
                localStorage.removeItem('pickupNotes');
            }
            
            // Actualizar visibilidad
            updateModalSectionsVisibility();
            
            console.log('✅ Tipo actualizado a:', orderTypeName);
        }

        function syncWithMainSystem() {
            // Obtener el tipo de pedido del sistema principal
            const currentOrderType = localStorage.getItem('orderType') || 'Comer aquí';
            
            // Convertir a formato del modal
            let modalType = 'comer-aqui';
            switch(currentOrderType) {
                case 'Comer aquí':
                    modalType = 'comer-aqui';
                    break;
                case 'Para llevar':
                    modalType = 'para-llevar';
                    break;
                case 'Recoger':
                    modalType = 'recoger';
                    break;
            }
            
            // Actualizar el estado del modal
            window.paymentModalState.selectedOrderType = modalType;
            
            // Seleccionar el botón correcto
            document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
                btn.classList.remove('selected');
                if (btn.dataset.type === modalType) {
                    btn.classList.add('selected');
                }
            });
            
            // Actualizar visibilidad
            updateModalSectionsVisibility();
            
            console.log('✅ Modal sincronizado con sistema:', currentOrderType, '→', modalType);
        }

        function goToStep(step) {
    console.log('🚀 Intentando ir al paso:', step, 'Tipo actual:', window.paymentModalState.selectedOrderType);
    
    // Validaciones antes de cambiar de paso
    if (step === 2 && window.paymentModalState.currentStep === 1) {
        const orderType = window.paymentModalState.selectedOrderType;
        
        if (orderType === 'comer-aqui') {
            // CRÍTICO: Verificar que hay mesa seleccionada
            const selectedTableBtn = document.querySelector('#payment-modal .table-btn.selected');
            
            if (!selectedTableBtn) {
                alert('Por favor, selecciona una mesa para "Comer aquí"');
                return;
            }
            
            // IMPORTANTE: Obtener y guardar el ID correcto
            const tableId = selectedTableBtn.dataset.tableId;
            const tableNumber = selectedTableBtn.dataset.tableNumber;
            
            console.log('🔍 Validando mesa seleccionada:', {
                id: tableId,
                number: tableNumber
            });
            
            // Actualizar selectedTable con los valores correctos
            window.paymentModalState.selectedTable = {
                id: tableId,
                number: tableNumber
            };
            
            // CRÍTICO: Guardar en localStorage
            localStorage.setItem('tableNumber', tableId);
            
            console.log('✅ Mesa confirmada para paso 2:', localStorage.getItem('tableNumber'));
            
        } else if (orderType === 'para-llevar') {
            const deliverySelect = document.getElementById('modal-delivery-service');
            if (!deliverySelect || !deliverySelect.value) {
                alert('Por favor, selecciona un servicio de delivery para "Para llevar"');
                return;
            }
            localStorage.setItem('deliveryService', deliverySelect.value);
        }
    }
    
    // Cambiar al paso seleccionado
    document.querySelectorAll('#payment-modal .step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('#payment-modal .step-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const stepContent = document.getElementById(`step-${step}`);
    const stepItem = document.querySelector(`#payment-modal .step-item[data-step="${step}"]`);
    
    if (stepContent) stepContent.classList.add('active');
    if (stepItem) stepItem.classList.add('active');
    
    window.paymentModalState.currentStep = step;
    updateStepNavigation();
    
    console.log('✅ Cambiado al paso:', step);
}

        function nextStep() {
            if (window.paymentModalState.currentStep >= 2) return;
            goToStep(window.paymentModalState.currentStep + 1);
        }

        function prevStep() {
            if (window.paymentModalState.currentStep <= 1) return;
            goToStep(window.paymentModalState.currentStep - 1);
        }

        function updateStepNavigation() {
            const prevButton = document.querySelector('#payment-modal .step-btn.prev');
            const nextButton = document.querySelector('#payment-modal .step-btn.next');
            const confirmButton = document.querySelector('#payment-modal .step-btn.confirm');
            
            if (prevButton) {
                prevButton.disabled = window.paymentModalState.currentStep === 1;
            }
            
            if (nextButton && confirmButton) {
                if (window.paymentModalState.currentStep === 1) {
                    nextButton.style.display = 'block';
                    confirmButton.style.display = 'none';
                } else {
                    nextButton.style.display = 'none';
                    confirmButton.style.display = 'block';
                }
            }
        }

        function resetModal() {
            console.log('🔄 Reseteando modal...');
            
            window.paymentModalState = {
                currentStep: 1,
                selectedOrderType: 'comer-aqui',
                selectedTable: null,
                paymentRows: []
            };
            
            // Restablecer UI
            document.querySelectorAll('#payment-modal .step-content').forEach(step => {
                step.classList.remove('active');
            });
            const step1 = document.getElementById('step-1');
            if (step1) step1.classList.add('active');
            
            document.querySelectorAll('#payment-modal .step-item').forEach(item => {
                item.classList.remove('active');
            });
            const stepItem1 = document.querySelector('#payment-modal .step-item[data-step="1"]');
            if (stepItem1) stepItem1.classList.add('active');
            
            document.querySelectorAll('#payment-modal .order-type-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            const defaultBtn = document.querySelector('#payment-modal .order-type-btn[data-type="comer-aqui"]');
            if (defaultBtn) defaultBtn.classList.add('selected');
            
            const paymentContainer = document.getElementById('payment-rows-container');
            if (paymentContainer) paymentContainer.innerHTML = '';
            
            document.querySelectorAll('#payment-modal .table-btn.selected').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            updateModalSectionsVisibility();
            updateStepNavigation();
        }

        // Funciones de métodos de pago
       
        function removePaymentRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                window.paymentModalState.paymentRows = window.paymentModalState.paymentRows.filter(id => id !== rowId);
            }
        }

        function calculateChange(rowId) {
            const row = document.getElementById(rowId);
            const totalAmountInput = row.querySelector('.total-amount');
            const totalPaidInput = row.querySelector('.total-paid');
            const changeInput = row.querySelector('.change');
            
            const totalAmount = parseFloat(totalAmountInput.value) || 0;
            const totalPaid = parseFloat(totalPaidInput.value) || 0;
            const change = totalPaid - totalAmount;
            
            changeInput.value = change.toFixed(2);
            
            // Aplicar estilos según el cambio
            if (change < 0) {
                totalPaidInput.style.borderColor = 'var(--error-color)';
                changeInput.style.borderColor = 'var(--error-color)';
            } else {
                totalPaidInput.style.borderColor = 'var(--success-color)';
                changeInput.style.borderColor = 'var(--success-color)';
            }
        }

        function processPayment() {
            // Validar métodos de pago
            if (window.paymentModalState.paymentRows.length === 0) {
                alert('Debe agregar al menos un método de pago');
                return;
            }
            
            // Validar montos
            let totalPaid = 0;
            let valid = true;
            
            window.paymentModalState.paymentRows.forEach(rowId => {
                const row = document.getElementById(rowId);
                const totalPaidInput = row.querySelector('.total-paid');
                const paidValue = parseFloat(totalPaidInput.value) || 0;
                
                if (paidValue <= 0) {
                    totalPaidInput.style.borderColor = 'var(--error-color)';
                    valid = false;
                } else {
                    totalPaid += paidValue;
                }
            });
            
            if (!valid) {
                alert('Por favor, ingrese montos válidos en todos los métodos de pago');
                return;
            }
            
            const orderTotal = parseFloat(document.getElementById('order-total').textContent);
            
            if (totalPaid < orderTotal) {
                alert(`El total pagado (${totalPaid.toFixed(2)}) es menor al total del pedido (${orderTotal.toFixed(2)})`);
                return;
            }
            
            // Mostrar información según el tipo de pedido
            let info = '';
            if (window.paymentModalState.selectedOrderType === 'comer-aqui' && window.paymentModalState.selectedTable) {
                info = `Mesa seleccionada: Mesa ${window.paymentModalState.selectedTable.number}`;
            } else if (window.paymentModalState.selectedOrderType === 'para-llevar') {
                const deliveryService = localStorage.getItem('deliveryService') || 'No especificado';
                info = `Delivery: ${deliveryService}`;
            } else if (window.paymentModalState.selectedOrderType === 'recoger') {
                const pickupNotes = localStorage.getItem('pickupNotes') || 'Sin notas';
                info = `Notas: ${pickupNotes}`;
            }
            
            // Procesar pago
            alert(`Pago procesado correctamente\nTipo de pedido: ${window.paymentModalState.selectedOrderType}\n${info}`);
            closePaymentModal();
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM cargado, modal listo para usar');
        });
        document.addEventListener('DOMContentLoaded', function() {
    const toggleInput = document.getElementById('tables-enabled-input');
    const toggleContainer = document.getElementById('toggle-container');
    const infoBox = document.getElementById('tables-info-box');
    
    if (toggleInput) {
        toggleInput.addEventListener('change', function() {
            console.log('🔄 Toggle cambiado:', this.checked);
            
            // Actualizar clases visuales
            if (this.checked) {
                toggleContainer.classList.add('active');
                infoBox.classList.add('show');
            } else {
                toggleContainer.classList.remove('active');
                infoBox.classList.remove('show');
            }
        });
    }
});
document.addEventListener('click', function(e) {
    const modal = document.getElementById('tables-config-modal');
    if (e.target === modal) {
        closeTablesConfigModal();
    }
});

// Cerrar con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('tables-config-modal');
        if (modal && modal.classList.contains('show')) {
            closeTablesConfigModal();
        }
    }
});
    </script>
</body>
</html>