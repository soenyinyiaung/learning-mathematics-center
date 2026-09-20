export default function voucherManagement() {
    return {
        cart: [],
        loading: false,
        customerType: 'general',
        customerName: '',
        customerPhone: '',
        selectedStudent: '',
        studentId: '',
        students: [],
        voucherNumber: '',
        voucherDate: '',
        cartTotalItems: 0,
        cartTotalPrice: 0,
        
        async init() {
            // Get cart data from localStorage or passed data
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                this.cart = JSON.parse(savedCart);
                this.updateTotals();
            }
            
            // Set current date
            this.voucherDate = new Date().toISOString().split('T')[0];
            
            // Generate voucher number
            this.voucherNumber = this.generateVoucherNumber();
            
            // Fetch students
            await this.fetchStudents();
            
            this.loading = false;
        },
        
        async fetchStudents() {
            try {
                const response = await fetch('/api/students?status=active');
                if (response.ok) {
                    const data = await response.json();
                    this.students = data.data || [];
                    console.log('Students loaded:', this.students);
                }
            } catch (error) {
                console.error('Error fetching students:', error);
            }
        },
        
        generateVoucherNumber() {
            const date = new Date();
            const year = date.getFullYear().toString().slice(-2);
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const seconds = date.getSeconds().toString().padStart(2, '0');
            const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
            return year + month + day + hours + minutes + seconds + random;
        },
        
        updateTotals() {
            this.cartTotalItems = this.cart.reduce((total, item) => total + item.qty, 0);
            this.cartTotalPrice = this.cart.reduce((total, item) => total + (parseFloat(item.amount) * item.qty), 0);
        },
        
        customerTypeChanged() {
            if (this.customerType === 'student') {
                this.customerName = '';
            } else {
                this.selectedStudent = '';
                this.studentId = '';
                this.customerPhone = '';
            }
        },
        
        studentSelected() {
            const student = this.students.find(s => String(s.id) === String(this.selectedStudent));
            if (student) {
                this.studentId = student.student_id || '';
                this.customerPhone = student.phone || '';
            } else {
                this.studentId = '';
                this.customerPhone = '';
            }
        },
        
        async generateVoucher() {
            if (this.cart.length === 0) return;
            if (this.customerType === 'general' && !this.customerName.trim()) {
                alert('Please enter customer name');
                return;
            }
            if (this.customerType === 'student' && !this.selectedStudent) {
                alert('Please select a student');
                return;
            }
            
            const voucherData = {
                type: 'sale',
                voucher_number: this.voucherNumber,
                voucher_date: this.voucherDate,
                customer_type: this.customerType,
                customer_name: this.customerType === 'student' ? this.students.find(s => s.id === this.selectedStudent)?.name : this.customerName,
                customer_phone: this.customerPhone,
                student_id: this.selectedStudent || null,
                student_id_number: this.studentId,
                total_amount: this.cartTotalPrice,
                items: this.cart
            };
            
            // Print first
            this.printVoucher();
            
            // Wait for print dialog to close, then save to database
            setTimeout(async () => {
                try {
                    const response = await fetch('/api/vouchers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(voucherData)
                    });
                    
                    if (response.ok) {
                        // Clear cart
                        localStorage.removeItem('cart');
                        this.cart = [];
                        this.customerName = '';
                        this.customerPhone = '';
                        this.selectedStudent = '';
                        this.studentId = '';
                        this.cartTotalItems = 0;
                        this.cartTotalPrice = '0.00';
                        
                        // Redirect back to vouchers
                        window.location.href = '/admin/vouchers';
                    } else {
                        console.error('Failed to save voucher');
                        alert('Failed to save voucher. Please try again.');
                    }
                } catch (error) {
                    console.error('Error saving voucher:', error);
                    alert('Failed to save voucher. Please try again.');
                }
            }, 1000);
        },
        
        printVoucher() {
            window.print();
        }
    };
}