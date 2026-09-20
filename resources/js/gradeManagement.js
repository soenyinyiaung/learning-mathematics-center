export default function gradeManagement() {
    return {
        grades: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingGrade: null,
        newGradeName: '',
        editGradeName: '',
        currentPage: 1,
        lastPage: 1,
        
        async fetchGrades(page = 1) {
            this.loading = true;
            try {
                const response = await fetch(`/api/grades?page=${page}`);
                const data = await response.json();
                this.grades = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching grades:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async addGrade() {
            if (!this.newGradeName.trim()) return;
            
            try {
                const response = await fetch('/api/grades', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.newGradeName })
                });
                
                if (response.ok) {
                    this.newGradeName = '';
                    this.showAddModal = false;
                    await this.fetchGrades(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding grade:', error);
            }
        },
        
        async editGrade(grade) {
            this.editingGrade = grade;
            this.editGradeName = grade.name;
            this.showEditModal = true;
        },
        
        async updateGrade() {
            if (!this.editGradeName.trim() || !this.editingGrade) return;
            
            try {
                const response = await fetch(`/api/grades/${this.editingGrade.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.editGradeName })
                });
                
                if (response.ok) {
                    this.editGradeName = '';
                    this.editingGrade = null;
                    this.showEditModal = false;
                    await this.fetchGrades(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating grade:', error);
            }
        },
        
        async deleteGrade(id) {
            if (!confirm('Are you sure you want to delete this grade?')) return;
            
            try {
                const response = await fetch(`/api/grades/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchGrades(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting grade:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchGrades(page);
        },
        
        init() {
            this.fetchGrades();
        }
    };
}