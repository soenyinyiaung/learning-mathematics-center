export default function saleItemManagement() {
    return {
        saleItems: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingItem: null,
        newItem: {
            name: '',
            qty: 0,
            amount: 0
        },
        editItemData: {
            name: '',
            qty: 0,
            amount: 0
        },
        cart: [],
        cartTotalItems: 0,
        cartTotalPrice: 0,
        isSidebarOpen: false,
        
        init() {
            // Get sidebar state from Alpine store
            const sidebarState = Alpine.store('sidebar');
            if (sidebarState) {
                this.isSidebarOpen = !sidebarState.hidden;
            }
            
            // Watch for sidebar changes
            this.$watch('$store.sidebar.hidden', (value) => {
                this.isSidebarOpen = !value;
            });
            
            this.fetchSaleItems();
        },
        
        async fetchSaleItems() {
            this.loading = true;
            try {
                const response = await fetch('/api/sale-items');
                const data = await response.json();
                this.saleItems = data;
            } catch (error) {
                console.error('Error fetching sale items:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async addItem() {
            if (!this.newItem.name.trim() || this.newItem.qty < 0 || this.newItem.amount < 0) return;
            
            try {
                const response = await fetch('/api/sale-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newItem)
                });
                
                if (response.ok) {
                    this.newItem = {
                        name: '',
                        qty: 0,
                        amount: 0
                    };
                    this.showAddModal = false;
                    await this.fetchSaleItems();
                }
            } catch (error) {
                console.error('Error adding sale item:', error);
            }
        },
        
        async editItem(item) {
            this.editingItem = item;
            this.editItemData = {
                name: item.name,
                qty: item.qty,
                amount: item.amount
            };
            this.showEditModal = true;
        },
        
        async updateItem() {
            if (!this.editItemData.name.trim() || this.editItemData.qty < 0 || this.editItemData.amount < 0 || !this.editingItem) return;
            
            try {
                const response = await fetch(`/api/sale-items/${this.editingItem.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editItemData)
                });
                
                if (response.ok) {
                    this.editItemData = {
                        name: '',
                        qty: 0,
                        amount: 0
                    };
                    this.editingItem = null;
                    this.showEditModal = false;
                    await this.fetchSaleItems();
                }
            } catch (error) {
                console.error('Error updating sale item:', error);
            }
        },
        
        async deleteItem(id) {
            if (!confirm('Are you sure you want to delete this sale item?')) return;
            
            try {
                const response = await fetch(`/api/sale-items/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchSaleItems();
                }
            } catch (error) {
                console.error('Error deleting sale item:', error);
            }
        },
        
        addToCart(item) {
            const qtyInput = document.getElementById('qty-' + item.id);
            const qty = parseInt(qtyInput.value);
            
            // Get the current displayed quantity
            const saleItem = this.saleItems.find(saleItem => saleItem.id === item.id);
            if (!saleItem || saleItem.qty < qty) return;
            
            const existingItem = this.cart.find(cartItem => cartItem.id === item.id);
            
            if (existingItem) {
                existingItem.qty += qty;
            } else {
                this.cart.push({
                    id: item.id,
                    name: item.name,
                    qty: qty,
                    amount: item.amount
                });
            }
            
            // Decrease the displayed available quantity
            saleItem.qty -= qty;
            
            // Update computed properties
            this.$nextTick(() => {
                this.cartTotalItems = this.cart.reduce((total, item) => total + item.qty, 0);
                this.cartTotalPrice = this.cart.reduce((total, item) => total + (parseFloat(item.amount) * item.qty), 0);
            });
            
            qtyInput.value = 1;
        },
        
        removeFromCart(index) {
            const cartItem = this.cart[index];
            
            // Restore the available quantity
            const saleItem = this.saleItems.find(saleItem => saleItem.id === cartItem.id);
            if (saleItem) {
                saleItem.qty += cartItem.qty;
            }
            
            this.cart.splice(index, 1);
            
            // Update computed properties
            this.$nextTick(() => {
                this.cartTotalItems = this.cart.reduce((total, item) => total + item.qty, 0);
                this.cartTotalPrice = this.cart.reduce((total, item) => total + (parseFloat(item.amount) * item.qty), 0);
            });
        },
        
        updateCartQty(index, change) {
            const cartItem = this.cart[index];
            const newQty = cartItem.qty + change;
            
            const saleItem = this.saleItems.find(saleItem => saleItem.id === cartItem.id);
            if (!saleItem) return;
            
            // Check if we have enough available quantity
            if (change > 0 && saleItem.qty <= 0) return;
            
            if (newQty > 0) {
                cartItem.qty = newQty;
                saleItem.qty -= change;
                
                // Update computed properties
                this.$nextTick(() => {
                    this.cartTotalItems = this.cart.reduce((total, item) => total + item.qty, 0);
                    this.cartTotalPrice = this.cart.reduce((total, item) => total + (parseFloat(item.amount) * item.qty), 0);
                });
            }
        },
        
        async checkout() {
            if (this.cart.length === 0) return;
            
            try {
                // Save cart to localStorage for voucher page
                localStorage.setItem('cart', JSON.stringify(this.cart));
                
                // Redirect to voucher page
                window.location.href = '/admin/sales/checkout';
            } catch (error) {
                console.error('Error during checkout:', error);
                alert('Checkout failed. Please try again.');
            }
        }
    };
}