export default function expenseManagement() {
    return {
        expenses: [],
        categories: [],
        loading: false,
        categoryLoading: false,
        showAddModal: false,
        showEditModal: false,
        showCategoryModal: false,
        showEditCategoryModal: false,
        editingExpense: null,
        editingCategory: null,
        activeTab: 'expenses',
        newCategoryName: '',
        editCategoryName: '',
        newExpense: {
            amount: '',
            expense_date: new Date().toISOString().split('T')[0],
            category_id: '',
            notes: ''
        },
        editExpenseData: {
            amount: '',
            expense_date: '',
            category_id: '',
            notes: ''
        },
        currentPage: 1,
        lastPage: 1,
        
        async fetchExpenses(page = 1) {
            this.loading = true;
            try {
                const response = await fetch(`/api/expenses?page=${page}`);
                const data = await response.json();
                this.expenses = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching expenses:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async fetchCategories() {
            this.categoryLoading = true;
            try {
                const response = await fetch('/api/expense-categories');
                const data = await response.json();
                this.categories = data;
            } catch (error) {
                console.error('Error fetching categories:', error);
            } finally {
                this.categoryLoading = false;
            }
        },
        
        setTab(tab) {
            this.activeTab = tab;
        },
        
        async addExpense() {
            if (!this.newExpense.amount) return;
            
            try {
                const response = await fetch('/api/expenses', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newExpense)
                });
                
                if (response.ok) {
                    this.newExpense = {
                        amount: '',
                        expense_date: new Date().toISOString().split('T')[0],
                        category_id: '',
                        notes: ''
                    };
                    this.showAddModal = false;
                    await this.fetchExpenses(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding expense:', error);
            }
        },
        
        async editExpense(expense) {
            this.editingExpense = expense;
            this.editExpenseData = {
                amount: expense.amount,
                expense_date: expense.expense_date || new Date().toISOString().split('T')[0],
                category_id: expense.category_id,
                notes: expense.notes
            };
            this.showEditModal = true;
        },
        
        async updateExpense() {
            if (!this.editExpenseData.amount || !this.editingExpense) return;
            
            try {
                const response = await fetch(`/api/expenses/${this.editingExpense.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editExpenseData)
                });
                
                if (response.ok) {
                    this.editExpenseData = {
                        amount: '',
                        expense_date: new Date().toISOString().split('T')[0],
                        category_id: '',
                        notes: ''
                    };
                    this.editingExpense = null;
                    this.showEditModal = false;
                    await this.fetchExpenses(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating expense:', error);
            }
        },
        
        async deleteExpense(id) {
            if (!confirm('Are you sure you want to delete this expense?')) return;
            
            try {
                const response = await fetch(`/api/expenses/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchExpenses(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting expense:', error);
            }
        },
        
        async addCategory() {
            if (!this.newCategoryName.trim()) return;
            
            try {
                const response = await fetch('/api/expense-categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.newCategoryName })
                });
                
                if (response.ok) {
                    this.newCategoryName = '';
                    this.showCategoryModal = false;
                    await this.fetchCategories();
                }
            } catch (error) {
                console.error('Error adding category:', error);
            }
        },
        
        async deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) return;
            
            try {
                const response = await fetch(`/api/expense-categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchCategories();
                }
            } catch (error) {
                console.error('Error deleting category:', error);
            }
        },
        
        editCategory(category) {
            this.editingCategory = category;
            this.editCategoryName = category.name;
            this.showEditCategoryModal = true;
        },
        
        async updateCategory() {
            if (!this.editCategoryName.trim() || !this.editingCategory) return;
            
            try {
                const response = await fetch(`/api/expense-categories/${this.editingCategory.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.editCategoryName })
                });
                
                if (response.ok) {
                    this.editCategoryName = '';
                    this.editingCategory = null;
                    this.showEditCategoryModal = false;
                    await this.fetchCategories();
                }
            } catch (error) {
                console.error('Error updating category:', error);
            }
        },
        
        getCategoryExpenseCount(categoryId) {
            return this.expenses.filter(expense => expense.category_id === categoryId).length;
        },
        
        async goToPage(page) {
            await this.fetchExpenses(page);
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },
        
        formatAmount(amount) {
            return parseFloat(amount).toFixed(2);
        },
        
        init() {
            this.fetchExpenses();
            this.fetchCategories();
        }
    };
}
