export default function academicYearManagement() {
    return {
        academicYears: [],
        grades: [],
        students: [],
        availableStudents: [],
        loading: false,
        loadingGrades: false,
        loadingStudents: false,
        showAddModal: false,
        showEditModal: false,
        showAddStudentModal: false,
        selectedYear: null,
        selectedGrade: null,
        editingYear: null,
        studentSearchQuery: '',
        newYear: {
            start_year: '',
            end_year: ''
        },
        editYearData: {
            start_year: '',
            end_year: ''
        },
        newStudent: {
            student_ids: []
        },

        get filteredAvailableStudents() {
            if (!this.studentSearchQuery) {
                return this.availableStudents;
            }
            const search = this.studentSearchQuery.toLowerCase();
            return this.availableStudents.filter(student => 
                student.name.toLowerCase().includes(search) || 
                student.student_id.toLowerCase().includes(search)
            );
        },

        async init() {
            await this.fetchAcademicYears();
            await this.fetchGrades();
            await this.fetchAvailableStudents();
        },

        async fetchAcademicYears() {
            this.loading = true;
            try {
                const response = await fetch('/api/academic-years');
                const data = await response.json();
                this.academicYears = data.map(year => ({
                    ...year,
                    year_range: `${year.start_year}-${year.end_year}`
                }));
            } catch (error) {
                console.error('Error fetching academic years:', error);
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

        async fetchAvailableStudents() {
            try {
                const response = await fetch('/api/students');
                const data = await response.json();
                this.availableStudents = data.data || data;
            } catch (error) {
                console.error('Error fetching students:', error);
            }
        },

        async fetchStudents(academicYearId, gradeId) {
            this.loadingStudents = true;
            try {
                const response = await fetch(`/api/academic-years/${academicYearId}/grades/${gradeId}/students`);
                const data = await response.json();
                this.students = data;
            } catch (error) {
                console.error('Error fetching students:', error);
            } finally {
                this.loadingStudents = false;
            }
        },

        selectAcademicYear(year) {
            this.selectedYear = year;
            this.selectedGrade = null;
            this.students = [];
        },

        selectGrade(grade) {
            this.selectedGrade = grade;
            if (this.selectedYear) {
                this.fetchStudents(this.selectedYear.id, grade.id);
            }
        },

        searchStudents() {
            // This is handled by the computed property
        },

        async addYear() {
            if (!this.newYear.start_year || !this.newYear.end_year) {
                alert('Please fill in all required fields');
                return;
            }

            if (parseInt(this.newYear.end_year) <= parseInt(this.newYear.start_year)) {
                alert('End year must be greater than start year');
                return;
            }

            try {
                const response = await fetch('/api/academic-years', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newYear)
                });

                if (response.ok) {
                    alert('Academic year added successfully');
                    this.showAddModal = false;
                    this.newYear = { start_year: '', end_year: '' };
                    await this.fetchAcademicYears();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to add academic year'));
                }
            } catch (error) {
                console.error('Error adding academic year:', error);
                alert('Failed to add academic year. Please try again.');
            }
        },

        editYear(year) {
            this.editingYear = year;
            this.editYearData = {
                start_year: year.start_year,
                end_year: year.end_year
            };
            this.showEditModal = true;
        },

        async updateYear() {
            if (!this.editYearData.start_year || !this.editYearData.end_year) {
                alert('Please fill in all required fields');
                return;
            }

            if (parseInt(this.editYearData.end_year) <= parseInt(this.editYearData.start_year)) {
                alert('End year must be greater than start year');
                return;
            }

            try {
                const response = await fetch(`/api/academic-years/${this.editingYear.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editYearData)
                });

                if (response.ok) {
                    alert('Academic year updated successfully');
                    this.showEditModal = false;
                    this.editYearData = { start_year: '', end_year: '' };
                    this.editingYear = null;
                    await this.fetchAcademicYears();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to update academic year'));
                }
            } catch (error) {
                console.error('Error updating academic year:', error);
                alert('Failed to update academic year. Please try again.');
            }
        },

        async deleteYear(id) {
            if (!confirm('Are you sure you want to delete this academic year?')) return;

            try {
                const response = await fetch(`/api/academic-years/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    alert('Academic year deleted successfully');
                    if (this.selectedYear && this.selectedYear.id === id) {
                        this.selectedYear = null;
                        this.selectedGrade = null;
                        this.students = [];
                    }
                    await this.fetchAcademicYears();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to delete academic year'));
                }
            } catch (error) {
                console.error('Error deleting academic year:', error);
                alert('Failed to delete academic year. Please try again.');
            }
        },

        async addStudent() {
            if (this.newStudent.student_ids.length === 0) {
                alert('Please select at least one student');
                return;
            }

            try {
                const response = await fetch(`/api/academic-years/${this.selectedYear.id}/students`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        student_ids: this.newStudent.student_ids,
                        grade_id: this.selectedGrade.id
                    })
                });

                if (response.ok) {
                    alert('Students added successfully');
                    this.showAddStudentModal = false;
                    this.newStudent = { student_ids: [] };
                    this.studentSearchQuery = '';
                    await this.fetchStudents(this.selectedYear.id, this.selectedGrade.id);
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to add students'));
                }
            } catch (error) {
                console.error('Error adding students:', error);
                alert('Failed to add students. Please try again.');
            }
        },

        async removeStudent(studentId) {
            if (!confirm('Are you sure you want to remove this student from this academic year?')) return;

            try {
                const response = await fetch(`/api/academic-years/${this.selectedYear.id}/students/${studentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    alert('Student removed successfully');
                    await this.fetchStudents(this.selectedYear.id, this.selectedGrade.id);
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to remove student'));
                }
            } catch (error) {
                console.error('Error removing student:', error);
                alert('Failed to remove student. Please try again.');
            }
        }
    };
}