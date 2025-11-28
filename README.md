# Umdoni Municipality Website

Official website for **Umdoni Local Municipality**, KwaZulu-Natal, South Africa.

## About

The Umdoni Municipality website serves as the primary digital platform for citizens, businesses, and visitors to access municipal services, information, and resources. Located in Scottburgh, Umdoni Municipality provides comprehensive online access to government services, public notices, community engagement, and administrative information.

## Features

### Public Portal
- **News & Announcements** - Latest municipal updates and community news
- **Events Calendar** - Community events, public meetings, and municipal activities
- **Service Requests** - Online submission for municipal services (waste management, graffiti removal, etc.)
- **Leadership Information** - Mayor, Deputy Mayor, Speaker, and council member profiles
- **Departments** - Contact information and services for all municipal departments

### Document Library
- Annual Reports
- Budget Documents
- Council Minutes
- IDP (Integrated Development Plan)
- Policies & Bylaws
- SDBIP (Service Delivery and Budget Implementation Plan)
- Ward Profiles
- Valuation Rolls

### Business & Procurement
- Tender Opportunities
- Request for Proposals (RFPs)
- Quotation Submissions
- Vacancy Postings
- Project Information

### Citizen Services
- Municipal Service Directory
- Meeting Agendas
- Public Notices
- Contact Directory
- Community Information
- COVID-19 Resources

### Dashboard (Admin)
- User Management
- Content Management (News, Events, Documents)
- Service Request Management
- Tender & RFP Administration
- Analytics & Reporting
- Role-Based Access Control

## Technology Stack

### Backend
- **PHP 8+** - Server-side scripting
- **Custom MVC Framework** - Built by Rakheoana Lefela
- **MySQL** - Database management
- **Composer** - Dependency management

### Frontend
- **Bootstrap 5** - Responsive UI framework
- **JavaScript/jQuery** - Interactive functionality
- **SimpleDatatables** - Data table management
- **Font Awesome** - Icons
- **Animate.css** - Animations

### Infrastructure
- **AWS Cognito** - User authentication & authorization
- **AWS S3** - Document storage
- **Rollbar** - Error tracking & monitoring
- **Google Analytics** - Website analytics

### Development Tools
- **Docker** - Containerization (optional)
- **Gulp** - Task automation
- **PHPUnit** - Unit testing
- **Git** - Version control

## Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Apache/Nginx web server

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/gedeza/umdoni-website.git
   cd umdoni-website
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   Update `.env` with your database credentials and AWS settings:
   - Database configuration
   - AWS credentials (Cognito, S3)
   - Other environment-specific settings

4. **Database setup**
   - Create a MySQL database
   - Import database schema (if available)
   - Update database credentials in `.env`

5. **Configure web server**
   - Set document root to `/public` directory
   - Enable mod_rewrite (Apache) or configure URL rewriting

6. **Set permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 public/uploads
   ```

## Development

### Local Development Server

**Using PHP built-in server:**
```bash
cd public
php -S localhost:8000
```

**Using Docker:**
```bash
docker-compose up
```

### Running Tests
```bash
vendor/bin/phpunit
```

## Project Structure

```
umdoni-website/
├── App/
│   ├── Controllers/     # Application controllers
│   ├── Models/          # Data models
│   ├── Views/           # View templates
│   └── Config.php       # Application configuration
├── Components/          # Reusable components
├── Core/                # Core framework classes
├── public/              # Public web root
│   ├── assets/          # CSS, JS, images
│   ├── Includes/        # Shared includes
│   ├── layouts/         # Layout templates
│   └── index.php        # Front controller
├── vendor/              # Composer dependencies
├── .env                 # Environment configuration
├── composer.json        # PHP dependencies
└── README.md
```

## Contributing

This is a municipal government project. For contributions or issues, please contact the municipality's IT department.

## Security

If you discover any security vulnerabilities, please report them to the municipality's IT security team immediately.

## License

This project is proprietary software owned by Umdoni Local Municipality.

## Contact

**Umdoni Local Municipality**
- Website: https://umdoni.gov.za
- Support Hotline: +27 87 286 5329 (24 hours)
- Anti-Fraud Hotline: 0801 111 660
- Disaster Management: (039) 974 6200

## Credits

- **Framework Development:** Rakheoana Lefela
- **Municipality:** Umdoni Local Municipality, KwaZulu-Natal, South Africa
