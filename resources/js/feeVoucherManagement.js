export default function feeVoucherManagement() {
    return {
        invoice: null,
        loading: false,
        voucherNumber: '',
        voucherDate: '',
        
        init() {
            this.voucherDate = new Date().toISOString().split('T')[0];
            this.voucherNumber = this.generateVoucherNumber();
            this.loadInvoice();
        },
        
        loadInvoice() {
            const invoiceData = localStorage.getItem('feeInvoice');
            if (invoiceData) {
                this.invoice = JSON.parse(invoiceData);
            }
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
                    const response = await fetch('/api/vouchers', {
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
                            total_amount: this.invoice.amount,
                            items: [
                                {
                                    description: 'School Fee - ' + this.invoice.month_year,
                                    amount: this.invoice.amount,
                                    qty: 1
                                }
                            ]
                        })
                    });
                    
                    if (response.ok) {
                        // Update invoice status to paid
                        await fetch(`/api/invoices/${this.invoice.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                status: 'Paid'
                            })
                        });
                        
                        // Clear localStorage
                        localStorage.removeItem('feeInvoice');
                        // Redirect to student fees page
                        window.location.href = '/admin/student-fees';
                    }
                }, 1000);
            } catch (error) {
                console.error('Error saving payment:', error);
                alert('Failed to save payment. Please try again.');
            }
        }
    };
}