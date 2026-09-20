export default function userManagement() {
    return {
        users: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingUser: null,
        newUser: {
            name: '',
            email: '',
            phone: '',
            password: ''
        },
        editUserData: {
            name: '',
            email: '',
            phone: '',
            password: ''
        },
        showNewPassword: false,
        showEditPassword: false,
        currentPage: 1,
        lastPage: 1,
        
        async fetchUsers(page = 1) {
            this.loading = true;
            try {
                const response = await fetch(`/api/users?page=${page}`);
                const data = await response.json();
                this.users = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching users:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async addUser() {
            if (!this.newUser.name.trim() || !this.newUser.email.trim() || !this.newUser.password) return;
            
            try {
                const response = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newUser)
                });
                
                if (response.ok) {
                    this.newUser = {
                        name: '',
                        email: '',
                        phone: '',
                        password: ''
                    };
                    this.showAddModal = false;
                    await this.fetchUsers(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding user:', error);
            }
        },
        
        async editUser(user) {
            this.editingUser = user;
            this.editUserData = {
                name: user.name,
                email: user.email,
                phone: user.phone,
                password: ''
            };
            this.showEditModal = true;
        },
        
        async updateUser() {
            if (!this.editUserData.name.trim() || !this.editUserData.email.trim() || !this.editingUser) return;
            
            try {
                const response = await fetch(`/api/users/${this.editingUser.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editUserData)
                });
                
                if (response.ok) {
                    this.editUserData = {
                        name: '',
                        email: '',
                        phone: '',
                        password: ''
                    };
                    this.editingUser = null;
                    this.showEditModal = false;
                    await this.fetchUsers(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating user:', error);
            }
        },
        
        async deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;
            
            try {
                const response = await fetch(`/api/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchUsers(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting user:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchUsers(page);
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },
        
        generatePassword() {
            const length = 12;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
            let password = '';
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            return password;
        },
        
        generateAndSetPassword(type) {
            const password = this.generatePassword();
            if (type === 'new') {
                this.newUser.password = password;
            } else if (type === 'edit') {
                this.editUserData.password = password;
            }
        },
        
        init() {
            this.fetchUsers();
        }
    };
}