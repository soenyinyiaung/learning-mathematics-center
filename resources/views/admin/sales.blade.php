@extends('admin.layout')

@section('content')
<div x-cloak x-data="saleItemManagement()" x-init="init()" class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sale Items Grid -->
        <div class="lg:col-span-3 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Sale Items</h3>
                <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New Item
                </button>
            </div>
            
            <div class="p-6">
                <div x-show="loading" class="text-center py-8 text-gray-500">
                    Loading...
                </div>
                
                <div x-show="!loading" :class="isSidebarOpen ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4'">
                    <template x-for="item in saleItems" :key="item.id">
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="font-semibold text-gray-800 text-lg" x-text="item.name"></h4>
                                <div class="flex gap-2">
                                    <button @click="editItem(item)" class="text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-600">Price:</span>
                                <span class="text-xl font-bold text-green-600" x-text="'$' + parseFloat(item.amount).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2})"></span>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-600">Available:</span>
                                <span class="text-2xl font-bold text-indigo-600" x-text="item.qty"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" :id="'qty-' + item.id" min="1" :max="item.qty" :disabled="item.qty === 0" value="1" @input="$el.value = Math.min(parseInt($el.value) || 1, item.qty)" class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <button @click="addToCart(item)" :disabled="item.qty === 0" class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-2 px-4 rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </template>
                    <div x-show="saleItems.length === 0" class="col-span-full text-center py-8 text-gray-500">
                        No sale items found
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Shopping Cart -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart"></i>
                    Shopping Cart
                </h3>
            </div>
            
            <div class="p-6">
                <div x-show="cart.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-shopping-cart text-4xl mb-3 text-gray-300"></i>
                    <p>Your cart is empty</p>
                </div>
                
                <div x-show="cart.length > 0" class="space-y-4">
                    <template x-for="(cartItem, index) in cart" :key="index">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-medium text-gray-800" x-text="cartItem.name"></h4>
                                <button @click="removeFromCart(index)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Price:</span>
                                <span class="font-semibold text-green-600" x-text="'$' + parseFloat(cartItem.amount).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2})"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button @click="updateCartQty(index, -1)" class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center text-gray-700">-</button>
                                    <span class="w-8 text-center font-medium" x-text="cartItem.qty"></span>
                                    <button @click="updateCartQty(index, 1)" class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded flex items-center justify-center text-gray-700">+</button>
                                </div>
                                <span class="font-semibold text-indigo-600" x-text="'$' + (parseFloat(cartItem.amount) * cartItem.qty).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2})"></span>
                            </div>
                        </div>
                    </template>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Total Items:</span>
                            <span class="font-bold text-gray-800" x-text="cartTotalItems"></span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600">Total Price:</span>
                            <span class="font-bold text-green-600" x-text="'$' + cartTotalPrice.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2})"></span>
                        </div>
                        <button @click="checkout()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add New Sale Item</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" x-model="newItem.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter item name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                    <input type="number" x-model="newItem.amount" min="0" step="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter price">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" x-model="newItem.qty" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter quantity">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newItem = {name: '', qty: 0, amount: 0}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addItem()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Item</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Sale Item</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" x-model="editItemData.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter item name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                    <input type="number" x-model="editItemData.amount" min="0" step="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter price">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" x-model="editItemData.qty" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter quantity">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editItemData = {name: '', qty: 0, amount: 0}; editingItem = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateItem()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Item</button>
            </div>
        </div>
    </div>
</div>
@endsection