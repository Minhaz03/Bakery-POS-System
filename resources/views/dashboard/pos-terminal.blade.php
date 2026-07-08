<x-layouts.admin title="POS Terminal">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            top: 1px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            font-size: 14px;
            padding-left: 13px !important;
        }
    </style>

    <!-- POS Interface with AlpineJS -->
    <div x-data="posApp()">
        <div style="display:grid;grid-template-columns:1fr 450px;gap:20px;height:calc(100vh - 120px);">

        <!-- Left Column: Products Grid -->
        <div style="display:flex;flex-direction:column;gap:16px;height:100%;min-height:0;">
            <!-- Header Controls -->
            <div class="card" style="padding:16px 20px;display:flex;gap:12px;align-items:center;flex-shrink:0;">
                <div style="flex:1;position:relative;">
                    <input type="text" x-model="searchQuery" class="form-control" placeholder="Search menu items..." style="padding-left:36px;">
                    <i class="bi bi-search" style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <div style="display:flex;gap:6px;overflow-x:auto;">
                    <button class="btn btn-sm" :class="activeCategory === 'All' ? 'btn-primary' : 'btn-outline'" @click="activeCategory = 'All'">All</button>
                    @foreach($categories as $category)
                        <button class="btn btn-sm" :class="activeCategory === '{{ $category }}' ? 'btn-primary' : 'btn-outline'" @click="activeCategory = '{{ $category }}'">{{ $category }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Items Catalog -->
            <div style="flex:1;overflow-y:auto;min-height:0;padding-bottom:12px;">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;">
                    <template x-for="item in filteredItems()" :key="item.id">
                        <div class="card" @click="addToCart(item)" style="cursor:pointer;transition:all 0.15s;border-radius:10px;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
                            <div style="padding:18px;text-align:center;background:#fafafa;border-bottom:1px solid #f1f5f9;">
                                <div style="font-size:32px;color:var(--primary);margin-bottom:8px;">
                                    <i :class="'bi ' + item.image"></i>
                                </div>
                                <div style="font-size:13.5px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" x-text="item.name"></div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;" x-text="item.category"></div>
                            </div>
                            <div style="padding:10px 14px;display:flex;justify-content:space-between;align-items:center;background:#fff;">
                                <span style="font-weight:800;color:var(--primary);font-size:13.5px;">৳<span x-text="item.price"></span></span>
                                <span style="font-size:10px;padding:1px 6px;border-radius:999px;font-weight:600;" :class="item.stock <= 5 ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-800'">
                                    Stock: <span x-text="item.stock"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right Column: Cart Panel -->
        <div class="card" style="display:flex;flex-direction:column;height:100%;min-height:0;background:#fff;border-radius:12px;">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
                <span class="card-title"><i class="bi bi-cart3" style="color:var(--primary);"></i> Current Cart</span>
                <button class="btn btn-outline btn-sm" @click="clearCart()" style="margin-left:auto;padding:4px 8px;font-size:11px;border-color:#fee2e2;color:#b91c1c;">Clear</button>
            </div>

            <!-- Customer & Date Selection -->
            <div style="padding:16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;gap:12px;">
                <div style="flex:1;display:flex;gap:6px;min-width:0;">
                    <div style="flex:1; min-width:0;" x-init="
                        $nextTick(() => {
                            $($refs.customerSelect).select2({
                                width: '100%'
                            }).on('select2:select', (e) => {
                                customerId = e.params.data.id;
                            }).on('select2:unselect', (e) => {
                                customerId = '';
                            });
                        })
                    ">
                        <select x-ref="customerSelect" class="form-control">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-outline" style="padding:0 12px;height:38px;border-radius:8px;flex-shrink:0;" @click="showCustomerModal = true" title="Add Customer">
                        <i class="bi bi-person-plus"></i>
                    </button>
                </div>
                <input type="text" x-ref="datePicker" x-model="saleDate" class="form-control" style="width:130px;height:38px;background:#fff;flex-shrink:0;" placeholder="Date">
            </div>

            <!-- Cart Items List -->
            <div style="flex:1;overflow-y:auto;padding:12px;min-height:0;border-bottom:1px solid #f1f5f9;">
                <template x-if="cart.length === 0">
                    <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                        <i class="bi bi-cart-x" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                        <span style="font-size:13px;">Cart is empty. Click items on the left to add.</span>
                    </div>
                </template>

                <div style="display:flex;flex-direction:column;gap:8px;">
                    <template x-for="cartItem in cart" :key="cartItem.id">
                        <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;background:#f8fafc;border-radius:8px;border:1px solid #f1f5f9;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12.5px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" x-text="cartItem.name"></div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">৳<span x-text="cartItem.price"></span> / unit</div>
                            </div>
                            <!-- Qty Controls -->
                            <div style="display:flex;align-items:center;gap:6px;">
                                <button @click="updateQty(cartItem.id, -1)" style="width:22px;height:22px;border-radius:4px;border:1px solid #cbd5e1;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;">-</button>
                                <span style="font-size:12.5px;font-weight:700;width:18px;text-align:center;" x-text="cartItem.qty"></span>
                                <button @click="updateQty(cartItem.id, 1)" style="width:22px;height:22px;border-radius:4px;border:1px solid #cbd5e1;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;">+</button>
                            </div>
                            <!-- Price -->
                            <div style="font-weight:800;color:#0f172a;font-size:12.5px;width:60px;text-align:right;">
                                ৳<span x-text="cartItem.price * cartItem.qty"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Pricing Calculation -->
            <div style="padding:18px;background:#f8fafc;border-top:1px solid #e2e8f0;flex-shrink:0;display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;">
                    <span>Subtotal</span>
                    <span style="margin-left:auto;font-weight:600;color:#0f172a;">৳<span x-text="subtotal()"></span></span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;align-items:center;">
                    <span>Discount (৳)</span>
                    <input type="number" x-model.number="discount" min="0" class="form-control" style="width:70px;padding:2px 6px;font-size:12px;height:26px;text-align:right;margin-left:auto;">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;">
                    <span>VAT / Tax (5%)</span>
                    <span style="margin-left:auto;font-weight:600;color:#0f172a;">৳<span x-text="tax()"></span></span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;align-items:center;">
                    <span>Payment Method</span>
                    <select x-model="paymentMethod" class="form-control" style="width:120px;padding:2px 6px;font-size:12px;height:26px;margin-left:auto;">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_pay">Mobile Pay</option>
                        <option value="credit">Credit (Unpaid)</option>
                    </select>
                </div>
                <div x-show="paymentMethod !== 'credit'">
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;align-items:center;">
                        <span>Amount Paid (৳)</span>
                        <input type="number" x-model.number="amountTendered" min="0" class="form-control" style="width:70px;padding:2px 6px;font-size:12px;height:26px;text-align:right;">
                    </div>
                </div>
                <div x-show="paymentMethod !== 'credit' && amountTendered > total()">
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;align-items:center;">
                        <span>Change Return</span>
                        <span style="font-weight:600;color:#16a34a;">৳<span x-text="Math.max(0, amountTendered - total())"></span></span>
                    </div>
                </div>
                <div x-show="paymentMethod !== 'credit' && amountTendered < total()">
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;color:#475569;align-items:center;">
                        <span>Due Balance</span>
                        <span style="font-weight:600;color:#dc2626;">৳<span x-text="Math.max(0, total() - amountTendered)"></span></span>
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:800;color:#0f172a;border-top:1px dashed #cbd5e1;padding-top:10px;margin-top:4px;">
                    <span>Total Bill</span>
                    <span style="margin-left:auto;color:var(--primary);">৳<span x-text="total()"></span></span>
                </div>

                <button class="btn btn-primary" @click="checkout()" :disabled="cart.length === 0 || processing" style="width:100%;margin-top:12px;justify-content:center;height:44px;font-size:14px;">
                    <i class="bi bi-wallet2"></i> <span x-text="processing ? 'Processing...' : 'Pay & Print Receipt'"></span>
                </button>
            </div>
        </div>

    </div>

    <!-- Add Customer Modal Overlay -->
    <div x-show="showCustomerModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;">
        <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:20px;">
            <div class="card" @click.outside="showCustomerModal = false" style="width:100%;max-width:400px;box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <div class="card-header" style="justify-content:space-between;padding:16px 20px;">
                <span style="font-weight:700;font-size:16px;">New Customer</span>
                <button @click="showCustomerModal = false" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;"><i class="bi bi-x"></i></button>
            </div>
            <div class="card-body" style="padding:20px;">
                <div class="form-group">
                    <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" x-model="newCustomer.name" class="form-control" placeholder="e.g. John Doe">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Phone</label>
                    <input type="text" x-model="newCustomer.phone" class="form-control" placeholder="e.g. 01700000000">
                </div>
            </div>
            <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
                <button class="btn btn-outline" @click="showCustomerModal = false">Cancel</button>
                <button class="btn btn-primary" @click="saveCustomer()" :disabled="savingCustomer || !newCustomer.name">
                    <span x-text="savingCustomer ? 'Saving...' : 'Save Customer'"></span>
                </button>
            </div>
            </div>
        </div>
    </div>
    <!-- End POS Wrapper -->
    </div>

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

                showCustomerModal: false,
                savingCustomer: false,
                newCustomer: { name: '', phone: '' },

                init() {
                    flatpickr(this.$refs.datePicker, {
                        defaultDate: this.saleDate,
                        dateFormat: "Y-m-d",
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
                        const matchesCategory = this.activeCategory === 'All' || item.category === this.activeCategory;
                        return matchesSearch && matchesCategory;
                    });
                },

                addToCart(item) {
                    const existing = this.cart.find(c => c.id === item.id);
                    if (existing) {
                        if (existing.qty < item.stock) {
                            existing.qty++;
                        } else {
                            Swal.fire('Stock Limit Reached', 'Cannot exceed available stock of ' + item.stock + ' items.', 'warning');
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
                            Swal.fire('Out of Stock', 'This product is currently out of stock.', 'error');
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
                            Swal.fire('Stock Limit Reached', 'Cannot exceed available stock.', 'warning');
                        }
                    }
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

                async checkout() {
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
                            Swal.fire({
                                title: 'Order Placed Successfully!',
                                html: `<div style="font-size: 15px; margin-top: 8px;">Invoice: <strong>${data.invoice_no}</strong></div><div style="font-size: 15px; margin-top: 8px;">Total Bill: <strong style="color: #16a34a; font-size: 18px;">৳${totalBill}</strong></div><div style="font-size: 13px; color: #64748b; margin-top: 6px;"><i class="bi bi-printer"></i> Receipt printed.</div>`,
                                icon: 'success',
                                confirmButtonColor: '#6366f1',
                                confirmButtonText: 'OK'
                            });
                            this.clearCart();
                            // Optional: Update items stock based on checkout
                            this.cart.forEach(cartItem => {
                                let item = this.items.find(i => i.id === cartItem.id);
                                if (item) item.stock -= cartItem.qty;
                            });
                        } else {
                            throw new Error(data.message || 'Validation error');
                        }
                    } catch (error) {
                        Swal.fire('Error', error.message || 'Something went wrong processing the checkout.', 'error');
                    } finally {
                        this.processing = false;
                    }
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
                            
                            // Append new option to select2 and select it
                            const newOption = new Option(`${data.customer.name} (${data.customer.phone || 'No phone'})`, data.customer.id, true, true);
                            $(this.$refs.customerSelect).append(newOption).trigger('change');
                            
                            this.showCustomerModal = false;
                            this.newCustomer = { name: '', phone: '' };
                            
                            // Simple toast notification
                            const toastContainer = document.getElementById('toastContainer');
                            if (toastContainer) {
                                const toast = document.createElement('div');
                                toast.className = 'toast-msg success';
                                toast.innerHTML = '<i class="bi bi-check-circle-fill"></i> Customer created successfully!';
                                toastContainer.appendChild(toast);
                                setTimeout(() => { toast.style.opacity='0'; toast.style.transform='translateX(20px)'; toast.style.transition='0.4s'; setTimeout(() => toast.remove(), 400); }, 4000);
                            }
                        } else {
                            throw new Error(data.message || 'Failed to create customer');
                        }
                    } catch (error) {
                        Swal.fire('Error', error.message, 'error');
                    } finally {
                        this.savingCustomer = false;
                    }
                }
            }
        }
    </script>

</x-layouts.admin>
