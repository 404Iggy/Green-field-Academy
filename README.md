# Greenfield Academy

Greenfield Academy is a custom PHP school website for a Kenyan academic institution offering Early Years, O Level, A Level and University programmes. The site is built with native PHP, MySQL, HTML, CSS and JavaScript, and is designed to work as a responsive public school website with a protected student/teacher portal.

## Overview

This project includes:

- A public home page with hero content, academic pathway cards, stats and news
- About and leadership information
- A director profile page
- Academic programme listings by level
- Contact form submission
- Portal login and protected dashboard access
- Database-backed data management via MySQL

## Website Pages

The current project contains the following pages:

- index.php — home page
- about.php — school story, mission and leadership
- portfolio.php — director profile
- academic.php — academic programmes by level
- contact.php — contact form and map section
- login.php — portal login
- register.php — registration page
- portal.php — authenticated user dashboard
- config.php — site settings and database connection
- header.php and footer.php — shared template layout

## Tech Stack

- PHP 8+
- MySQL
- HTML5
- CSS3
- Vanilla JavaScript
- XAMPP / Apache / MySQL local environment

This is a hand-built PHP project. It does not use a framework such as Laravel or CodeIgniter.

## Project Structure

```text
Green-field-Academy/
├── index.php
├── about.php
├── academic.php
├── portfolio.php
├── contact.php
├── login.php
├── register.php
├── portal.php
├── config.php
├── header.php
├── footer.php
├── main.css
├── main.js
├── database.sql
├── migration.sql
├── README.md
└── .git/
```

## Database Setup

The project uses a MySQL database named `greenfield_academy`.

### 1. Create the database

In phpMyAdmin, create a database called:

```text
greenfield_academy
```

### 2. Import the schema

Import the SQL files from this project into the database:

```text
database.sql
migration.sql
```

If you are using XAMPP, make sure Apache and MySQL are running before importing.

## Configuration

The connection settings and site details are defined in `config.php`.

```php
const SITE_NAME = 'Greenfield Academy';
const SITE_TAGLINE = 'A complete learning journey from Early Years to University';
const SITE_ADDRESS = 'Westlands, Nairobi, Kenya';
const SITE_PHONE = '+254 707 566 279';
const SITE_EMAIL = 'info@greenfield.sc.ke';

const DB_HOST = 'localhost';
const DB_NAME = 'greenfield_academy';
const DB_USER = 'root';
const DB_PASS = '';
```

If your local environment uses different credentials, update these values to match your database setup.

## Local Development

### Prerequisites

- XAMPP, WAMP, MAMP or a working local PHP + MySQL setup
- Apache web server
- MySQL database server
- Modern browser

### Run the project

1. Copy the repository into your local web directory, for example:

```text
C:\xampp\htdocs\Green-field-Academy
```

or on Linux:

```text
/var/www/html/Green-field-Academy
```

2. Start Apache and MySQL.
3. Import the SQL files into the `greenfield_academy` database.
4. Open the site in a browser:

```text
http://localhost/Green-field-Academy/
```

## Core Features

- Fully responsive design for desktop, tablet and mobile devices
- PDF-style school branding with green, cream and gold styling
- Multi-level academic programme layout
- Database-driven contact and portal logic
- Session-based authentication for protected portal pages

## Security Notes

The site uses:

- PDO prepared statements for database queries
- PHP session checks for protected pages
- Output escaping via the `sanitize()` helper
- Password verification using PHP's password handling functions

## Notes

- The public site is intentionally custom and lightweight rather than framework-based.
- The academic page is named `academic.php` in this repository, not `academics.php`.
- The site is designed around a local school environment and can be adjusted for production by updating database credentials and contact information.

## License

This project is for educational and personal use as part of the Greenfield Academy website build.

If you want, this README can also be expanded into a more polished project pitch with a screenshot section, setup checklist and contributor notes.
```

### Configuration

Open `includes/config.php` and update the database credentials to match your environment:

```php
define('DB_HOST', 'localhost');   // Usually 'localhost' for XAMPP
define('DB_NAME', 'greenfield_academy');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password (empty for default XAMPP)
```

You can also update the school's contact details here:

```php
define('SITE_NAME',    'Greenfield Academy');
define('SITE_EMAIL',   'info@greenfield.ac.ke');
define('SITE_PHONE',   '+254 700 123 456');
define('SITE_ADDRESS', '14 Westlands Drive, Nairobi, Kenya');
```

### Running the Project

**1. Start XAMPP** — ensure Apache and MySQL are both **Running** (green).

**2. Open your browser** and navigate to:

```
http://localhost/greenfield_website/
```

That's it — the site should load immediately.

---

## Pages & Routes

| URL | File | Access |
|---|---|---|
| `/` or `/index.php` | `index.php` | Public |
| `/about.php` | `about.php` | Public |
| `/academics.php` | `academics.php` | Public |
| `/academics.php?level=o_level` | `academics.php` | Public — opens O Level tab |
| `/admissions.php` | `admissions.php` | Public |
| `/contact.php` | `contact.php` | Public |
| `/login.php` | `login.php` | Public — redirects to portal if already logged in |
| `/portal.php` | `portal.php` | 🔒 Protected — requires login |
| `/logout.php` | `logout.php` | 🔒 Destroys session |

> **Portal guard:** `portal.php` checks for a valid PHP session at the top of the file. Any unauthenticated request is immediately redirected to `login.php`.

---

## Design System

### Colour Palette

| Name | Hex | Usage |
|---|---|---|
| Green Dark | `#0D2B1F` | Hero backgrounds, topbar, footer |
| Green Mid | `#1A4A30` | Primary buttons, nav active state |
| Green Light | `#2E7D52` | Hover states, borders, icons |
| Gold | `#C9A84C` | Header border, CTA band, accents |
| Gold Light | `#F0D88A` | Hero badge, hero stat numbers |
| Cream | `#FAF8F3` | Page background |
| Text Mid | `#4A4A4A` | Body copy |
| Text Light | `#7A7A7A` | Captions, meta text |

All colours are defined as CSS custom properties (variables) in `assets/css/main.css`:

```css
:root {
  --green-dark:  #0d2b1f;
  --green-mid:   #1a4a30;
  --green-light: #2e7d52;
  --gold:        #c9a84c;
  --cream:       #faf8f3;
  /* ... */
}
```

### Typography

| Font | Style | Used For |
|---|---|---|
| **Playfair Display** | Serif, 600–900 weight | All headings, logo name, large display text |
| **DM Sans** | Sans-serif, 300–600 weight | Body text, navigation, labels, forms |

Both fonts are loaded from Google Fonts via a `<link>` in `header.php`.

### Responsive Breakpoints

| Breakpoint | Width | Behaviour |
|---|---|---|
| Desktop | 1200px+ | Full multi-column layouts, topbar visible |
| Tablet | 600px – 900px | 2-column grids, hamburger menu, stacked forms |
| Mobile | < 600px | Single-column, compressed topbar, full-width buttons |

---

## JavaScript Modules

All JavaScript lives in `assets/js/main.js`. There are no external JS libraries or dependencies.

### 1. Mobile Navigation Toggle
Toggles the `.open` class on the `<nav>` element when the hamburger button is clicked. Animates the three bars into an × icon. Clicking outside the menu closes it.

### 2. Academics Level Tabs
Switches the visible course panel when a level tab is clicked by toggling the `.active` and `.hidden` classes. Also reads `?level=` from the URL query string on page load so that direct links (e.g. from the Home page level cards) open the correct tab automatically.

### 3. Scroll-In Animations
Uses `IntersectionObserver` to add the `.in-view` class to cards and tiles as they enter the viewport. Each observed element starts at `opacity: 0` and `translateY(20px)` and transitions to its natural position. Each element is observed only once.

### 4. Auto-Dismiss Alerts
Success and error `.alert` messages fade out automatically after **4 seconds** using a CSS `opacity` transition, so the user doesn't need to dismiss them manually.

---

## Security

The following security measures are implemented throughout the project:

| Measure | Implementation |
|---|---|
| **SQL Injection Prevention** | All database queries use PDO Prepared Statements with bound parameters |
| **XSS Prevention** | All user-supplied output is escaped with `htmlspecialchars()` via the `sanitize()` helper in `config.php` |
| **Password Security** | Passwords are hashed with PHP `password_hash()` using the `PASSWORD_BCRYPT` algorithm |
| **Password Verification** | Login uses `password_verify()` — plaintext passwords are never stored or compared |
| **Session Security** | PHP sessions are started in `config.php`; the portal page checks for a valid session on every request |
| **Input Validation** | All form inputs are validated server-side (required fields, email format, ENUM value whitelisting) |
| **Role-Based Access** | The `users.role` column (`student`, `staff`, `admin`) controls what the portal dashboard displays |

> ⚠️ **Production note:** Before deploying to a live server, replace the demo password fallback in `login.php` with proper bcrypt-only verification. Also consider adding CSRF token protection to all forms.

---

## Demo Credentials

The `database.sql` seed file includes two demo accounts for testing:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@greenfield.ac.ke` | `Admin@1234` |
| Student | `james@greenfield.ac.ke` | `Student@1234` |

> These credentials are shown on the login page for convenience during development. **Remove the demo hint block from `login.php` before going live.**

---

## Deployment

### Shared Hosting (cPanel)

1. Zip the project folder
2. Upload and extract to `public_html/` via cPanel File Manager
3. Create a MySQL database and user via cPanel → MySQL Databases
4. Import `database.sql` via phpMyAdmin
5. Update `includes/config.php` with the live DB credentials
6. Visit your domain — the site should be live

### VPS / Linux Server (Apache)

```bash
# Install dependencies
sudo apt update
sudo apt install apache2 php8.1 php8.1-mysql mysql-server -y

# Move files
sudo cp -r greenfield_website/ /var/www/html/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/greenfield_website/
sudo chmod -R 755 /var/www/html/greenfield_website/

# Import database
mysql -u root -p < database.sql

# Restart Apache
sudo systemctl restart apache2
```

---

## Future Enhancements

The following features are planned for future versions:

- [ ] **Admin dashboard** — manage admissions, view contact messages, post news
- [ ] **Email notifications** — auto-email on admissions form submission (PHPMailer)
- [ ] **Student results portal** — upload and display exam results per student
- [ ] **News & Events module** — full CRUD for news posts with image uploads
- [ ] **Google Maps integration** — embedded map on the Contact page
- [ ] **CSRF protection** — token-based form security
- [ ] **Password reset** — email-based password recovery flow
- [ ] **Search functionality** — site-wide search for courses and pages
- [ ] **Multi-language support** — English and Swahili toggle
- [ ] **Fee payment integration** — M-Pesa / Stripe payment gateway

---

## Contributing

Contributions, bug reports and feature suggestions are welcome.

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "Add: description of your change"`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

Please follow the existing code style — no external frameworks, clean comments, and test across Chrome, Firefox and mobile before submitting.

---

## License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2025 Greenfield Academy Web Development Team

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

---

<div align="center">

Built with ❤️ for **Greenfield Academy**, Nairobi, Kenya

*Early Years · O Level · A Level · University*

</div>
