<x-layouts.admin title="POS Terminal">
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.js') }}/dist/flatpickr.min.css">
    <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>

    <style>
        /* ── POS Custom Variables ── */
        :root {
            --pos-primary: #6366f1;
            --pos-primary-dark: #4f46e5;
            --pos-success: #10b981;
            --pos-warning: #f59e0b;
            --pos-danger: #ef4444;
            --pos-cash: #10b981;
            --pos-card: #3b82f6;
            --pos-digital: #8b5cf6;
        }

        /* ── Layout ── */
        .pos-wrapper {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 18px;
            height: calc(100vh - 110px);
            min-width: 0;
        }

        @media (max-width: 1200px) {
            .pos-wrapper {
                grid-template-columns: 1fr 340px;
            }
        }

        @media (max-width: 992px) {
            .pos-wrapper {
                grid-template-columns: 1fr;
                height: auto;
                min-height: calc(100vh - 110px);
            }
            .pos-cart-panel {
                height: 600px;
            }
        }

        /* ── Left Panel ── */
        .pos-left {
            display: flex;
            flex-direction: column;
            gap: 14px;
            height: 100%;
            min-height: 0;
        }

        /* ── Search & Filter Bar ── */
        .pos-search-bar {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            min-width: 0;
        }

        .pos-search-input-wrap {
            width: 100%;
            position: relative;
        }

        .pos-search-input {
            width: 100%;
            padding: 11px 16px 11px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .pos-search-input::placeholder {
            color: #b0bac6;
            font-weight: 400;
        }

        .pos-search-input:focus {
            border-color: var(--pos-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .pos-search-input:focus::placeholder {
            color: #c7cdd6;
        }

        .pos-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        /* ── Category Chips ── */
        .pos-category-chips {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
        }

        .pos-category-chips::-webkit-scrollbar {
            display: none;
        }

        .pos-chip {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.18s;
            flex-shrink: 0;
        }

        .pos-chip:hover {
            border-color: var(--pos-primary);
            color: var(--pos-primary);
            background: rgba(99, 102, 241, 0.06);
        }

        .pos-chip.active {
            background: var(--pos-primary);
            border-color: var(--pos-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        /* ── Items Grid ── */
        .pos-items-grid {
            margin-top: 10px;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        .pos-items-inner {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(168px, 1fr));
            gap: 14px;
            padding-bottom: 8px;
        }

        /* ── Product Card ── */
        .pos-product-card {
            background: #fff;
            border: 1.5px solid #e8ecf0;
            border-radius: 14px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .pos-product-card:hover {
            margin-top: 5px;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.14);
            border-color: var(--pos-primary);
        }

        .pos-product-card:active {
            transform: scale(0.97);
        }

        .pos-product-img {
            padding: 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-bottom: 1px solid #f1f5f9;
            position: relative;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .pos-product-icon {
            font-size: 34px;
            color: var(--pos-primary);
        }

        .pos-product-stock-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
        }

        .pos-product-stock-badge.low {
            background: #fef3c7;
            color: #92400e;
        }

        .pos-product-stock-badge.ok {
            background: #d1fae5;
            color: #065f46;
        }

        .pos-product-info {
            padding: 12px 14px;
        }

        .pos-product-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .pos-product-category {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .pos-product-price {
            font-size: 15px;
            font-weight: 800;
            color: var(--pos-primary);
        }

        /* ── Right: Cart Panel ── */
        .pos-cart-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* ── Cart Header ── */
        .pos-cart-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        .pos-cart-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .pos-cart-count {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .pos-clear-btn {
            padding: 5px 12px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .pos-clear-btn:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        /* ── Customer & Date Row ── */
        .pos-customer-row {
            padding: 12px 16px;
            background: #fafbfd;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 10px;
            align-items: stretch;
            flex-shrink: 0;
        }

        .pos-select-wrap {
            flex: 1;
            position: relative;
        }

        .pos-select-wrap select,
        .pos-custom-select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            padding: 9px 36px 9px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13.5px;
            color: #1e293b;
            background: #fff;
            outline: none;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .pos-select-wrap select:focus,
        .pos-custom-select:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .pos-select-arrow {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .pos-add-customer-btn {
            width: 40px;
            flex-shrink: 0;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            color: var(--pos-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.15s;
        }

        .pos-add-customer-btn:hover {
            background: rgba(99, 102, 241, 0.08);
            border-color: var(--pos-primary);
        }

        .pos-date-input {
            width: 130px;
            flex-shrink: 0;
            padding: 9px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13.5px;
            color: #1e293b;
            background: #fff;
            outline: none;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        .pos-date-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        /* ── Cart Items ── */
        .pos-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 10px 14px;
            min-height: 0;
        }

        .pos-cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: #cbd5e1;
        }

        .pos-cart-empty i {
            font-size: 42px;
            display: block;
            margin-bottom: 10px;
        }

        .pos-cart-empty span {
            font-size: 13px;
            color: #94a3b8;
        }

        .pos-cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: border-color 0.15s;
        }

        .pos-cart-item:hover {
            border-color: #e0e7ff;
            background: #fafbff;
        }

        .pos-cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .pos-cart-item-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-cart-item-price {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── Qty Controls ── */
        .pos-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 0;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .pos-qty-btn {
            width: 26px;
            height: 26px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            color: #64748b;
            transition: background 0.12s, color 0.12s;
        }

        .pos-qty-btn:hover {
            background: var(--pos-primary);
            color: #fff;
        }

        .pos-qty-val {
            font-size: 12.5px;
            font-weight: 700;
            width: 22px;
            text-align: center;
            color: #0f172a;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }

        .pos-cart-item-total {
            font-weight: 800;
            color: #0f172a;
            font-size: 13px;
            width: 58px;
            text-align: right;
        }

        .pos-cart-item-del {
            color: #cbd5e1;
            font-size: 15px;
            cursor: pointer;
            transition: color 0.15s;
        }

        .pos-cart-item-del:hover {
            color: var(--pos-danger);
        }

        /* ── Order Summary ── */
        .pos-order-summary {
            flex-shrink: 0;
            padding: 14px 16px;
            background: #fafbfd;
            border-top: 1px solid #f1f5f9;
        }

        .pos-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: #475569;
        }

        .pos-summary-row .label {
            font-weight: 500;
        }

        .pos-summary-row .value {
            font-weight: 600;
            color: #0f172a;
        }

        .pos-discount-input {
            width: 80px;
            padding: 4px 8px;
            border: 1.5px solid #e2e8f0;
            border-radius: 7px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            text-align: right;
            outline: none;
            transition: border-color 0.2s;
        }

        .pos-discount-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
        }

        .pos-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            margin-top: 4px;
            border-top: 2px dashed #e2e8f0;
        }

        .pos-total-label {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .pos-total-amount {
            font-size: 22px;
            font-weight: 900;
            color: var(--pos-primary);
            letter-spacing: -0.5px;
        }

        /* ── Pay Button ── */
        .pos-pay-btn {
            width: 100%;
            margin-top: 12px;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .pos-pay-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .pos-pay-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .pos-pay-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ── PAYMENT MODAL ── */
        .pay-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            animation: fadeIn 0.2s ease;
        }

        .pay-modal {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: slideUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .pay-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pay-modal-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pay-modal-title i {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .pay-modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.15s;
        }

        .pay-modal-close:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .pay-modal-body {
            padding: 24px;
        }

        /* Step Indicator */
        .pay-steps {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            gap: 4px;
        }

        .pay-step {
            flex: 1;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            transition: background 0.3s;
        }

        .pay-step.done {
            background: var(--pos-primary);
        }

        .pay-step.active {
            background: linear-gradient(90deg, var(--pos-primary), #a78bfa);
        }

        /* Step 1: Order Confirmation */
        .pay-order-confirm {
            background: linear-gradient(135deg, #f0f4ff, #fafbff);
            border: 1px solid #e0e7ff;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .pay-confirm-row {
            display: flex;
            justify-content: space-between;
            font-size: 13.5px;
            color: #475569;
            margin-bottom: 6px;
        }

        .pay-confirm-row .val {
            font-weight: 600;
            color: #0f172a;
        }

        .pay-confirm-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1.5px dashed #c7d2fe;
        }

        .pay-confirm-total .label {
            font-size: 14px;
            font-weight: 700;
            color: #374151;
        }

        .pay-confirm-total .amount {
            font-size: 26px;
            font-weight: 900;
            color: var(--pos-primary);
            letter-spacing: -0.5px;
        }

        /* Step 2: Payment Method */
        .pay-method-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .pay-method-card {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .pay-method-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .pay-method-card.selected {
            border-color: var(--method-color, var(--pos-primary));
            background: var(--method-bg, rgba(99, 102, 241, 0.05));
            box-shadow: 0 4px 14px var(--method-shadow, rgba(99, 102, 241, 0.2));
        }

        .pay-method-card.selected::after {
            content: '\f26a';
            font-family: 'Bootstrap Icons';
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 14px;
            color: var(--method-color, var(--pos-primary));
        }

        .pay-method-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .pay-method-name {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
        }

        .pay-method-desc {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Step 2: Amount Input */
        .pay-amount-section {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .pay-amount-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .pay-amount-input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            background: #fff;
            outline: none;
            font-family: 'Inter', sans-serif;
            text-align: right;
            transition: border-color 0.2s;
        }

        .pay-amount-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .pay-change-row {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 13px;
        }

        .pay-change-label {
            color: #64748b;
        }

        .pay-change-value {
            font-weight: 700;
            font-size: 14px;
        }

        .pay-change-value.positive {
            color: var(--pos-success);
        }

        .pay-change-value.negative {
            color: var(--pos-danger);
        }

        /* Step 3: Success / Receipt */
        .pay-success-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .pay-success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            color: #059669;
            margin: 0 auto 14px;
            animation: bounceIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .pay-success-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .pay-success-sub {
            font-size: 13px;
            color: #64748b;
        }

        /* Receipt Print Mock */
        .pos-receipt {
            background: #fff;
            border: 1px dashed #d1d5db;
            border-radius: 10px;
            padding: 18px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #374151;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            max-height: 300px;
            overflow-y: auto;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #d1d5db;
        }

        .receipt-shop-name {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.3px;
        }

        .receipt-divider {
            border: none;
            border-top: 1px dashed #d1d5db;
            margin: 8px 0;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .receipt-item-name {
            flex: 1;
        }

        .receipt-item-qty {
            width: 30px;
            text-align: center;
            color: #6b7280;
        }

        .receipt-item-price {
            width: 70px;
            text-align: right;
        }

        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 13px;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #d1d5db;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
            color: #9ca3af;
            font-size: 11px;
        }

        /* ── Modal Action Buttons ── */
        .pay-modal-actions {
            display: flex;
            gap: 10px;
        }

        .pay-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            border: none;
            transition: all 0.18s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .pay-btn-outline {
            background: #f1f5f9;
            color: #475569;
        }

        .pay-btn-outline:hover {
            background: #e2e8f0;
        }

        .pay-btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
        }

        .pay-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(99, 102, 241, 0.4);
        }

        .pay-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
        }

        .pay-btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(16, 185, 129, 0.4);
        }

        .pay-btn-print {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            box-shadow: 0 3px 10px rgba(245, 158, 11, 0.3);
        }

        .pay-btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(245, 158, 11, 0.4);
        }

        /* ── Add Customer Modal ── */
        .cust-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.2s;
        }

        .cust-modal {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: slideUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .cust-modal-header {
            padding: 18px 22px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cust-modal-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cust-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: background 0.15s;
        }

        .cust-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .cust-modal-body {
            padding: 22px;
        }

        .pos-form-group {
            margin-bottom: 14px;
        }

        .pos-form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 6px;
        }

        .pos-form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            outline: none;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .pos-form-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .cust-modal-footer {
            padding: 16px 22px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Scrollbar */
        .pos-cart-items::-webkit-scrollbar,
        .pos-items-grid::-webkit-scrollbar,
        .pos-receipt::-webkit-scrollbar {
            width: 4px;
        }

        .pos-cart-items::-webkit-scrollbar-track,
        .pos-items-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-cart-items::-webkit-scrollbar-thumb,
        .pos-items-grid::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printable-receipt,
            #printable-receipt * {
                visibility: visible;
            }

            #printable-receipt {
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>

    <!-- POS Wrapper -->
    <div x-data="posApp()">
        <div class="pos-wrapper">

            <!-- ═══════════════════════════════════════════════════ -->
            <!-- LEFT: PRODUCTS PANEL -->
            <!-- ═══════════════════════════════════════════════════ -->
            <div class="pos-left">

                <!-- Search + Category Chips -->
                <div class="pos-search-bar">
                    <!-- Full-width Search Input -->
                    <div class="pos-search-input-wrap">
                        <i class="bi bi-search pos-search-icon"></i>
                        <input type="text" x-model="searchQuery" class="pos-search-input"
                            placeholder="Search menu items by name…">
                    </div>
                    <!-- Category Filter Chips below -->
                    <div class="pos-category-chips">
                        <button class="pos-chip" :class="activeCategory === 'All' && 'active'"
                            @click="activeCategory = 'All'">
                            <i class="bi bi-grid-3x3-gap"></i> All
                        </button>
                        @foreach ($categories as $category)
                            <button class="pos-chip" :class="activeCategory === '{{ $category }}' && 'active'"
                                @click="activeCategory = '{{ $category }}'">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Product Catalog -->
                <div class="pos-items-grid">
                    <div class="pos-items-inner">
                        <template x-for="item in filteredItems()" :key="item.id">
                            <div class="pos-product-card" @click="addToCart(item)">
                                <div class="pos-product-img" :style="item.image_url ? 'padding:0;' : ''">
                                    <template x-if="item.image_url">
                                        <img :src="item.image_url" alt="" style="width:100%;height:100%;object-fit:cover;">
                                    </template>
                                    <template x-if="!item.image_url">
                                        <i :class="'bi ' + item.image + ' pos-product-icon'"></i>
                                    </template>
                                    <span class="pos-product-stock-badge" :class="getAvailableStock(item) <= 5 ? 'low' : 'ok'">
                                        <span
                                            x-text="getAvailableStock(item) <= 0 ? 'Out of Stock' : (getAvailableStock(item) <= 5 ? '⚠ ' + getAvailableStock(item) + ' left' : getAvailableStock(item) + ' in stock')"></span>
                                    </span>
                                </div>
                                <div class="pos-product-info">
                                    <div class="pos-product-name" x-text="item.name"></div>
                                    <div class="pos-product-category" x-text="item.category"></div>
                                    <div class="pos-product-price">৳<span x-text="item.price"></span></div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="filteredItems().length === 0">
                            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#94a3b8;">
                                <i class="bi bi-search"
                                    style="font-size:42px;display:block;margin-bottom:12px;opacity:0.5;"></i>
                                <div style="font-size:15px;font-weight:600;">No items found</div>
                                <div style="font-size:13px;margin-top:4px;">Try a different search or category</div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════ -->
            <!-- RIGHT: CART PANEL -->
            <!-- ═══════════════════════════════════════════════════ -->
            <div class="pos-cart-panel">

                <!-- Cart Header -->
                <div class="pos-cart-header">
                    <div class="pos-cart-title">
                        <i class="bi bi-cart3"></i>
                        Current Order
                        <span class="pos-cart-count" x-text="cart.length + ' item' + (cart.length !== 1 ? 's' : '')">0
                            items</span>
                    </div>
                    <button class="pos-clear-btn" @click="clearCart()">
                        <i class="bi bi-trash3"></i> Clear
                    </button>
                </div>

                <!-- Customer + Date -->
                <div class="pos-customer-row">
                    <div class="pos-select-wrap" style="flex:1;">
                        <select x-ref="customerSelect" class="pos-custom-select"
                            @change="customerId = $event.target.value">
                            <option value="">👤 Walk-in Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}
                                    ({{ $customer->phone ?? 'No phone' }})
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down pos-select-arrow"></i>
                    </div>
                    <button class="pos-add-customer-btn" @click="showCustomerModal = true" title="Add new customer">
                        <i class="bi bi-person-plus"></i>
                    </button>
                    <input type="text" x-ref="datePicker" x-model="saleDate" class="pos-date-input"
                        placeholder="Date">
                </div>

                <!-- Cart Items -->
                <div class="pos-cart-items">
                    <template x-if="cart.length === 0">
                        <div class="pos-cart-empty">
                            <i class="bi bi-cart-x"></i>
                            <span>Cart is empty.<br>Click products on the left to add.</span>
                        </div>
                    </template>

                    <template x-for="cartItem in cart" :key="cartItem.id">
                        <div class="pos-cart-item">
                            <div class="pos-cart-item-info">
                                <div class="pos-cart-item-name" x-text="cartItem.name"></div>
                                <div class="pos-cart-item-price">৳<span x-text="cartItem.price"></span> / unit</div>
                            </div>
                            <div class="pos-qty-ctrl">
                                <button class="pos-qty-btn" @click="updateQty(cartItem.id, -1)">−</button>
                                <span class="pos-qty-val" x-text="cartItem.qty"></span>
                                <button class="pos-qty-btn" @click="updateQty(cartItem.id, 1)">+</button>
                            </div>
                            <div class="pos-cart-item-total">৳<span
                                    x-text="(cartItem.price * cartItem.qty).toFixed(0)"></span></div>
                            <i class="bi bi-x pos-cart-item-del" @click="removeFromCart(cartItem.id)"
                                title="Remove"></i>
                        </div>
                    </template>
                </div>

                <!-- Order Summary -->
                <div class="pos-order-summary">
                    <div class="pos-summary-row">
                        <span class="label">Subtotal</span>
                        <span class="value">৳<span x-text="subtotal()"></span></span>
                    </div>
                    <div class="pos-summary-row">
                        <span class="label">Discount (৳)</span>
                        <input type="number" x-model.number="discount" min="0" class="pos-discount-input"
                            placeholder="0">
                    </div>
                    <div class="pos-summary-row">
                        <span class="label">VAT / Tax (5%)</span>
                        <span class="value">৳<span x-text="tax()"></span></span>
                    </div>

                    <div class="pos-total-row">
                        <span class="pos-total-label">Total Bill</span>
                        <span class="pos-total-amount">৳<span x-text="total()"></span></span>
                    </div>

                    <!-- Pay & Print Button -->
                    <button class="pos-pay-btn" @click="openPayModal()" :disabled="cart.length === 0 || processing">
                        <i class="bi bi-wallet2"></i>
                        <span x-text="processing ? 'Processing…' : 'Pay & Print Receipt'"></span>
                    </button>
                </div>
            </div>

        </div><!-- end pos-wrapper -->


        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- PAYMENT FLOW MODAL -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div class="pay-modal-overlay" x-show="showPayModal" style="display:none;" @click.self="closePayModal()">
            <div class="pay-modal" @click.stop>

                <!-- Modal Header -->
                <div class="pay-modal-header">
                    <div class="pay-modal-title">
                        <div
                            :style="'width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;background:' +
                            (payStep === 3 ? 'linear-gradient(135deg,#d1fae5,#a7f3d0)' :
                                'linear-gradient(135deg,#ede9fe,#ddd6fe)')">
                            <i :class="payStep === 1 ? 'bi bi-receipt' : (payStep === 2 ? 'bi bi-credit-card-2-front' :
                                'bi bi-check-circle-fill')"
                                :style="payStep === 3 ? 'color:#059669' : 'color:#6366f1'"></i>
                        </div>
                        <span
                            x-text="payStep === 1 ? 'Confirm Order' : (payStep === 2 ? 'Select Payment' : 'Payment Complete')"></span>
                    </div>
                    <button class="pay-modal-close" @click="closePayModal()" x-show="payStep !== 3 || !processing">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                <!-- Step progress bar -->
                <div style="padding:0 24px;margin-top:16px;">
                    <div class="pay-steps">
                        <div class="pay-step" :class="payStep >= 1 ? (payStep > 1 ? 'done' : 'active') : ''"></div>
                        <div style="width:8px;height:4px;background:transparent;"></div>
                        <div class="pay-step" :class="payStep >= 2 ? (payStep > 2 ? 'done' : 'active') : ''"></div>
                        <div style="width:8px;height:4px;background:transparent;"></div>
                        <div class="pay-step" :class="payStep >= 3 ? 'done' : ''"></div>
                    </div>
                    <div
                        style="display:flex;justify-content:space-between;font-size:11px;color:#94a3b8;font-weight:600;margin-top:4px;margin-bottom:6px;">
                        <span :style="payStep >= 1 ? 'color:#6366f1' : ''">Confirm</span>
                        <span :style="payStep >= 2 ? 'color:#6366f1' : ''">Payment</span>
                        <span :style="payStep >= 3 ? 'color:#059669' : ''">Done</span>
                    </div>
                </div>

                <div class="pay-modal-body">

                    <!-- ── STEP 1: Confirm Order ── -->
                    <div x-show="payStep === 1">
                        <div class="pay-order-confirm">
                            <div class="pay-confirm-row">
                                <span>Items</span>
                                <span class="val" x-text="cart.length + ' product(s)'"></span>
                            </div>
                            <template x-for="item in cart" :key="item.id">
                                <div class="pay-confirm-row" style="font-size:12.5px;padding-left:8px;color:#64748b;">
                                    <span x-text="item.name + ' × ' + item.qty"></span>
                                    <span class="val" x-text="'৳' + (item.price * item.qty)"></span>
                                </div>
                            </template>
                            <div class="pay-confirm-row" style="margin-top:6px;">
                                <span>Discount</span>
                                <span class="val" style="color:#10b981;">- ৳<span x-text="discount"></span></span>
                            </div>
                            <div class="pay-confirm-row">
                                <span>VAT (5%)</span>
                                <span class="val">৳<span x-text="tax()"></span></span>
                            </div>
                            <div class="pay-confirm-total">
                                <span class="label">Grand Total</span>
                                <span class="amount">৳<span x-text="total()"></span></span>
                            </div>
                        </div>

                        <div class="pay-modal-actions">
                            <button class="pay-btn pay-btn-outline" @click="closePayModal()">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </button>
                            <button class="pay-btn pay-btn-primary" @click="payStep = 2">
                                Proceed to Pay <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ── STEP 2: Choose Payment Method ── -->
                    <div x-show="payStep === 2">
                        <p style="font-size:13px;color:#64748b;margin-bottom:14px;font-weight:500;">Choose how your
                            customer will pay:</p>

                        <!-- Payment Method Cards -->
                        <div class="pay-method-cards">
                            <!-- Cash -->
                            <div class="pay-method-card"
                                style="--method-color:#10b981;--method-bg:rgba(16,185,129,0.05);--method-shadow:rgba(16,185,129,0.18);"
                                :class="paymentMethod === 'cash' && 'selected'"
                                @click="paymentMethod = 'cash'; autoSetPaidAmount()">
                                <div class="pay-method-icon" style="color:#10b981;">💵</div>
                                <div class="pay-method-name">Cash</div>
                                <div class="pay-method-desc">Physical money</div>
                            </div>
                            <!-- Card -->
                            <div class="pay-method-card"
                                style="--method-color:#3b82f6;--method-bg:rgba(59,130,246,0.05);--method-shadow:rgba(59,130,246,0.18);"
                                :class="paymentMethod === 'card' && 'selected'"
                                @click="paymentMethod = 'card'; autoSetPaidAmount()">
                                <div class="pay-method-icon" style="color:#3b82f6;">💳</div>
                                <div class="pay-method-name">Card</div>
                                <div class="pay-method-desc">Debit / Credit</div>
                            </div>
                            <!-- Digital -->
                            <div class="pay-method-card"
                                style="--method-color:#8b5cf6;--method-bg:rgba(139,92,246,0.05);--method-shadow:rgba(139,92,246,0.18);"
                                :class="paymentMethod === 'mobile_pay' && 'selected'"
                                @click="paymentMethod = 'mobile_pay'; autoSetPaidAmount()">
                                <div class="pay-method-icon" style="color:#8b5cf6;">📱</div>
                                <div class="pay-method-name">Digital</div>
                                <div class="pay-method-desc">bKash / Nagad</div>
                            </div>
                        </div>

                        <!-- Credit option -->
                        <div style="margin-bottom:14px;">
                            <label
                                style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;transition:border-color 0.15s;"
                                :style="paymentMethod === 'credit' ? 'border-color:#ef4444;background:rgba(239,68,68,0.04)' :
                                    ''"
                                @click="paymentMethod = 'credit'; amountTendered = 0">
                                <span style="font-size:20px;">📒</span>
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:700;color:#374151;">Credit / Unpaid</div>
                                    <div style="font-size:11.5px;color:#94a3b8;">Record as due — collect later</div>
                                </div>
                                <div style="width:18px;height:18px;border-radius:50%;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;"
                                    :style="paymentMethod === 'credit' ? 'border-color:#ef4444;background:#ef4444' : ''">
                                    <i class="bi bi-check" style="font-size:11px;color:#fff;"
                                        x-show="paymentMethod === 'credit'"></i>
                                </div>
                            </label>
                        </div>

                        <!-- Amount Tendered (for non-credit) -->
                        <div class="pay-amount-section" x-show="paymentMethod !== 'credit'">
                            <div class="pay-amount-label">Amount Received (৳)</div>
                            <input type="number" class="pay-amount-input" x-model.number="amountTendered"
                                min="0" placeholder="0.00" @input="null">
                            <div class="pay-change-row" x-show="amountTendered > 0">
                                <span class="pay-change-label"
                                    x-text="amountTendered >= total() ? 'Change to Return:' : 'Remaining Due:'"></span>
                                <span class="pay-change-value"
                                    :class="amountTendered >= total() ? 'positive' : 'negative'"
                                    x-text="'৳' + Math.abs(amountTendered - total()).toFixed(0)"></span>
                            </div>
                        </div>

                        <div class="pay-modal-actions">
                            <button class="pay-btn pay-btn-outline" @click="payStep = 1">
                                <i class="bi bi-arrow-left"></i> Back
                            </button>
                            <button class="pay-btn pay-btn-success" @click="processCheckout()"
                                :disabled="processing">
                                <i class="bi bi-check-lg"></i>
                                <span x-text="processing ? 'Processing…' : 'Complete Payment'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- ── STEP 3: Success + Print Receipt ── -->
                    <div x-show="payStep === 3">
                        <div class="pay-success-header">
                            <div class="pay-success-icon">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="pay-success-title">Payment Successful!</div>
                            <div class="pay-success-sub">Invoice: <strong x-text="invoiceNo"></strong></div>
                        </div>

                        <!-- Receipt Mock -->
                        <div class="pos-receipt" id="printable-receipt">
                            <div class="receipt-header">
                                <div class="receipt-shop-name">{{ config('settings.restaurant_name', 'Bakery Shop') }}
                                </div>
                                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Point of Sale Receipt</div>
                                <div style="font-size:11px;color:#6b7280;" x-text="receiptDate"></div>
                                <div style="font-size:11px;color:#6b7280;">Invoice: <span x-text="invoiceNo"></span>
                                </div>
                            </div>

                            <div class="receipt-row"
                                style="font-weight:700;font-size:11px;color:#6b7280;border-bottom:1px dashed #d1d5db;padding-bottom:4px;margin-bottom:4px;">
                                <span class="receipt-item-name">Item</span>
                                <span class="receipt-item-qty">Qty</span>
                                <span class="receipt-item-price">Price</span>
                            </div>

                            <template x-for="item in receiptItems" :key="item.id">
                                <div class="receipt-row">
                                    <span class="receipt-item-name" x-text="item.name"></span>
                                    <span class="receipt-item-qty" x-text="item.qty"></span>
                                    <span class="receipt-item-price" x-text="'৳' + (item.price * item.qty)"></span>
                                </div>
                            </template>

                            <hr class="receipt-divider">
                            <div class="receipt-row" style="font-size:11.5px;"><span>Subtotal</span><span
                                    x-text="'৳' + receiptSubtotal"></span></div>
                            <div class="receipt-row" style="font-size:11.5px;color:#10b981;">
                                <span>Discount</span><span x-text="'- ৳' + receiptDiscount"></span>
                            </div>
                            <div class="receipt-row" style="font-size:11.5px;"><span>VAT (5%)</span><span
                                    x-text="'৳' + receiptTax"></span></div>
                            <div class="receipt-total-row">
                                <span>TOTAL</span>
                                <span x-text="'৳' + receiptTotal"></span>
                            </div>
                            <div class="receipt-row" style="font-size:11.5px;margin-top:4px;"
                                x-show="paymentMethod !== 'credit'">
                                <span>Paid (<span x-text="receiptPaymentMethod"></span>)</span>
                                <span x-text="'৳' + receiptAmountPaid"></span>
                            </div>
                            <div class="receipt-row" style="font-size:11.5px;color:#10b981;"
                                x-show="paymentMethod !== 'credit' && receiptChange > 0">
                                <span>Change</span>
                                <span x-text="'৳' + receiptChange"></span>
                            </div>
                            <div class="receipt-row" style="font-size:11.5px;color:#ef4444;"
                                x-show="paymentMethod === 'credit'">
                                <span>Status</span>
                                <span>CREDIT (Unpaid)</span>
                            </div>

                            <div class="receipt-footer">
                                <div>Thank you for your purchase!</div>
                                <div style="margin-top:4px;">Printed: <span x-text="receiptDate"></span></div>
                                <div style="margin-top:6px;font-size:13px;">* * * * * * * * * *</div>
                            </div>
                        </div>

                        <div class="pay-modal-actions">
                            <button class="pay-btn pay-btn-print" @click="printReceipt()">
                                <i class="bi bi-printer"></i> Print Receipt
                            </button>
                            <button class="pay-btn pay-btn-primary" @click="finishSale()">
                                <i class="bi bi-check2-all"></i> New Order
                            </button>
                        </div>
                    </div>

                </div><!-- end pay-modal-body -->
            </div><!-- end pay-modal -->
        </div><!-- end pay-modal-overlay -->


        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- ADD CUSTOMER MODAL -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div class="cust-modal-overlay" x-show="showCustomerModal" style="display:none;">
            <div class="cust-modal" @click.stop>
                <div class="cust-modal-header">
                    <div class="cust-modal-title">
                        <i class="bi bi-person-plus-fill"></i> Add New Customer
                    </div>
                    <button class="cust-modal-close" @click="showCustomerModal = false">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="cust-modal-body">
                    <div class="pos-form-group">
                        <label class="pos-form-label">Full Name <span style="color:#ef4444">*</span></label>
                        <input type="text" x-model="newCustomer.name" class="pos-form-input"
                            placeholder="e.g. John Doe">
                    </div>
                    <div class="pos-form-group" style="margin-bottom:0;">
                        <label class="pos-form-label">Phone Number</label>
                        <input type="text" x-model="newCustomer.phone" class="pos-form-input"
                            placeholder="e.g. 01700000000">
                    </div>
                </div>
                <div class="cust-modal-footer">
                    <button class="pay-btn pay-btn-outline" style="flex:none;padding:10px 18px;"
                        @click="showCustomerModal = false">Cancel</button>
                    <button class="pay-btn pay-btn-primary" style="flex:none;padding:10px 18px;"
                        @click="saveCustomer()" :disabled="savingCustomer || !newCustomer.name">
                        <span x-text="savingCustomer ? 'Saving…' : 'Save Customer'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div><!-- end x-data -->

    <script>
        function posApp() {
            return {
                searchQuery: '',
                activeCategory: 'All',
                discount: 0,
                customerId: '',
                paymentMethod: 'cash',
                amountTendered: 0,
                saleDate: new Date().toISOString().split('T')[0],
                processing: false,
                items: @json($posItems),
                customers: @json($customers),
                cart: [],

                // Payment modal state
                showPayModal: false,
                payStep: 1, // 1=confirm, 2=payment method, 3=success

                // Receipt data (after successful checkout)
                invoiceNo: '',
                receiptDate: '',
                receiptItems: [],
                receiptSubtotal: 0,
                receiptDiscount: 0,
                receiptTax: 0,
                receiptTotal: 0,
                receiptAmountPaid: 0,
                receiptChange: 0,
                receiptPaymentMethod: 'Cash',

                // Customer modal state
                showCustomerModal: false,
                savingCustomer: false,
                newCustomer: {
                    name: '',
                    phone: ''
                },

                init() {
                    flatpickr(this.$refs.datePicker, {
                        defaultDate: this.saleDate,
                        dateFormat: 'Y-m-d',
                        onChange: (selectedDates, dateStr) => {
                            this.saleDate = dateStr;
                        }
                    });

                    this.$watch('cart', () => this.autoSetPaidAmount());
                    this.$watch('discount', () => this.autoSetPaidAmount());
                    this.$watch('paymentMethod', (val) => {
                        if (val === 'credit') {
                            this.amountTendered = 0;
                        } else {
                            this.amountTendered = this.total();
                        }
                    });
                },

                autoSetPaidAmount() {
                    if (this.paymentMethod !== 'credit') {
                        this.amountTendered = this.total();
                    }
                },

                filteredItems() {
                    return this.items.filter(item => {
                        const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesCategory = this.activeCategory === 'All' || item.category === this
                            .activeCategory;
                        return matchesSearch && matchesCategory;
                    });
                },

                getAvailableStock(item) {
                    const cartItem = this.cart.find(c => c.id === item.id);
                    const qtyInCart = cartItem ? cartItem.qty : 0;
                    return item.stock - qtyInCart;
                },

                addToCart(item) {
                    const existing = this.cart.find(c => c.id === item.id);
                    if (existing) {
                        if (existing.qty < item.stock) {
                            existing.qty++;
                        } else {
                            Swal.fire({
                                title: 'Stock Limit Reached',
                                text: 'Cannot exceed available stock of ' + item.stock + ' items.',
                                icon: 'warning',
                                confirmButtonColor: '#6366f1',
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        if (item.stock > 0) {
                            this.cart.push({
                                id: item.id,
                                name: item.name,
                                price: item.price,
                                qty: 1
                            });
                        } else {
                            Swal.fire({
                                title: 'Out of Stock',
                                text: 'This product is currently out of stock.',
                                icon: 'error',
                                confirmButtonColor: '#6366f1',
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    }
                },

                updateQty(id, amount) {
                    const item = this.cart.find(c => c.id === id);
                    const catalogItem = this.items.find(i => i.id === id);
                    if (item) {
                        item.qty += amount;
                        if (item.qty <= 0) {
                            this.cart = this.cart.filter(c => c.id !== id);
                        } else if (item.qty > catalogItem.stock) {
                            item.qty = catalogItem.stock;
                            Swal.fire({
                                title: 'Stock Limit',
                                text: 'Cannot exceed available stock.',
                                icon: 'warning',
                                toast: true,
                                position: 'top-end',
                                timer: 2500,
                                showConfirmButton: false
                            });
                        }
                    }
                },

                removeFromCart(id) {
                    this.cart = this.cart.filter(c => c.id !== id);
                },

                clearCart() {
                    this.cart = [];
                    this.discount = 0;
                },

                subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                tax() {
                    return Math.round(this.subtotal() * 0.05);
                },

                total() {
                    const sum = this.subtotal() - this.discount + this.tax();
                    return sum > 0 ? sum : 0;
                },

                // Open pay modal → step 1
                openPayModal() {
                    if (this.cart.length === 0) return;
                    this.payStep = 1;
                    this.showPayModal = true;
                },

                closePayModal() {
                    if (this.processing) return;
                    this.showPayModal = false;
                    this.payStep = 1;
                },

                // Step 2 → complete checkout via API
                async processCheckout() {
                    const totalBill = this.total();
                    this.processing = true;

                    try {
                        const response = await fetch('{{ route('dashboard.pos-terminal.checkout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                customer_id: this.customerId || null,
                                sale_date: this.saleDate,
                                discount: this.discount || 0,
                                tax: this.tax(),
                                subtotal: this.subtotal(),
                                total: totalBill,
                                amount_tendered: this.amountTendered || 0,
                                payment_method: this.paymentMethod,
                                cart: this.cart
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Populate receipt data
                            this.invoiceNo = data.invoice_no;
                            this.receiptDate = new Date().toLocaleString('en-BD', {
                                dateStyle: 'medium',
                                timeStyle: 'short'
                            });
                            this.receiptItems = [...this.cart];
                            this.receiptSubtotal = this.subtotal();
                            this.receiptDiscount = this.discount;
                            this.receiptTax = this.tax();
                            this.receiptTotal = totalBill;
                            this.receiptAmountPaid = this.amountTendered;
                            this.receiptChange = Math.max(0, this.amountTendered - totalBill);
                            this.receiptPaymentMethod = {
                                cash: 'Cash',
                                card: 'Card',
                                mobile_pay: 'Digital Pay',
                                credit: 'Credit'
                            } [this.paymentMethod] || 'Cash';

                            // Update stock
                            this.cart.forEach(cartItem => {
                                let item = this.items.find(i => i.id === cartItem.id);
                                if (item) item.stock -= cartItem.qty;
                            });

                            this.clearCart();
                            this.payStep = 3;
                        } else {
                            throw new Error(data.message || 'Validation error');
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Something went wrong.',
                            icon: 'error',
                            confirmButtonColor: '#6366f1'
                        });
                    } finally {
                        this.processing = false;
                    }
                },

                printReceipt() {
                    window.print();
                },

                finishSale() {
                    this.showPayModal = false;
                    this.payStep = 1;
                    this.discount = 0;
                    this.customerId = '';
                    this.paymentMethod = 'cash';
                    this.amountTendered = 0;
                },

                async saveCustomer() {
                    this.savingCustomer = true;
                    try {
                        const response = await fetch('{{ route('dashboard.customers.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: this.newCustomer.name,
                                phone: this.newCustomer.phone || null,
                                is_active: true
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.customers.push(data.customer);
                            this.customerId = data.customer.id;

                            // Add to native select
                            const sel = this.$refs.customerSelect;
                            const opt = new Option(
                                `${data.customer.name} (${data.customer.phone || 'No phone'})`,
                                data.customer.id, true, true
                            );
                            sel.appendChild(opt);

                            this.showCustomerModal = false;
                            this.newCustomer = {
                                name: '',
                                phone: ''
                            };

                            Swal.fire({
                                title: 'Customer Added!',
                                text: data.customer.name + ' has been saved.',
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false,
                                confirmButtonColor: '#6366f1'
                            });
                        } else {
                            throw new Error(data.message || 'Failed to create customer');
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: error.message,
                            icon: 'error',
                            confirmButtonColor: '#6366f1'
                        });
                    } finally {
                        this.savingCustomer = false;
                    }
                }
            }
        }
    </script>

</x-layouts.admin>
