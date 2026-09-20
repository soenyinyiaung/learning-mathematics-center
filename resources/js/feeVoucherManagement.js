export default function feeVoucherManagement() {
    return {
        invoice: null,
        loading: false,
        voucherNumber: '',
        voucherDate: '',
        discount: 0,
        studentSubjects: [],
        
        init() {
            this.voucherDate = new Date().toISOString().split('T')[0];
            this.voucherNumber = this.generateVoucherNumber();
            this.loadInvoice();
        },
        
        loadInvoice() {
            const invoiceData = localStorage.getItem('feeInvoice');
            if (invoiceData) {
                this.invoice = JSON.parse(invoiceData);
                this.loadStudentSubjects();
            }
        },
        
        async loadStudentSubjects() {
            if (!this.invoice || !this.invoice.student) return;
            
            try {
                const response = await fetch(`/api/students/${this.invoice.student.id}`);
                if (response.ok) {
                    const data = await response.json();
                    const student = data;
                    
                    if (student.subjects && student.subjects.length > 0) {
                        // Fetch grade subject fees for each subject
                        const subjectFees = await Promise.all(
                            student.subjects.map(async (subject) => {
                                try {
                                    const feeResponse = await fetch(`/api/grade-subject-fees/grade/${student.grade_id}`);
                                    if (feeResponse.ok) {
                                        const feeData = await feeResponse.json();
                                        const feeDataArray = feeData.data || feeData;
                                        const subjectFee = feeDataArray.find(f => f.subject_id === subject.id);
                                        return {
                                            name: subject.name,
                                            amount: subjectFee ? subjectFee.fee : 0
                                        };
                                    }
                                    return { name: subject.name, amount: 0 };
                                } catch (error) {
                                    return { name: subject.name, amount: 0 };
                                }
                            })
                        );
                        this.studentSubjects = subjectFees;
                    }
                }
            } catch (error) {
                console.error('Error loading student subjects:', error);
            }
        },
        
        get totalAmount() {
            const originalAmount = parseFloat(this.invoice?.amount || 0);
            const discountAmount = parseFloat(this.discount || 0);
            return Math.max(0, originalAmount - discountAmount);
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
            
            return 'FEE' + year + month + day + hours + minutes + seconds + random;
        },
        
        async savePayment() {
            if (!this.invoice) return;
            
            try {
                // Print first
                window.print();
                
                // Wait 1 second then save to database
                setTimeout(async () => {
                    // Build items array from student subjects
                    const items = this.studentSubjects.map(subject => ({
                        description: subject.name,
                        amount: subject.amount,
                        qty: 1
                    }));
                    
                    // Add discount item if applicable
                    if (this.discount > 0) {
                        items.push({
                            description: 'Discount',
                            amount: -this.discount,
                            qty: 1
                        });
                    }
                    
                    // Save voucher
                    const voucherResponse = await fetch('/api/vouchers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            type: 'student_fee',
                            voucher_number: this.voucherNumber,
                            voucher_date: this.voucherDate,
                            customer_type: 'student',
                            customer_name: this.invoice.student.name,
                            customer_phone: this.invoice.student.phone || '',
                            student_id: this.invoice.student.id,
                            student_id_number: this.invoice.student.student_id,
                            total_amount: this.totalAmount,
                            items: items
                        })
                    });
                    
                    if (voucherResponse.ok) {
                        // Save payment with discount
                        const paymentResponse = await fetch('/api/payments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                invoice_id: this.invoice.id,
                                voucher_no: this.voucherNumber,
                                paid_amount: this.totalAmount,
                                paid_date: this.voucherDate,
                                discount: this.discount
                            })
                        });
                        
                        if (paymentResponse.ok) {
                            // Clear localStorage
                            localStorage.removeItem('feeInvoice');
                            // Redirect to student fees page
                            window.location.href = '/admin/student-fees';
                        } else {
                            alert('Failed to save payment. Please try again.');
                        }
                    } else {
                        alert('Failed to save voucher. Please try again.');
                    }
                }, 1000);
            } catch (error) {
                console.error('Error saving payment:', error);
                alert('Failed to save payment. Please try again.');
            }
        }
    };
}