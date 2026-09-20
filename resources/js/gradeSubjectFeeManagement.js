export default function gradeSubjectFeeManagement() {
    return {
        fees: [],
        grades: [],
        subjects: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingFee: null,
        newFee: {
            grade_id: '',
            subject_id: '',
            fee: ''
        },
        editFeeData: {
            grade_id: '',
            subject_id: '',
            fee: ''
        },
        currentPage: 1,
        lastPage: 1,
        filterGrade: '',
        filterSubject: '',
        
        async fetchFees(page = 1) {
            this.loading = true;
            try {
                let url = `/api/grade-subject-fees?page=${page}`;
                
                // If both filters are empty, add order by grade name
                if (!this.filterGrade && !this.filterSubject) {
                    url += '&sort=grade_name';
                }
                
                const response = await fetch(url);
                const data = await response.json();
                this.fees = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching fees:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async fetchGrades() {
            try {
                const response = await fetch('/api/grades');
                const data = await response.json();
                this.grades = data.data || data;
            } catch (error) {
                console.error('Error fetching grades:', error);
            }
        },
        
        async fetchSubjects() {
            try {
                const response = await fetch('/api/subjects');
                const data = await response.json();
                this.subjects = data.data || data;
            } catch (error) {
                console.error('Error fetching subjects:', error);
            }
        },
        
        async filterFees() {
            this.loading = true;
            try {
                let url = '/api/grade-subject-fees';
                const params = new URLSearchParams();
                
                if (this.filterGrade) {
                    params.append('grade_id', this.filterGrade);
                }
                
                if (this.filterSubject) {
                    params.append('subject_id', this.filterSubject);
                }
                
                // If both filters are empty, sort by grade name
                if (!this.filterGrade && !this.filterSubject) {
                    params.append('sort', 'grade_name');
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                
                const response = await fetch(url);
                const data = await response.json();
                this.fees = data.data || [];
                this.currentPage = data.current_page || 1;
                this.lastPage = data.last_page || 1;
            } catch (error) {
                console.error('Error filtering fees:', error);
                this.fees = [];
            } finally {
                this.loading = false;
            }
        },
        
        async addFee() {
            if (!this.newFee.grade_id || !this.newFee.subject_id || !this.newFee.fee) return;
            
            try {
                const response = await fetch('/api/grade-subject-fees', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newFee)
                });
                
                if (response.ok) {
                    this.newFee = { grade_id: '', subject_id: '', fee: '' };
                    this.showAddModal = false;
                    if (this.filterGrade || this.filterSubject) {
                        await this.filterFees();
                    } else {
                        await this.fetchFees(this.currentPage);
                    }
                }
            } catch (error) {
                console.error('Error adding fee:', error);
            }
        },
        
        async editFee(fee) {
            this.editingFee = fee;
            this.editFeeData = {
                grade_id: fee.grade_id,
                subject_id: fee.subject_id,
                fee: fee.fee
            };
            this.showEditModal = true;
        },
        
        async updateFee() {
            if (!this.editFeeData.grade_id || !this.editFeeData.subject_id || !this.editFeeData.fee || !this.editingFee) return;
            
            try {
                const response = await fetch(`/api/grade-subject-fees/${this.editingFee.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editFeeData)
                });
                
                if (response.ok) {
                    this.editFeeData = { grade_id: '', subject_id: '', fee: '' };
                    this.editingFee = null;
                    this.showEditModal = false;
                    if (this.filterGrade || this.filterSubject) {
                        await this.filterFees();
                    } else {
                        await this.fetchFees(this.currentPage);
                    }
                }
            } catch (error) {
                console.error('Error updating fee:', error);
            }
        },
        
        async deleteFee(id) {
            if (!confirm('Are you sure you want to delete this fee?')) return;
            
            try {
                const response = await fetch(`/api/grade-subject-fees/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    if (this.filterGrade || this.filterSubject) {
                        await this.filterFees();
                    } else {
                        await this.fetchFees(this.currentPage);
                    }
                }
            } catch (error) {
                console.error('Error deleting fee:', error);
            }
        },
        
        async goToPage(page) {
            if (this.filterGrade || this.filterSubject) {
                await this.filterFees();
            } else {
                await this.fetchFees(page);
            }
        },
        
        init() {
            this.fetchFees();
            this.fetchGrades();
            this.fetchSubjects();
        }
    };
}