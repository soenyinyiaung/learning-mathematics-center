export default function studentRegistrationManagement() {
    return {
        registrations: [],
        loading: false,
        searchQuery: '',
        filterStatus: '',
        currentPage: 1,
        lastPage: 1,
        showAddModal: false,
        newRegistration: {
            student_id: '',
            name: '',
            phone: '',
            birthday: '',
            nrc_id: '',
            grade_id: '',
            guardian_name: '',
            guardian_contact: '',
            subject_ids: []
        },
        grades: [],
        subjects: [],
        
        async init() {
            await this.fetchRegistrations();
            await this.fetchGrades();
            await this.fetchSubjects();
        },
        
        async fetchRegistrations(page = 1) {
            this.loading = true;
            try {
                let url = '/api/student-registrations?page=' + page;
                const params = new URLSearchParams();
                
                if (this.filterStatus) {
                    params.append('status', this.filterStatus);
                }
                
                if (this.searchQuery) {
                    params.append('search', this.searchQuery);
                }
                
                if (params.toString()) {
                    url += '&' + params.toString();
                }
                
                const response = await fetch(url);
                const data = await response.json();
                this.registrations = data.data || [];
                this.currentPage = data.current_page || 1;
                this.lastPage = data.last_page || 1;
            } catch (error) {
                console.error('Error fetching registrations:', error);
                this.registrations = [];
            } finally {
                this.loading = false;
            }
        },
        
        async searchRegistrations() {
            this.currentPage = 1;
            await this.fetchRegistrations();
        },
        
        async confirmRegistration(student) {
            if (!confirm(`Are you sure you want to confirm ${student.name}'s registration?`)) return;
            
            try {
                const response = await fetch(`/api/student-registrations/${student.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        registration_status: 'confirmed'
                    })
                });
                
                if (response.ok) {
                    alert('Registration confirmed successfully');
                    await this.fetchRegistrations(this.currentPage);
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to confirm registration'));
                }
            } catch (error) {
                console.error('Error confirming registration:', error);
                alert('Failed to confirm registration. Please try again.');
            }
        },
        
        async rejectRegistration(student) {
            if (!confirm(`Are you sure you want to reject ${student.name}'s registration?`)) return;
            
            try {
                const response = await fetch(`/api/student-registrations/${student.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        registration_status: 'rejected'
                    })
                });
                
                if (response.ok) {
                    alert('Registration rejected successfully');
                    await this.fetchRegistrations(this.currentPage);
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to reject registration'));
                }
            } catch (error) {
                console.error('Error rejecting registration:', error);
                alert('Failed to reject registration. Please try again.');
            }
        },
        
        async goToPage(page) {
            await this.fetchRegistrations(page);
        },
        
        async fetchGrades() {
            try {
                const response = await fetch('/api/grades');
                if (response.ok) {
                    const data = await response.json();
                    this.grades = data.data || data;
                }
            } catch (error) {
                console.error('Error fetching grades:', error);
            }
        },
        
        async fetchSubjects() {
            try {
                const response = await fetch('/api/subjects');
                if (response.ok) {
                    const data = await response.json();
                    this.subjects = data.data || data;
                }
            } catch (error) {
                console.error('Error fetching subjects:', error);
            }
        },
        
        async addRegistration() {
            if (!this.newRegistration.student_id || !this.newRegistration.name || !this.newRegistration.phone || 
                !this.newRegistration.birthday || !this.newRegistration.nrc_id || !this.newRegistration.grade_id || 
                !this.newRegistration.guardian_name || !this.newRegistration.guardian_contact) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (this.newRegistration.subject_ids.length === 0) {
                alert('Please select at least one subject');
                return;
            }
            
            try {
                const response = await fetch('/api/students', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newRegistration)
                });
                
                if (response.ok) {
                    alert('Registration added successfully');
                    this.showAddModal = false;
                    this.newRegistration = {
                        student_id: '',
                        name: '',
                        phone: '',
                        birthday: '',
                        nrc_id: '',
                        grade_id: '',
                        guardian_name: '',
                        guardian_contact: '',
                        subject_ids: []
                    };
                    await this.fetchRegistrations(this.currentPage);
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Failed to add registration'));
                }
            } catch (error) {
                console.error('Error adding registration:', error);
                alert('Failed to add registration. Please try again.');
            }
        }
    };
}