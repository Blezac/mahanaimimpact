# Mahanaim Impact Network - Event Registration System

## 🎯 Project Overview

A complete, production-ready multi-page website and event registration system for Mahanaim Impact Network, a faith-driven youth empowerment and economic development organization.

**Tagline:** Empowering Purpose. Building Legacy.

---

## ✨ Features

### Frontend Features
- ✅ Modern, responsive design (Mobile-first)
- ✅ Professional corporate + nonprofit aesthetic
- ✅ Multi-page website (Home, Academy, Gallery, Partner)
- ✅ Dynamic event listing and registration
- ✅ Automatic early bird pricing calculation
- ✅ Real-time event details display
- ✅ Smooth animations and hover effects
- ✅ SEO-optimized meta tags

### Backend Features
- ✅ PHP-based event management system
- ✅ MySQL database with PDO (secure prepared statements)
- ✅ Paystack payment integration
- ✅ QR code ticket generation
- ✅ Email notifications (PHPMailer)
- ✅ Admin dashboard with analytics
- ✅ CSV export functionality
- ✅ Session-based authentication
- ✅ Form validation and sanitization

### Security Features
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ Input sanitization and validation
- ✅ Password hashing (bcrypt)
- ✅ Session-based admin authentication
- ✅ Protected admin routes
- ✅ CSRF protection ready

---

## 📂 Project Structure

```
mahanaim-impact-network/
├── admin/
│   ├── login.php              # Admin login page
│   ├── dashboard.php          # Admin dashboard with analytics
│   └── logout.php             # Admin logout handler
├── config/
│   └── db.php                 # Database configuration
├── emails/
│   └── mailer.php             # Email templates and functions
├── includes/
│   ├── header.php             # Global header navigation
│   └── footer.php             # Global footer
├── payments/
│   └── paystack.php           # Paystack payment initialization
├── qrcodes/                   # Generated QR code storage
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── index.php                  # Homepage
├── academy.php                # Academy page
├── gallery.php                # Gallery page
├── partner.php                # Partner/mentor/sponsor forms
├── register.php               # Event registration form
├── verify.php                 # Payment verification & QR generation
├── database.sql               # Database schema with sample data
├── composer.json              # PHP dependencies
└── README.md                  # This file
```

---

## 🚀 Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (PHP dependency manager)

### Step 1: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE mahanaim_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u root -p mahanaim_db < database.sql
```

3. Update database credentials in `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mahanaim_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Step 2: Install Dependencies

```bash
cd mahanaim-impact-network
composer install
```

This will install:
- PHPMailer (Email sending)
- Endroid QR Code (QR code generation)

### Step 3: Configure Paystack

1. Get your Paystack API keys from [paystack.com](https://paystack.com)
2. Update keys in `payments/paystack.php`:
```php
define('PAYSTACK_SECRET_KEY', 'sk_live_your_secret_key');
define('PAYSTACK_PUBLIC_KEY', 'pk_live_your_public_key');
```

### Step 4: Configure Email (PHPMailer)

Update SMTP settings in `emails/mailer.php` and `verify.php`:
```php
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password';
```

For Gmail, enable "Less secure app access" or use an App Password.

### Step 5: Set Permissions

```bash
chmod 755 qrcodes/
chmod 644 config/db.php
```

### Step 6: Admin Access

Default admin credentials (change immediately after first login):
- **Username:** admin
- **Password:** admin123

To create a new admin password hash:
```php
echo password_hash('your_password', PASSWORD_DEFAULT);
```

---

## 🎨 Design System

### Color Palette
- **Deep Purple:** `#4B0082` (Primary - Indigo)
- **Light Purple:** `#6A0DAD` (Secondary - Purple)
- **Gold Accent:** `#D4AF37` (Accent)
- **White:** `#FFFFFF` (Background)
- **Light Gray:** `#F8F9FA` (Sections)

### Typography
- **Headings:** Montserrat (Bold, 700-800)
- **Body:** Open Sans (Regular, 400)

### Responsive Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

---

## 💳 Payment Flow

1. User selects event and fills registration form
2. System calculates pricing (early bird vs regular)
3. Registration saved as "pending" in database
4. User redirected to Paystack payment gateway
5. After payment:
   - Transaction verified with Paystack API
   - Payment status updated to "paid"
   - QR code generated and saved
   - Confirmation email sent to participant
   - Notification email sent to admin
6. User sees confirmation page with QR ticket

---

## 📧 Email Notifications

### Participant Email Includes:
- Event details (title, date, location)
- Payment confirmation
- Transaction reference
- QR code ticket (attached)

### Admin Email Includes:
- New registration alert
- Participant details
- Payment amount
- Transaction reference

---

## 🔐 Admin Dashboard Features

### Statistics Display
- Total registrations
- Paid vs pending count
- Total revenue
- Event-specific analytics

### Event Management
- View all events
- See registrations per event
- Export registrations to CSV
- Real-time updates

### Recent Activity
- Latest 10 registrations
- Payment status tracking
- Quick overview

---

## 🎫 QR Code System

- **Format:** `MIN-TICKET-{registration_id}`
- **Storage:** `/qrcodes/{registration_id}.png`
- **Usage:** Scan at event entrance for check-in
- **Security:** Unique per registration

---

## 📱 Pages Overview

### 1. Homepage (index.php)
- Hero section with call-to-action
- Mission overview
- Trade fair highlight
- Academy highlight
- Upcoming events (dynamic from database)
- Donation CTA

### 2. Academy (academy.php)
- Program overview
- Target audience
- Benefits and curriculum
- Enrollment CTA
- FAQ section

### 3. Gallery (gallery.php)
- Trade fair photos
- Outreach programs
- Mentorship sessions
- Hover overlay effects

### 4. Partner (partner.php)
- Become a mentor form
- Sponsor a student form
- Form validation
- Success confirmations

### 5. Register (register.php)
- Event selection dropdown
- Dynamic pricing display
- Ticket type selection (Vendor/Attendee/Sponsor)
- Real-time calculations
- Payment integration

### 6. Verify (verify.php)
- Payment verification
- QR code generation
- Email sending
- Confirmation display

---

## 🛡️ Security Best Practices

1. **SQL Injection Prevention:** All queries use PDO prepared statements
2. **XSS Protection:** All outputs use `htmlspecialchars()`
3. **Password Security:** Bcrypt hashing with `password_hash()`
4. **Session Security:** Admin routes protected with session checks
5. **Input Validation:** Server-side validation on all forms
6. **Email Validation:** `filter_var()` with `FILTER_VALIDATE_EMAIL`

---

## 🔧 Configuration Checklist

Before going live:

- [ ] Update database credentials
- [ ] Configure Paystack API keys (use live keys)
- [ ] Set up SMTP email server
- [ ] Change default admin password
- [ ] Test payment flow end-to-end
- [ ] Verify email delivery
- [ ] Test QR code generation
- [ ] Check file permissions
- [ ] Enable HTTPS/SSL
- [ ] Set up backup schedule
- [ ] Configure error logging
- [ ] Test on mobile devices

---

## 📊 Database Tables

### events
- Event information
- Pricing (early bird + regular)
- Dates and locations

### registrations
- Participant information
- Payment status
- QR code path
- Transaction references

### admins
- Admin user accounts
- Hashed passwords

### mentors
- Mentor applications

### sponsors
- Sponsorship commitments

---

## 🚨 Troubleshooting

### Payment Issues
- Verify Paystack API keys are correct
- Check callback URL is accessible
- Ensure amount is in kobo (multiply by 100)

### Email Not Sending
- Verify SMTP credentials
- Check firewall/port 587
- Enable "Less secure apps" for Gmail
- Use app-specific password

### QR Code Not Generating
- Check `qrcodes/` folder permissions (755)
- Verify Composer dependencies installed
- Check PHP GD extension enabled

### Database Connection Failed
- Verify MySQL service is running
- Check database credentials
- Ensure database exists
- Check PDO extension enabled

---

## 📈 Future Enhancements

Potential features to add:
- [ ] Multiple payment gateways (Flutterwave, etc.)
- [ ] SMS notifications
- [ ] Bulk registration
- [ ] Discount codes
- [ ] Refund system
- [ ] Event check-in system (QR scanner)
- [ ] Attendee certificates
- [ ] Social media integration
- [ ] Newsletter signup
- [ ] Blog/News section

---

## 📞 Support

For questions or support:
- **Email:** info@mahanaimnetwork.org
- **Website:** [Coming Soon]

---

## 📄 License

© 2025 Mahanaim Impact Network. All Rights Reserved.

---

## 👨‍💻 Developer Notes

### Code Style
- PSR-12 coding standards
- Meaningful variable names
- Inline comments for complex logic
- Modular file structure

### Testing Recommendations
- Test all forms with invalid data
- Verify payment flow with test keys
- Check email delivery
- Test mobile responsiveness
- Verify QR code scanning
- Test admin dashboard functions

---

**Built with ❤️ for Mahanaim Impact Network**

*Empowering Purpose. Building Legacy.*
