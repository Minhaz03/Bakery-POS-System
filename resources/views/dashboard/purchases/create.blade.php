<x-layouts.admin title="Create Purchase Order">
    <!-- Select2 Assets & Custom Styling -->
    <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>

    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13.5px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            line-height: 28px;
            padding-left: 2px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }
        .select2-dropdown {
            border-color: #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }
        .select2-results__option {
            font-size: 13px;
            padding: 8px 12px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6366f1;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.purchases') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Create Purchase Order</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Create a new pending purchase order for supplies or raw materials.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="card" style="margin-bottom:20px;border-color:#fca5a5;background:#fef2f2;color:#991b1b;">
            <div class="card-body" style="padding:16px 20px;">
                <h5 style="margin:0 0 8px 0;font-weight:700;"><i class="bi bi-exclamation-triangle-fill"></i> Please correct the errors below:</h5>
                <ul style="margin:0;padding-left:20px;font-size:13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.purchases.store') }}" id="purchase-form" enctype="multipart/form-data">
        @csrf

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">
            <!-- Left Side: Order Items -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <!-- Basic Info Card -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-info-circle"></i> Basic Details</span>
                    </div>
                    <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label" for="supplier_id">Supplier <span style="color:#ef4444;">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-control select2" required style="cursor:pointer;width:100%;">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} (Balance: ৳ {{ number_format($supplier->current_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="purchase_date">Purchase Date <span style="color:#ef4444;">*</span></label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required style="height:42px;">
                        </div>
                        <div class="form-group" style="grid-column: span 2;margin-bottom:0;">
                            <label class="form-label" for="attachment">Attachment Document (Invoice / Receipt / PO Scan)</label>
                            <input type="file" name="attachment" id="attachment" class="form-control" accept="image/*,.pdf,.doc,.docx" style="padding: 7px 12px;height:auto;">
                            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Supported formats: PDF, Images, DOC/DOCX (Max: 10MB)</small>
                        </div>
                    </div>
                </div>

                <!-- Products Selector & Items Card -->
                <div class="card">
                    <div class="card-header" style="justify-content:space-between;display:flex;align-items:center;">
                        <span class="card-title"><i class="bi bi-box-seam"></i> Purchase Items</span>
                    </div>
                    <div class="card-body">
                        <!-- Add Item Row Selector -->
                        <div style="display:flex;gap:12px;margin-bottom:20px;align-items:flex-end;">
                            <div style="flex:1;">
                                <label class="form-label" for="product_selector">Select Product to Add</label>
                                <select id="product_selector" class="form-control select2" style="cursor:pointer;width:100%;">
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}" data-unit="{{ $product->unit?->short_name ?? 'pcs' }}" data-unit-id="{{ $product->unit_id }}" data-base-unit-id="{{ $product->unit?->base_unit_id }}" data-sku="{{ $product->sku }}">
                                            {{ $product->name }} ({{ $product->sku }}) - Current Stock: {{ floatval($product->stock_qty) }} {{ $product->unit?->short_name ?? 'pcs' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" id="btn-add-product" class="btn btn-primary" style="height:42px;"><i class="bi bi-plus-lg"></i> Add to List</button>
                        </div>

                        <!-- Items Table -->
                        <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:8px;">
                            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;" id="items-table">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                                        <th style="padding:12px 16px;">Product Details</th>
                                        <th style="padding:12px 16px;width:150px;text-align:center;">Quantity</th>
                                        <th style="padding:12px 16px;width:160px;text-align:right;">Unit Cost (৳)</th>
                                        <th style="padding:12px 16px;width:160px;text-align:right;">Subtotal</th>
                                        <th style="padding:12px 16px;width:60px;text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody style="color:#334155;" id="items-tbody">
                                    <!-- Dynamic rows go here -->
                                    <tr id="empty-row">
                                        <td colspan="5" style="padding:32px;text-align:center;color:#94a3b8;">
                                            <i class="bi bi-cart" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                                            No items added. Select a product above to build your order list.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary & Financials -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-receipt"></i> Order Summary</span>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
                        <!-- Subtotal Display -->
                        <div style="display:flex;justify-content:space-between;font-size:14px;color:#475569;">
                            <span>Subtotal:</span>
                            <span style="font-weight:600;color:#0f172a;">৳ <span id="summary-subtotal">0.00</span></span>
                        </div>

                        <!-- Financial Modifiers -->
                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" for="discount_amount">Discount Amount (৳)</label>
                            <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" class="form-control calculation-input" value="{{ old('discount_amount', '0.00') }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" for="tax_amount">Order Tax Amount (৳)</label>
                            <input type="number" step="0.01" min="0" name="tax_amount" id="tax_amount" class="form-control calculation-input" value="{{ old('tax_amount', '0.00') }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" for="shipping_cost">Shipping Cost (৳)</label>
                            <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost" class="form-control calculation-input" value="{{ old('shipping_cost', '0.00') }}" required>
                        </div>

                        <hr style="border:0;border-top:1px solid #e2e8f0;margin:8px 0;">

                        <!-- Grand Total Display -->
                        <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;color:#0f172a;">
                            <span>Grand Total:</span>
                            <span>৳ <span id="summary-grand-total">0.00</span></span>
                        </div>

                        <hr style="border:0;border-top:1px solid #e2e8f0;margin:8px 0;">

                        <!-- Payment & Dues -->
                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" for="amount_paid">Amount Paid (৳) <span style="color:#ef4444;">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" class="form-control calculation-input" value="{{ old('amount_paid', '0.00') }}" required>
                        </div>

                        <div style="display:flex;justify-content:space-between;font-size:14px;color:#475569;">
                            <span>Remaining Due:</span>
                            <span style="font-weight:700;color:#ef4444;">৳ <span id="summary-amount-due">0.00</span></span>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;margin-top:8px;">
                            <label class="form-label" for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" style="cursor:pointer;">
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="credit" {{ old('payment_method') === 'credit' ? 'selected' : '' }}>Credit / Supplier Account</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label class="form-label" for="notes">Purchase Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Supplier terms, delivery instructions, reference, etc...">{{ old('notes') }}</textarea>
                        </div>

                        <div style="margin-top:12px;display:flex;flex-direction:column;gap:10px;">
                            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-save"></i> Save Purchase Order</button>
                            <a href="{{ route('dashboard.purchases') }}" class="btn btn-outline" style="text-decoration:none;justify-content:center;text-align:center;">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            const allUnits = @json($allUnits);
            const unitMap = {};
            allUnits.forEach(u => unitMap[u.id] = u);

            // Initialize Select2 dropdowns
            $('#supplier_id').select2({
                placeholder: 'Select Supplier',
                allowClear: true,
                width: '100%'
            });
            $('#product_selector').select2({
                placeholder: '-- Choose Product --',
                allowClear: true,
                width: '100%'
            });

            let rowIndex = 0;
            const itemsTbody = document.getElementById('items-tbody');
            const emptyRow = document.getElementById('empty-row');
            const btnAddProduct = document.getElementById('btn-add-product');
            const productSelector = document.getElementById('product_selector');

            const discountInput = document.getElementById('discount_amount');
            const taxInput = document.getElementById('tax_amount');
            const shippingInput = document.getElementById('shipping_cost');
            const paidInput = document.getElementById('amount_paid');

            const summarySubtotal = document.getElementById('summary-subtotal');
            const summaryGrandTotal = document.getElementById('summary-grand-total');
            const summaryAmountDue = document.getElementById('summary-amount-due');

            // Add Product to list
            btnAddProduct.addEventListener('click', function() {
                const selectedOpt = productSelector.options[productSelector.selectedIndex];
                if (!selectedOpt || !selectedOpt.value) {
                    Swal.fire('Error', 'Please select a product first.', 'error');
                    return;
                }

                const productId = selectedOpt.value;
                const productName = selectedOpt.text.split(' (')[0];
                const productSku = selectedOpt.getAttribute('data-sku');
                const productUnit = selectedOpt.getAttribute('data-unit');
                const productUnitId = selectedOpt.getAttribute('data-unit-id');
                const productBaseUnitId = selectedOpt.getAttribute('data-base-unit-id');
                const productCost = parseFloat(selectedOpt.getAttribute('data-cost')) || 0;

                // Check if already in list
                const existingInput = document.querySelector(`.item-product-id[value="${productId}"]`);
                if (existingInput) {
                    Swal.fire('Info', 'Product is already in the purchase list. Please update its quantity directly.', 'info');
                    return;
                }

                // Remove empty row if exists
                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }

                // Calculate product's base cost
                const pUnitObj = unitMap[productUnitId];
                let baseCost = productCost;
                if (pUnitObj && pUnitObj.base_unit_id) {
                    if (pUnitObj.operator === '*') {
                        baseCost = productCost / parseFloat(pUnitObj.conversion_rate);
                    } else {
                        baseCost = productCost * parseFloat(pUnitObj.conversion_rate);
                    }
                }

                // Generate unit options
                let unitOptions = '';
                allUnits.forEach(u => {
                    // Include if it's the exact same unit, OR they share a base unit, OR one is the base unit of the other
                    if (u.id == productUnitId || u.base_unit_id == productUnitId || (productBaseUnitId && (u.id == productBaseUnitId || u.base_unit_id == productBaseUnitId))) {
                        let isSelected = (u.id == productUnitId) ? 'selected' : '';
                        unitOptions += `<option value="${u.id}" ${isSelected}>${u.short_name}</option>`;
                    }
                });
                
                // If no matching unit found, fallback to basic option
                if (!unitOptions) {
                    unitOptions = `<option value="${productUnitId || ''}" selected>${productUnit}</option>`;
                }

                // Add row
                const tr = document.createElement('tr');
                tr.setAttribute('id', `row-${rowIndex}`);
                tr.style.borderBottom = '1px solid #f1f5f9';
                tr.innerHTML = `
                    <td style="padding:12px 16px;">
                        <input type="hidden" name="items[${rowIndex}][product_id]" value="${productId}" class="item-product-id">
                        <span style="font-weight:700;color:#0f172a;display:block;">${productName}</span>
                        <span style="font-size:11px;color:#64748b;font-family:monospace;">SKU: ${productSku}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <input type="number" step="0.001" min="0.001" name="items[${rowIndex}][quantity]" value="1.000" class="form-control item-qty" style="width:90px;padding:6px;text-align:center;" required>
                            <select name="items[${rowIndex}][unit_id]" class="form-control item-unit" style="width:70px;padding:6px;font-size:13px;" required>
                                ${unitOptions}
                            </select>
                        </div>
                    </td>
                    <td style="padding:12px 16px;text-align:right;">
                        <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_cost]" value="${productCost.toFixed(2)}" class="form-control item-cost" style="width:110px;padding:6px;text-align:right;margin-left:auto;" required>
                    </td>
                    <td style="padding:12px 16px;text-align:right;font-weight:700;color:#0f172a;vertical-align:middle;">
                        ৳ <span class="item-subtotal">${productCost.toFixed(2)}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;">
                        <button type="button" class="btn-remove-row" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;" title="Remove row"><i class="bi bi-trash"></i></button>
                    </td>
                `;

                itemsTbody.appendChild(tr);

                // Bind events for row inputs
                const qtyInput = tr.querySelector('.item-qty');
                const costInput = tr.querySelector('.item-cost');
                const unitSelect = tr.querySelector('.item-unit');
                const removeBtn = tr.querySelector('.btn-remove-row');

                unitSelect.addEventListener('change', function() {
                    const selectedUnitObj = unitMap[this.value];
                    if (!selectedUnitObj) return;

                    let newCost = baseCost;
                    if (selectedUnitObj.base_unit_id) {
                        if (selectedUnitObj.operator === '*') {
                            newCost = baseCost * parseFloat(selectedUnitObj.conversion_rate);
                        } else {
                            newCost = baseCost / parseFloat(selectedUnitObj.conversion_rate);
                        }
                    }
                    
                    costInput.value = newCost.toFixed(2);
                    calculateTotals();
                });

                qtyInput.addEventListener('input', calculateTotals);
                costInput.addEventListener('input', calculateTotals);
                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    checkIfEmpty();
                    calculateTotals();
                });

                rowIndex++;
                calculateTotals();
                
                // Reset select with select2 trigger
                $('#product_selector').val('').trigger('change');
            });

            function checkIfEmpty() {
                const rows = itemsTbody.querySelectorAll('tr:not(#empty-row)');
                if (rows.length === 0) {
                    emptyRow.style.display = '';
                }
            }

            // Calculation Logic
            function calculateTotals() {
                let subtotal = 0;
                const rows = itemsTbody.querySelectorAll('tr:not(#empty-row)');

                rows.forEach(row => {
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
                    const rowSubtotal = qty * cost;
                    row.querySelector('.item-subtotal').innerText = rowSubtotal.toFixed(2);
                    subtotal += rowSubtotal;
                });

                const discount = parseFloat(discountInput.value) || 0;
                const tax = parseFloat(taxInput.value) || 0;
                const shipping = parseFloat(shippingInput.value) || 0;
                const paid = parseFloat(paidInput.value) || 0;

                const grandTotal = subtotal - discount + tax + shipping;
                const due = grandTotal - paid;

                summarySubtotal.innerText = subtotal.toFixed(2);
                summaryGrandTotal.innerText = grandTotal.toFixed(2);
                summaryAmountDue.innerText = due.toFixed(2);
            }

            // Bind calculations to modifiers inputs
            document.querySelectorAll('.calculation-input').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            // Prevent form submit if empty items list
            document.getElementById('purchase-form').addEventListener('submit', function(e) {
                const rows = itemsTbody.querySelectorAll('tr:not(#empty-row)');
                if (rows.length === 0) {
                    e.preventDefault();
                    Swal.fire('Error', 'Please add at least one product item to the list.', 'error');
                }
            });
        });
    </script>
</x-layouts.admin>
