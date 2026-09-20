export default function settingManagement() {
    return {
        passwordForm: {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },
        loading: false,
        
        async changePassword() {
            if (!this.passwordForm.current_password || !this.passwordForm.new_password || !this.passwordForm.new_password_confirmation) {
                alert('Please fill in all fields');
                return;
            }
            
            if (this.passwordForm.new_password !== this.passwordForm.new_password_confirmation) {
                alert('New password and confirmation do not match');
                return;
            }
            
            if (this.passwordForm.new_password.length < 8) {
                alert('New password must be at least 8 characters');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/api/settings/change-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.passwordForm)
                });
                
                if (response.ok) {
                    const data = await response.json();
                    alert(data.message);
                    this.passwordForm = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: ''
                    };
                } else {
                    const error = await response.json();
                    alert(error.error || 'Failed to change password. Please try again.');
                }
            } catch (error) {
                console.error('Error changing password:', error);
                alert('Failed to change password. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        init() {
            // Initialize if needed
        }
    };
}