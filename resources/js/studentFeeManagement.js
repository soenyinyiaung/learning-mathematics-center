export default function studentFeeManagement() {
    return {
        invoices: [],
        students: [],
        loading: false,
        showGenerateModal: false,
        showHistoryModal: false,
        selectedMonthYear: '',
        selectedAmount: '',
        selectedStudentIds: [],
        selectAllStudents: false,
        selectedStudent: null,
        studentInvoices: [],
        searchQuery: '',
        filterStatus: '',
        
        async init() {
            await this.fetchInvoices();
            await this.fetchStudents();
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
                this.selectedStudentIds = this.students.map(s => s.id);
            } else {
                this.selectedStudentIds = [];
            }
        },
        
        async generateInvoices() {
            if (!this.selectedMonthYear) {
                alert('Please select month/year');
                return;
            }
            
            if (!this.selectedAmount || this.selectedAmount <= 0) {
                alert('Please enter amount');
                return;
            }
            
            if (this.selectedStudentIds.length === 0) {
                alert('Please select at least one student');
                return;
            }
            
            try {
                const response = await fetch('/api/invoices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        month_year: this.selectedMonthYear,
                        amount: this.selectedAmount,
                        student_ids: this.selectedStudentIds
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    alert(data.message + '. Created ' + data.invoices.length + ' invoices.');
                    this.showGenerateModal = false;
                    this.selectedMonthYear = '';
                    this.selectedAmount = '';
                    this.selectedStudentIds = [];
                    this.selectAllStudents = false;
                    await this.fetchInvoices();
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
        
        async viewHistory(student) {
            this.selectedStudent = student;
            this.showHistoryModal = true;
            
            try {
                const response = await fetch(`/api/invoices?student_id=${student.id}`);
                if (response.ok) {
                    const data = await response.json();
                    this.studentInvoices = data.data || [];
                }
            } catch (error) {
                console.error('Error fetching student invoices:', error);
            }
        }
    };
}