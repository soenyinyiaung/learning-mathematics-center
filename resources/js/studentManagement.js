export default function studentManagement() {
    return {
        students: [],
        grades: [],
        subjects: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingStudent: null,
        activeTab: 'active',
        newStudent: {
            name: '',
            phone: '',
            birthday: '',
            nrc_id: '',
            guardian_name: '',
            guardian_contact: '',
            subject_ids: []
        },
        editStudentData: {
            name: '',
            phone: '',
            birthday: '',
            nrc_id: '',
            guardian_name: '',
            guardian_contact: '',
            status: true,
            subject_ids: []
        },
        currentPage: 1,
        lastPage: 1,
        searchQuery: '',
        
        async fetchStudents(page = 1) {
            this.loading = true;
            try {
                let url = `/api/students?page=${page}`;
                if (this.searchQuery) {
                    url += `&search=${encodeURIComponent(this.searchQuery)}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                this.students = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching students:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async searchStudents() {
            this.currentPage = 1;
            await this.fetchStudents(1);
        },
        
        setTab(tab) {
            this.activeTab = tab;
        },
        
        get filteredStudents() {
            if (this.activeTab === 'active') {
                return this.students.filter(student => student.status === true);
            } else {
                return this.students.filter(student => student.status === false);
            }
        },
        
        async fetchGrades() {
            try {
                const response = await fetch('/api/grades');
                const data = await response.json();
                this.grades = data.data;
            } catch (error) {
                console.error('Error fetching grades:', error);
            }
        },

        async fetchSubjects() {
            try {
                const response = await fetch('/api/subjects');
                const data = await response.json();
                this.subjects = data.data;
            } catch (error) {
                console.error('Error fetching subjects:', error);
            }
        },
        
        async addStudent() {
            if (!this.newStudent.name.trim()) return;
            
            try {
                const response = await fetch('/api/students', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newStudent)
                });
                
                if (response.ok) {
                    this.newStudent = {
                        name: '',
                        phone: '',
                        birthday: '',
                        nrc_id: '',
                        guardian_name: '',
                        guardian_contact: '',
                        subject_ids: []
                    };
                    this.showAddModal = false;
                    await this.fetchStudents(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding student:', error);
            }
        },
        
        async editStudent(student) {
            this.editingStudent = student;
            this.editStudentData = {
                name: student.name,
                phone: student.phone,
                birthday: student.birthday,
                nrc_id: student.nrc_id,
                guardian_name: student.guardian_name,
                guardian_contact: student.guardian_contact,
                status: student.status,
                subject_ids: student.subjects ? student.subjects.map(s => s.id) : []
            };
            this.showEditModal = true;
        },
        
        async updateStudent() {
            if (!this.editStudentData.name.trim() || !this.editingStudent) return;
            
            try {
                const response = await fetch(`/api/students/${this.editingStudent.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editStudentData)
                });
                
                if (response.ok) {
                    this.editStudentData = {
                        name: '',
                        phone: '',
                        birthday: '',
                        nrc_id: '',
                        guardian_name: '',
                        guardian_contact: '',
                        status: true,
                        subject_ids: []
                    };
                    this.editingStudent = null;
                    this.showEditModal = false;
                    await this.fetchStudents(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating student:', error);
            }
        },
        
        async deleteStudent(id) {
            if (!confirm('Are you sure you want to delete this student?')) return;
            
            try {
                const response = await fetch(`/api/students/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchStudents(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting student:', error);
            }
        },
        
        async toggleStudentStatus(id) {
            try {
                const response = await fetch(`/api/students/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchStudents(this.currentPage);
                }
            } catch (error) {
                console.error('Error toggling student status:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchStudents(page);
        },
        
        init() {
            this.fetchStudents();
            this.fetchGrades();
            this.fetchSubjects();
            
            // Set active tab based on URL
            const path = window.location.pathname;
            if (path.includes('/students/active')) {
                this.activeTab = 'active';
            } else if (path.includes('/students/inactive')) {
                this.activeTab = 'inactive';
            }
        }
    };
}