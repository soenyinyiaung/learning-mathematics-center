export default function teacherManagement() {
    return {
        teachers: [],
        subjects: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        editingTeacher: null,
        activeTab: 'active',
        newTeacher: {
            name: '',
            phone: '',
            nrc_id: '',
            employment_type: 'full-time',
            subject_ids: []
        },
        editTeacherData: {
            teacher_id: '',
            name: '',
            phone: '',
            nrc_id: '',
            employment_type: 'full-time',
            status: true,
            subject_ids: []
        },
        currentPage: 1,
        lastPage: 1,
        searchQuery: '',
        
        async fetchTeachers(page = 1) {
            this.loading = true;
            try {
                let url = `/api/teachers?page=${page}`;
                if (this.searchQuery) {
                    url += `&search=${encodeURIComponent(this.searchQuery)}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                this.teachers = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching teachers:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async searchTeachers() {
            this.currentPage = 1;
            await this.fetchTeachers(1);
        },
        
        setTab(tab) {
            this.activeTab = tab;
        },
        
        get filteredTeachers() {
            if (this.activeTab === 'active') {
                return this.teachers.filter(teacher => teacher.status === true);
            } else {
                return this.teachers.filter(teacher => teacher.status === false);
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
        
        async addTeacher() {
            if (!this.newTeacher.name.trim()) return;

            try {
                const response = await fetch('/api/teachers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newTeacher)
                });

                if (response.ok) {
                    this.newTeacher = {
                        name: '',
                        phone: '',
                        nrc_id: '',
                        employment_type: 'full-time',
                        subject_ids: []
                    };
                    this.showAddModal = false;
                    await this.fetchTeachers(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding teacher:', error);
            }
        },
        
        async editTeacher(teacher) {
            this.editingTeacher = teacher;
            this.editTeacherData = {
                teacher_id: teacher.teacher_id,
                name: teacher.name,
                phone: teacher.phone,
                nrc_id: teacher.nrc_id,
                employment_type: teacher.employment_type,
                status: teacher.status,
                subject_ids: teacher.subjects ? teacher.subjects.map(s => s.id) : []
            };
            this.showEditModal = true;
        },
        
        async updateTeacher() {
            if (!this.editTeacherData.name.trim() || !this.editingTeacher) return;

            try {
                const response = await fetch(`/api/teachers/${this.editingTeacher.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editTeacherData)
                });

                if (response.ok) {
                    this.editTeacherData = {
                        teacher_id: '',
                        name: '',
                        phone: '',
                        nrc_id: '',
                        employment_type: 'full-time',
                        status: true,
                        subject_ids: []
                    };
                    this.editingTeacher = null;
                    this.showEditModal = false;
                    await this.fetchTeachers(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating teacher:', error);
            }
        },
        
        async deleteTeacher(id) {
            if (!confirm('Are you sure you want to delete this teacher?')) return;
            
            try {
                const response = await fetch(`/api/teachers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchTeachers(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting teacher:', error);
            }
        },
        
        async toggleTeacherStatus(id) {
            try {
                const response = await fetch(`/api/teachers/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchTeachers(this.currentPage);
                }
            } catch (error) {
                console.error('Error toggling teacher status:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchTeachers(page);
        },
        
        init() {
            this.fetchTeachers();
            this.fetchSubjects();
        }
    };
}
