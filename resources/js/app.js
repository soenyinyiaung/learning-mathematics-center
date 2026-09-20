import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import gradeManagement from './gradeManagement';
import subjectManagement from './subjectManagement';
import subjectDetails from './subjectDetails';
import studentManagement from './studentManagement';
import teacherManagement from './teacherManagement';
import teacherSalaryManagement from './teacherSalaryManagement';
import expenseManagement from './expenseManagement';
import userManagement from './userManagement';
import saleItemManagement from './saleItemManagement';
import voucherManagement from './voucherManagement';
import voucherListManagement from './voucherListManagement';
import studentFeeManagement from './studentFeeManagement';
import feeVoucherManagement from './feeVoucherManagement';
import settingManagement from './settingManagement';
import gradeSubjectFeeManagement from './gradeSubjectFeeManagement';
import studentRegistrationManagement from './studentRegistrationManagement';
import '@fortawesome/fontawesome-free/css/all.css';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('gradeManagement', gradeManagement);
Alpine.data('subjectManagement', subjectManagement);
Alpine.data('subjectDetails', subjectDetails);
Alpine.data('studentManagement', studentManagement);
Alpine.data('teacherManagement', teacherManagement);
Alpine.data('teacherSalaryManagement', teacherSalaryManagement);
Alpine.data('expenseManagement', expenseManagement);
Alpine.data('userManagement', userManagement);
Alpine.data('saleItemManagement', saleItemManagement);
Alpine.data('voucherManagement', voucherManagement);
Alpine.data('voucherListManagement', voucherListManagement);
Alpine.data('studentFeeManagement', studentFeeManagement);
Alpine.data('feeVoucherManagement', feeVoucherManagement);
Alpine.data('settingManagement', settingManagement);
Alpine.data('gradeSubjectFeeManagement', gradeSubjectFeeManagement);
Alpine.data('studentRegistrationManagement', studentRegistrationManagement);

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});
