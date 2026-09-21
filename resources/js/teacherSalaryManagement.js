export default function teacherSalaryManagement() {
    return {
        salaries: [],
        teachers: [],
        loading: false,
        showAddModal: false,
        showEditModal: false,
        showVoucherModal: false,
        editingSalary: null,
        selectedSalary: null,
        voucherNumber: '',
        voucherDate: '',
        addVoucherNumber: '',
        newSalary: {
            teacher_id: '',
            amount: '',
            salary_date: new Date().toISOString().split('T')[0],
            notes: ''
        },
        editSalaryData: {
            teacher_id: '',
            amount: '',
            salary_date: '',
            notes: ''
        },
        currentPage: 1,
        lastPage: 1,
        searchQuery: '',
        
        async fetchSalaries(page = 1) {
            this.loading = true;
            try {
                let url = `/api/teacher-salaries?page=${page}`;
                if (this.searchQuery) {
                    url += `&search=${encodeURIComponent(this.searchQuery)}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                this.salaries = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
            } catch (error) {
                console.error('Error fetching salaries:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async searchSalaries() {
            this.currentPage = 1;
            await this.fetchSalaries(1);
        },
        
        async fetchTeachers() {
            try {
                const response = await fetch('/api/teachers');
                const data = await response.json();
                this.teachers = data.data;
            } catch (error) {
                console.error('Error fetching teachers:', error);
            }
        },
        
        async addSalary() {
            if (!this.newSalary.teacher_id || !this.newSalary.amount) return;

            try {
                const response = await fetch('/api/teacher-salaries', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newSalary)
                });

                if (response.ok) {
                    this.newSalary = {
                        teacher_id: '',
                        amount: '',
                        salary_date: new Date().toISOString().split('T')[0],
                        notes: ''
                    };
                    this.addVoucherNumber = '';
                    this.showAddModal = false;
                    await this.fetchSalaries(this.currentPage);
                }
            } catch (error) {
                console.error('Error adding salary:', error);
            }
        },

        updateAddVoucherPreview() {
            if (!this.addVoucherNumber) {
                this.addVoucherNumber = this.generateVoucherNumber();
            }
        },

        async printAndAddSalary() {
            if (!this.newSalary.teacher_id || !this.newSalary.amount) {
                alert('Please fill in all required fields');
                return;
            }

            if (!this.addVoucherNumber) {
                this.addVoucherNumber = this.generateVoucherNumber();
            }

            try {
                // Print first
                window.print();

                // Wait 1 second then save to database
                setTimeout(async () => {
                    // Add salary
                    const salaryResponse = await fetch('/api/teacher-salaries', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.newSalary)
                    });

                    if (salaryResponse.ok) {
                        const salaryData = await salaryResponse.json();

                        // Create voucher
                        const voucherData = {
                            type: 'teacher_salary',
                            voucher_number: this.addVoucherNumber,
                            voucher_date: this.newSalary.salary_date,
                            customer_type: 'general',
                            customer_name: this.getTeacherName(this.newSalary.teacher_id),
                            customer_phone: '',
                            student_id: null,
                            student_id_number: '',
                            total_amount: this.newSalary.amount,
                            items: [{
                                description: 'Teacher Salary Payment',
                                amount: this.newSalary.amount,
                                qty: 1
                            }],
                            payment_method: 'cash'
                        };

                        const voucherResponse = await fetch('/api/vouchers', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(voucherData)
                        });

                        if (voucherResponse.ok) {
                            this.newSalary = {
                                teacher_id: '',
                                amount: '',
                                salary_date: new Date().toISOString().split('T')[0],
                                notes: ''
                            };
                            this.addVoucherNumber = '';
                            this.showAddModal = false;
                            await this.fetchSalaries(this.currentPage);
                            alert('Salary added and voucher saved successfully!');
                        } else {
                            alert('Salary added but failed to save voucher.');
                        }
                    } else {
                        alert('Failed to add salary. Please try again.');
                    }
                }, 1000);
            } catch (error) {
                console.error('Error adding salary and voucher:', error);
                alert('Failed to add salary and voucher. Please try again.');
            }
        },
        
        async editSalary(salary) {
            this.editingSalary = salary;
            this.editSalaryData = {
                teacher_id: salary.teacher_id,
                amount: salary.amount,
                salary_date: salary.salary_date || new Date().toISOString().split('T')[0],
                notes: salary.notes
            };
            this.showEditModal = true;
        },
        
        async updateSalary() {
            if (!this.editSalaryData.teacher_id || !this.editSalaryData.amount || !this.editingSalary) return;
            
            try {
                const response = await fetch(`/api/teacher-salaries/${this.editingSalary.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editSalaryData)
                });
                
                if (response.ok) {
                    this.editSalaryData = {
                        teacher_id: '',
                        amount: '',
                        salary_date: new Date().toISOString().split('T')[0],
                        notes: ''
                    };
                    this.editingSalary = null;
                    this.showEditModal = false;
                    await this.fetchSalaries(this.currentPage);
                }
            } catch (error) {
                console.error('Error updating salary:', error);
            }
        },
        
        async deleteSalary(id) {
            if (!confirm('Are you sure you want to delete this salary?')) return;
            
            try {
                const response = await fetch(`/api/teacher-salaries/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    await this.fetchSalaries(this.currentPage);
                }
            } catch (error) {
                console.error('Error deleting salary:', error);
            }
        },
        
        async goToPage(page) {
            await this.fetchSalaries(page);
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },
        
        formatAmount(amount) {
            return parseFloat(amount).toFixed(2);
        },
        
        getTeacherName(teacherId) {
            const teacher = this.teachers.find(t => t.id == teacherId);
            return teacher ? teacher.name : 'Unknown';
        },

        generateVoucherNumber() {
            const now = new Date();
            const year = String(now.getFullYear()).slice(-2);
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');

            return 'SAL' + year + month + day + hours + minutes + seconds + random;
        },

        generateVoucher(salary) {
            this.selectedSalary = salary;
            this.voucherNumber = this.generateVoucherNumber();
            this.voucherDate = new Date().toISOString().split('T')[0];
            this.showVoucherModal = true;
        },

        async printAndSaveVoucher() {
            if (!this.selectedSalary) return;

            try {
                // Print first
                window.print();

                // Wait 1 second then save to database
                setTimeout(async () => {
                    const voucherData = {
                        type: 'teacher_salary',
                        voucher_number: this.voucherNumber,
                        voucher_date: this.voucherDate,
                        customer_type: 'general',
                        customer_name: this.selectedSalary.teacher?.name || 'Unknown',
                        customer_phone: '',
                        student_id: null,
                        student_id_number: '',
                        total_amount: this.selectedSalary.amount,
                        items: [{
                            description: 'Teacher Salary Payment',
                            amount: this.selectedSalary.amount,
                            qty: 1
                        }],
                        payment_method: 'cash'
                    };

                    const response = await fetch('/api/vouchers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(voucherData)
                    });

                    if (response.ok) {
                        this.showVoucherModal = false;
                        this.selectedSalary = null;
                        alert('Voucher saved successfully!');
                    } else {
                        alert('Failed to save voucher. Please try again.');
                    }
                }, 1000);
            } catch (error) {
                console.error('Error saving voucher:', error);
                alert('Failed to save voucher. Please try again.');
            }
        },

        init() {
            this.fetchSalaries();
            this.fetchTeachers();
        },

        openAddModal() {
            this.newSalary = {
                teacher_id: '',
                amount: '',
                salary_date: new Date().toISOString().split('T')[0],
                notes: ''
            };
            this.addVoucherNumber = this.generateVoucherNumber();
            this.showAddModal = true;
        }
    };
}