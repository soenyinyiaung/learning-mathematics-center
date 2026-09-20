export default function subjectDetails() {
    return {
        subject: null,
        students: [],
        teachers: [],
        grades: [],
        loading: false,
        loadingStudents: false,
        loadingTeachers: false,
        showEditModal: false,
        editSubjectData: {
            name: ''
        },
        subjectId: null,
        selectedGrade: '',
        activeTab: 'students',
        studentCurrentPage: 1,
        studentTotalPages: 1,
        teacherCurrentPage: 1,
        teacherTotalPages: 1,
        itemsPerPage: 10,
        
        async fetchSubject() {
            this.loading = true;
            try {
                const response = await fetch(`/api/subjects/${this.subjectId}`);
                const data = await response.json();
                this.subject = data;
            } catch (error) {
                console.error('Error fetching subject:', error);
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
        
        async fetchStudents() {
            this.loadingStudents = true;
            try {
                let url = `/api/subjects/${this.subjectId}/students?page=${this.studentCurrentPage}&per_page=${this.itemsPerPage}`;
                if (this.selectedGrade) {
                    url += `&grade_id=${this.selectedGrade}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                this.students = data.data;
                this.studentCurrentPage = data.current_page;
                this.studentTotalPages = data.last_page;
            } catch (error) {
                console.error('Error fetching students:', error);
                this.students = [];
            } finally {
                this.loadingStudents = false;
            }
        },
        
        async fetchTeachers() {
            this.loadingTeachers = true;
            try {
                let url = `/api/subjects/${this.subjectId}/teachers?page=${this.teacherCurrentPage}&per_page=${this.itemsPerPage}`;
                if (this.selectedGrade) {
                    url += `&grade_id=${this.selectedGrade}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                this.teachers = data.data;
                this.teacherCurrentPage = data.current_page;
                this.teacherTotalPages = data.last_page;
            } catch (error) {
                console.error('Error fetching teachers:', error);
                this.teachers = [];
            } finally {
                this.loadingTeachers = false;
            }
        },
        
        async filterByGrade() {
            this.studentCurrentPage = 1;
            this.teacherCurrentPage = 1;
            await this.fetchStudents();
            await this.fetchTeachers();
        },
        
        editSubject() {
            if (!this.subject) return;
            this.editSubjectData = {
                name: this.subject.name
            };
            this.showEditModal = true;
        },
        
        async updateSubject() {
            if (!this.editSubjectData.name.trim() || !this.subjectId) return;
            
            try {
                const response = await fetch(`/api/subjects/${this.subjectId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editSubjectData)
                });
                
                if (response.ok) {
                    this.editSubjectData = { name: '' };
                    this.showEditModal = false;
                    await this.fetchSubject();
                }
            } catch (error) {
                console.error('Error updating subject:', error);
            }
        },
        
        async goToStudentPage(page) {
            this.studentCurrentPage = page;
            await this.fetchStudents();
        },
        
        async goToTeacherPage(page) {
            this.teacherCurrentPage = page;
            await this.fetchTeachers();
        },
        
        init() {
            this.subjectId = window.subjectId;
            this.fetchSubject();
            this.fetchGrades();
            this.fetchStudents();
            this.fetchTeachers();
        }
    };
}