export default function teacherSalaryManagement() {
    return {
        salaries: [],
        teachers: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingSalary: null,
        newSalary: {
            teacher_id: '',
            amount: '',
            salary_date: new Date().toISOString().split('T')[0],
            notes: ''
        },
        editSalaryData: {
            teacher_id: '',
            amount: '',
            salary_date: '',
            notes: ''
        },
        currentPage: 1,
        lastPage: 1,
        searchQuery: '',
        
        async fetchSalaries(page = 1) {
            this.loading = true;
            try {
                let url = `/api/teacher-salaries?page=${page}`;
                if (this.searchQuery) {
                    url += `&search=${encodeURIComponent(this.searchQuery)}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                this.salaries = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching salaries:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async searchSalaries() {
            this.currentPage = 1;
            await this.fetchSalaries(1);
        },
        
        async fetchTeachers() {
            try {
                const response = await fetch('/api/teachers');
                const data = await response.json();
                this.teachers = data.data;
            } catch (error) {
                console.error('Error fetching teachers:', error);
            }
        },
        
        async addSalary() {
            if (!this.newSalary.teacher_id || !this.newSalary.amount) return;
            
            try {
                const response = await fetch('/api/teacher-salaries', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newSalary)
                });
                
                if (response.ok) {
                    this.newSalary = {
                        teacher_id: '',
                        amount: '',
                        salary_date: new Date().toISOString().split('T')[0],
                        notes: ''
                    };
                    this.showAddModal = false;
                    await this.fetchSalaries(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding salary:', error);
            }
        },
        
        async editSalary(salary) {
            this.editingSalary = salary;
            this.editSalaryData = {
                teacher_id: salary.teacher_id,
                amount: salary.amount,
                salary_date: salary.salary_date || new Date().toISOString().split('T')[0],
                notes: salary.notes
            };
            this.showEditModal = true;
        },
        
        async updateSalary() {
            if (!this.editSalaryData.teacher_id || !this.editSalaryData.amount || !this.editingSalary) return;
            
            try {
                const response = await fetch(`/api/teacher-salaries/${this.editingSalary.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editSalaryData)
                });
                
                if (response.ok) {
                    this.editSalaryData = {
                        teacher_id: '',
                        amount: '',
                        salary_date: new Date().toISOString().split('T')[0],
                        notes: ''
                    };
                    this.editingSalary = null;
                    this.showEditModal = false;
                    await this.fetchSalaries(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating salary:', error);
            }
        },
        
        async deleteSalary(id) {
            if (!confirm('Are you sure you want to delete this salary?')) return;
            
            try {
                const response = await fetch(`/api/teacher-salaries/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchSalaries(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting salary:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchSalaries(page);
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },
        
        formatAmount(amount) {
            return parseFloat(amount).toFixed(2);
        },
        
        getTeacherName(teacherId) {
            const teacher = this.teachers.find(t => t.id === teacherId);
            return teacher ? teacher.name : 'Unknown';
        },
        
        init() {
            this.fetchSalaries();
            this.fetchTeachers();
        }
    };
}