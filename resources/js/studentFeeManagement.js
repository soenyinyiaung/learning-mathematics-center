export default function studentFeeManagement() {
    return {
        invoices: [],
        students: [],
        grades: [],
        gradeSubjectFees: [],
        loading: false,
        showGenerateModal: false,
        selectedMonthYear: '',
        selectedAmount: '',
        selectedGrade: '',
        selectedStudentIds: [],
        selectAllStudents: false,
        searchQuery: '',
        filterStatus: '',
        showEditModal: false,
        editingInvoice: null,
        
        async init() {
            await this.fetchInvoices();
            await this.fetchStudents();
            await this.fetchGrades();
            await this.fetchGradeSubjectFees();
        },
        
        async fetchStudents() {
            try {
                const response = await fetch('/api/students?status=active');
                if (response.ok) {
                    const data = await response.json();
                    this.students = data.data || [];
                }
            } catch (error) {
                console.error('Error fetching students:', error);
            }
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
        
        async fetchGradeSubjectFees() {
            try {
                const response = await fetch('/api/grade-subject-fees');
                if (response.ok) {
                    const data = await response.json();
                    this.gradeSubjectFees = data.data || data;
                }
            } catch (error) {
                console.error('Error fetching grade subject fees:', error);
            }
        },
        
        async fetchInvoices() {
            this.loading = true;
            try {
                const response = await fetch('/api/invoices');
                if (response.ok) {
                    const data = await response.json();
                    this.invoices = data.data || [];
                }
            } catch (error) {
                console.error('Error fetching invoices:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async searchStudents() {
            this.loading = true;
            try {
                let url = '/api/invoices';
                const params = new URLSearchParams();
                
                if (this.searchQuery) {
                    params.append('search', this.searchQuery);
                }
                
                if (this.filterStatus) {
                    params.append('status', this.filterStatus);
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const data = await response.json();
                    this.invoices = data.data || [];
                }
            } catch (error) {
                console.error('Error searching invoices:', error);
            } finally {
                this.loading = false;
            }
        },
        
        toggleSelectAllStudents() {
            if (this.selectAllStudents) {
                this.selectedStudentIds = this.filteredStudents.map(s => s.id);
            } else {
                this.selectedStudentIds = [];
            }
        },
        
        get filteredStudents() {
            if (!this.selectedGrade) {
                return this.students;
            }
            return this.students.filter(s => s.grade_id == this.selectedGrade);
        },
        
        filterStudentsByGrade() {
            this.selectedStudentIds = [];
            this.selectAllStudents = false;
        },
        
        async generateInvoices() {
            if (!this.selectedMonthYear) {
                alert('Please select month/year');
                return;
            }
            
            if (this.selectedStudentIds.length === 0) {
                alert('Please select at least one student');
                return;
            }
            
            // Calculate auto amounts if amount is blank
            const payload = {
                month_year: this.selectedMonthYear,
                student_ids: this.selectedStudentIds
            };
            
            if (this.selectedAmount && this.selectedAmount > 0) {
                payload.amount = this.selectedAmount;
            }
            
            try {
                const response = await fetch('/api/invoices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });
                
                if (response.ok) {
                    const data = await response.json();
                    alert(data.message + '. Created ' + data.invoices.length + ' invoices.');
                    this.showGenerateModal = false;
                    this.selectedMonthYear = '';
                    this.selectedAmount = '';
                    this.selectedGrade = '';
                    this.selectedStudentIds = [];
                    this.selectAllStudents = false;
                    await this.fetchInvoices();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + errorData.message);
                }
            } catch (error) {
                console.error('Error generating invoices:', error);
                alert('Failed to generate invoices. Please try again.');
            }
        },
        
        payInvoice(invoice) {
            // Store invoice data in localStorage
            localStorage.setItem('feeInvoice', JSON.stringify(invoice));
            // Redirect to fee payment checkout page
            window.location.href = '/admin/student-fees/checkout';
        },
        
        editInvoice(invoice) {
            this.editingInvoice = { ...invoice };
            this.showEditModal = true;
        },
        
        async updateInvoice() {
            if (!this.editingInvoice) return;
            
            if (!this.editingInvoice.amount || this.editingInvoice.amount < 0) {
                alert('Please enter a valid amount');
                return;
            }
            
            try {
                const response = await fetch(`/api/invoices/${this.editingInvoice.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        amount: this.editingInvoice.amount
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    alert('Invoice updated successfully');
                    this.showEditModal = false;
                    this.editingInvoice = null;
                    await this.fetchInvoices();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + errorData.message);
                }
            } catch (error) {
                console.error('Error updating invoice:', error);
                alert('Failed to update invoice. Please try again.');
            }
        },
        
        async deleteInvoice(invoice) {
            if (!confirm('Are you sure you want to delete this invoice?')) return;
            
            try {
                const response = await fetch(`/api/invoices/${invoice.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    alert('Invoice deleted successfully');
                    await this.fetchInvoices();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + errorData.message);
                }
            } catch (error) {
                console.error('Error deleting invoice:', error);
                alert('Failed to delete invoice. Please try again.');
            }
        }
    };
}