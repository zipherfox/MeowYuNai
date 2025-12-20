# 🐱 PawFinder - Lost Cat Tracker System

A comprehensive web-based lost cat tracking system with real-time map integration, built with PHP, MySQL, and Leaflet.js.

## 🌟 Features

### Core Functionality
- **User Registration & Authentication**: Secure user accounts with password hashing
- **Cat Profile Management**: Register multiple cats with photos and detailed information
- **Lost Cat Reporting**: Report lost cats with location pinning on interactive maps
- **Real-Time Map Visualization**: See all lost cats on an interactive map using OpenStreetMap
- **Location-Based Alerts**: Users see lost cats within 50km of their location
- **Mark Found**: Update lost reports when cats are reunited
- **Community Sightings**: (Database ready - can be extended)

### Technical Features
- Responsive design with warm, compassionate aesthetics
- Interactive maps with Leaflet.js and OpenStreetMap
- Marker clustering for better map performance
- Geocoding support (address to coordinates)
- Distance calculation using Haversine formula
- Secure file uploads for cat photos
- Location-based search and filtering

## 📋 Requirements

- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: 7.4 or higher
- **MySQL**: 5.7+ or MariaDB 10.3+
- **PHP Extensions**:
  - mysqli
  - gd (for image handling)
  - fileinfo
  - session

## 🚀 Installation

### Step 1: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE lost_cat_tracker;
```

2. Import the database schema:
```bash
mysql -u root -p lost_cat_tracker < database.sql
```

Or manually execute the SQL file in phpMyAdmin or MySQL Workbench.

### Step 2: Configure Database Connection

Edit `includes/config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'lost_cat_tracker');
```

### Step 3: Set File Permissions

Ensure the uploads directory is writable:

```bash
chmod 755 uploads/
chmod 755 uploads/cats/
```

### Step 4: Web Server Configuration

#### Apache (.htaccess)

Create a `.htaccess` file in the root directory:

```apache
RewriteEngine On
RewriteBase /

# Protect sensitive files
<FilesMatch "^(config|database)\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Enable PHP file uploads
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
```

### Step 5: Access the Application

1. Open your web browser
2. Navigate to `http://localhost/lost-cat-tracker` (or your configured domain)
3. Default admin credentials (if using the sample data):
   - Username: `admin`
   - Password: `admin123`

## 📁 Project Structure

```
lost-cat-tracker/
├── css/
│   └── style.css              # Main stylesheet with distinctive design
├── includes/
│   └── config.php             # Database configuration and helper functions
├── uploads/
│   └── cats/                  # Cat photos storage
├── database.sql               # Database schema
├── index.php                  # Homepage with map and listings
├── register.php               # User registration
├── login.php                  # User login
├── logout.php                 # Logout handler
├── dashboard.php              # User dashboard
├── my-cats.php                # Cat profile management
├── report-lost.php            # Report lost cat
├── cat-details.php            # Lost cat details page
├── mark-found.php             # Mark cat as found
├── map.php                    # Full-screen map view
└── README.md                  # This file
```

## 🎨 Design Features

### Visual Identity
- **Fonts**: 
  - Display: DM Serif Display (warm, friendly serif)
  - Body: Manrope (modern, readable sans-serif)
- **Color Palette**:
  - Primary: `#FF6B35` (Warm Orange)
  - Secondary: `#4ECDC4` (Turquoise)
  - Accent: `#FFE66D` (Yellow)
  - Background: `#FFF8F0` (Cream)

### Interactive Elements
- Smooth animations and transitions
- Hover effects on cards and buttons
- Animated navigation underlines
- Floating hero elements
- Responsive design for mobile devices

## 💻 Usage Guide

### For Cat Owners

1. **Register an Account**
   - Click "Sign Up" and fill in your details
   - Pin your location on the map
   - This helps you see lost cats near you

2. **Add Your Cats**
   - Go to "My Cats" → "Add New Cat"
   - Upload a photo and fill in details
   - Include distinctive features for identification

3. **Report a Lost Cat**
   - Click "Report Lost" from dashboard
   - Select the cat from your registered cats
   - Pin the location where last seen
   - Add any additional information

4. **Mark as Found**
   - When your cat is found, go to the report
   - Click "Mark as Found"
   - This removes it from the active lost cats map

### For Community Members

1. **View Lost Cats**
   - Browse the homepage to see recent lost cats
   - Use the map to see cats near your location
   - Click on markers for detailed information

2. **Help Find Lost Cats**
   - Contact owners if you spot a lost cat
   - Use the phone number or email provided
   - Provide specific location and time information

## 🗺️ Map Features

### Interactive Map (Leaflet.js + OpenStreetMap)
- **Marker Types**:
  - Red Cat Icon: Lost cats
  - Blue Pin Icon: User location
  - Teal Circles: Community sightings

### Map Clustering
- Automatically groups nearby markers
- Click clusters to zoom in
- Shows individual cats when zoomed

### Geocoding
- Convert addresses to coordinates
- Automatic location detection in browsers
- Manual map pinning support

## 🔒 Security Features

- Password hashing with bcrypt (PHP password_hash)
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars
- CSRF protection ready 
- File upload validation and sanitization
- Session-based authentication

## 📊 Database Schema

### Tables

**users**
- User accounts and profile information
- Location coordinates for nearby cat alerts

**cats**
- Cat profiles with photos and details
- Linked to user accounts

**lost_reports**
- Lost cat reports with location and status
- Tracks lost date and found date
- Contact information for reporting

**sightings** (optional)
- Community sightings of lost cats
- Extends functionality for community reporting

## 🛠️ Customization

### Change Colors
Edit `css/style.css` CSS variables:

```css
:root {
    --primary: #FF6B35;
    --secondary: #4ECDC4;
    --accent: #FFE66D;
    /* ... */
}
```

### Modify Radius
Change the proximity radius in `includes/config.php`:

```php
function getLostCatsNearLocation($lat, $lon, $radius = 5) {
    // Change $radius default value (in kilometers)
}
```

## 🙏 Acknowledgments

- **OpenStreetMap** for map tiles
- **Leaflet.js** for map functionality
- **Google Fonts** for typography
- Inspired by the need to help reunite lost cats with their families

---

**Made with ❤️ for cats and their loving owners**

🐾 Help bring lost cats home! 🐾
