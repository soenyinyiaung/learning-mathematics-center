export default function subjectManagement() {
    return {
        subjects: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingSubject: null,
        newSubject: {
            name: ''
        },
        editSubjectData: {
            name: ''
        },
        currentPage: 1,
        lastPage: 1,
        
        async fetchSubjects(page = 1) {
            this.loading = true;
            try {
                const response = await fetch(`/api/subjects?page=${page}`);
                const data = await response.json();
                this.subjects = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching subjects:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async addSubject() {
            if (!this.newSubject.name.trim()) return;
            
            try {
                const response = await fetch('/api/subjects', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newSubject)
                });
                
                if (response.ok) {
                    this.newSubject = { name: '' };
                    this.showAddModal = false;
                    await this.fetchSubjects(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding subject:', error);
            }
        },
        
        async editSubject(subject) {
            this.editingSubject = subject;
            this.editSubjectData = {
                name: subject.name
            };
            this.showEditModal = true;
        },
        
        async updateSubject() {
            if (!this.editSubjectData.name.trim() || !this.editingSubject) return;
            
            try {
                const response = await fetch(`/api/subjects/${this.editingSubject.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editSubjectData)
                });
                
                if (response.ok) {
                    this.editSubjectData = { name: '' };
                    this.editingSubject = null;
                    this.showEditModal = false;
                    await this.fetchSubjects(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating subject:', error);
            }
        },
        
        async deleteSubject(id) {
            if (!confirm('Are you sure you want to delete this subject?')) return;
            
            try {
                const response = await fetch(`/api/subjects/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchSubjects(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting subject:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchSubjects(page);
        },
        
        init() {
            this.fetchSubjects();
        }
    };
}