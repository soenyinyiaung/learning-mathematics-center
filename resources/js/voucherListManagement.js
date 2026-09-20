export default function voucherListManagement() {
    return {
        vouchers: [],
        loading: false,
        showViewModal: false,
        selectedVoucher: null,
        filterType: '',
        
        async init() {
            await this.fetchVouchers();
        },
        
        async fetchVouchers() {
            this.loading = true;
            try {
                let url = '/api/vouchers';
                if (this.filterType) {
                    url += '?type=' + this.filterType;
                }
                
                const response = await fetch(url);
                if (response.ok) {
                    const data = await response.json();
                    this.vouchers = data.data || [];
                }
            } catch (error) {
                console.error('Error fetching vouchers:', error);
            } finally {
                this.loading = false;
            }
        },
        
        viewVoucher(voucher) {
            this.selectedVoucher = voucher;
            this.showViewModal = true;
        },
        
        printVoucher() {
            const printContent = document.getElementById('voucher-detail').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = '<div style="max-width: 400px; margin: 0 auto; padding: 20px;">' + printContent + '</div>';
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    };
}