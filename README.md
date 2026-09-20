# Learning Mathematics Center

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <a href="https://github.com/laravel/framework"><img src="https://img.shields.io/badge/Laravel-13.17-red.svg" alt="Laravel Version"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.3-blue.svg" alt="PHP Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License"></a>
</p>

## ပရောဂျက် အကြောင်း

Learning Mathematics Center သည် သင်္ချာသင်တန်းလေ့ကျင့်ရေးစင်တာများအတွက် ရည်ရွယ်ထားသော Laravel framework ဖြင့် တည်ဆောက်ထားသည့် စီမံခန့်ခွဲမှုစနစ်တစ်ခုဖြစ်သည်။ ဤစနစ်သည် ကျောင်းသားများ၊ ဆရာများ၊ ဘာသာရပ်များ၊ ငွေစာရင်းများနှင့် အခြားစီမံခန့်ခွဲမှုလုပ်ငန်းများကို ထိရောက်စွာစီမံခန့်ခွဲနိုင်ရန် ဒီဇိုင်းထုတ်ထားသည်။

## အဓိက စွမ်းဆောင်ချက်များ

### 🎓 ကျောင်းသား စီမံခန့်ခွဲမှု
- ကျောင်းသားများကို မှတ်ပုံတင်ခြင်းနှင့် စီမံခန့်ခွဲခြင်း
- ကျောင်းသားအခြေအနေကို စီစစ်ခြင်း (Active/Inactive)
- ကျောင်းသားကျောင်းလစ်နှင့် အခွန်စာရင်းများ စီမံခန့်ခွဲခြင်း
- ကျောင်းသားအခြေအနေမှတ်တမ်းများ ထိန်းသိမ်းခြင်း

### 👨‍🏫 ဆရာ စီမံခန့်ခွဲမှု
- ဆရာများကို မှတ်ပုံတင်ခြင်းနှင့် စီမံခန့်ခွဲခြင်း
- ဆရာလစာစာရင်းများ စီမံခန့်ခွဲခြင်း
- ဆရာအခြေအနေကို စီစစ်ခြင်း (Active/Inactive)

### 📚 ဘာသာရပ်နှင့် အဆင့် စီမံခန့်ခွဲမှု
- ဘာသာရပ်များကို စီမံခန့်ခွဲခြင်း
- အဆင့်များ (Grades) ကို စီမံခန့်ခွဲခြင်း

### 💰 ငွေစာရင်း စီမံခန့်ခွဲမှု
- ကုန်ကျစရိတ်များကို စီမံခန့်ခွဲခြင်း
- ကုန်ကျစရိတ်အမျိုးအစားများကို စီမံခန့်ခွဲခြင်း
- အရောင်းပစ္စည်းများကို စီမံခန့်ခွဲခြင်း
- ဘောင်ချာများ (Vouchers) နှင့် ငွေပေးချေမှုများကို စီမံခန့်ခွဲခြင်း
- ငွေလွှဲများ (Invoices) ကို စီမံခန့်ခွဲခြင်း

### 📊 ဒက်ရှ်ဘုတ်
- ကျောင်းသားဦးရေ၊ ဘာသာရပ်ဦးရေ၊ လစဉ်ဝင်ငွေနှင့် ကုန်ကျစရိတ်များကို ကြည့်ရှုနိုင်ခြင်း
- လစဉ်နှင့် နှစ်စဉ် ဝင်ငွေ/ကုန်ကျစရိတ် အချက်အလက်များကို ဂရပ်ဖ်များဖြင့် ပြသခြင်း
- ကျောင်းသားဦးရေ ပြောင်းလဲမှုများကို ခန့်မှန်းနိုင်ခြင်း

### 👤 အသုံးပြုသူ စီမံခန့်ခွဲမှု
- အသုံးပြုသူများကို စီမံခန့်ခွဲခြင်း
- စာရွက်စာတမ်းများကို လုံခြုံစွာ စီမံခန့်ခွဲခြင်း
- စာဝှက်ပြောင်းလဲမှုများကို စီမံခန့်ခွဲခြင်း

## လိုအပ်ချက်များ

- PHP >= 8.3
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js & NPM (for frontend assets)

## ထည့်သွင်းပုံ

### 1. Repository ကို Clone လုပ်ပါ

```bash
git clone https://github.com/soenyinyiaung/learning-mathematics-center.git
cd learning-mathematics-center
```

### 2. Dependencies များကို ထည့်သွင်းပါ

```bash
composer install
npm install
```

### 3. Environment ဖိုင်ကို ပြင်ဆင်ပါ

```bash
cp .env.example .env
php artisan key:generate
```

`.env` ဖိုင်ထဲတွင် database အချက်အလက်များကို ပြင်ဆင်ပါ:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learning_mathematics_center
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### 4. Database ကို တည်ဆောက်ပါ

```bash
php artisan migrate
```

### 5. Frontend assets များကို build လုပ်ပါ

```bash
npm run build
```

### 6. Development server ကို စတင်ပါ

```bash
php artisan serve
```

အခြား terminal တစ်ခုတွင် Vite development server ကို စတင်ပါ:

```bash
npm run dev
```

### 7. Application ကို ဝင်ရောက်ပါ

Browser တွင် `http://localhost:8000` ကို ဖွင့်ပါ။

## အသုံးပြုပုံ

### စတင်အသုံးပြုရန်

1. Login page တွင် admin credentials ဖြင့် login လုပ်ပါ
2. Dashboard တွင် အဓိကအချက်အလက်များကို ကြည့်ရှုနိုင်ပါသည်
3. Sidebar မှ လိုအပ်သော section ကို ရွေးချယ်ပါ

### API Endpoints

ဤ application တွင် RESTful API endpoints များ ပါဝင်သည်:

- `GET/POST/PUT/DELETE /api/grades` - အဆင့်များ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/subjects` - ဘာသာရပ်များ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/students` - ကျောင်းသားများ စီမံခန့်ခွဲရန်
- `POST /api/students/{id}/toggle-status` - ကျောင်းသားအခြေအနေ ပြောင်းလဲရန်
- `GET/POST/PUT/DELETE /api/teachers` - ဆရာများ စီမံခန့်ခွဲရန်
- `POST /api/teachers/{id}/toggle-status` - ဆရာအခြေအနေ ပြောင်းလဲရန်
- `GET/POST/PUT/DELETE /api/teacher-salaries` - ဆရာလစာများ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/expenses` - ကုန်ကျစရိတ်များ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/expense-categories` - ကုန်ကျစရိတ်အမျိုးအစားများ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/users` - အသုံးပြုသူများ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/sale-items` - အရောင်းပစ္စည်းများ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/vouchers` - ဘောင်ချာများ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/invoices` - ငွေလွှဲများ စီမံခန့်ခွဲရန်
- `GET/POST/PUT/DELETE /api/payments` - ငွေပေးချေမှုများ စီမံခန့်ခွဲရန်
- `POST /api/settings/change-password` - စာဝှက်ပြောင်းလဲရန်

## Database Schema

ဤ application တွင် အောက်ပါ database tables များ ပါဝင်သည်:

- `users` - အသုံးပြုသူများ
- `grades` - အဆင့်များ
- `subjects` - ဘာသာရပ်များ
- `students` - ကျောင်းသားများ
- `student_status_logs` - ကျောင်းသားအခြေအနေမှတ်တမ်းများ
- `teachers` - ဆရာများ
- `teacher_salaries` - ဆရာလစာများ
- `expense_categories` - ကုန်ကျစရိတ်အမျိုးအစားများ
- `expenses` - ကုန်ကျစရိတ်များ
- `sale_items` - အရောင်းပစ္စည်းများ
- `vouchers` - ဘောင်ချာများ
- `invoices` - ငွေလွှဲများ
- `payments` - ငွေပေးချေမှုများ

## Development

### Testing

```bash
php artisan test
```

### Code Style

```bash
./vendor/bin/pint
```

### Database Migration

```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh
```

## ပံ့ပိုးကူညီမှု

ဤ project ကို support ပေးလိုပါက issues တင်နိုင်ပါသည် သို့မဟုတ် pull requests ပို့နိုင်ပါသည်။

## License

ဤ project သည် MIT License အောက်တွင် open-source အဖြစ် လွှင့်တင်ထားသည်။

## အသိပေးချက်

ဤ project သည် Laravel framework ကို အခြေခံထားသည်။ Laravel အကြောင်းကို [Laravel Documentation](https://laravel.com/docs) တွင် ဖတ်ရှုနိုင်ပါသည်။
