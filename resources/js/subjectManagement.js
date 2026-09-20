export default function subjectManagement() {
    return {
        subjects: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingSubject: null,
        newSubjectName: '',
        editSubjectName: '',
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
            if (!this.newSubjectName.trim()) return;
            
            try {
                const response = await fetch('/api/subjects', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.newSubjectName })
                });
                
                if (response.ok) {
                    this.newSubjectName = '';
                    this.showAddModal = false;
                    await this.fetchSubjects(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding subject:', error);
            }
        },
        
        async editSubject(subject) {
            this.editingSubject = subject;
            this.editSubjectName = subject.name;
            this.showEditModal = true;
        },
        
        async updateSubject() {
            if (!this.editSubjectName.trim() || !this.editingSubject) return;
            
            try {
                const response = await fetch(`/api/subjects/${this.editingSubject.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.editSubjectName })
                });
                
                if (response.ok) {
                    this.editSubjectName = '';
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