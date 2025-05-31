# Land Registration and Transfer System

![PHP Version](https://img.shields.io/badge/PHP-8.3-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Framework](https://img.shields.io/badge/Framework-MVC-orange)
![Blockchain](https://img.shields.io/badge/Blockchain-Enabled-brightgreen)
![Security](https://img.shields.io/badge/Security-Enhanced-red)

A comprehensive and secure platform for registering, transferring, and managing land properties. This system leverages cutting-edge technologies including blockchain, QR codes, and smart contracts to ensure transparent, secure, and user-friendly property management.

## 🌟 Key Features

### Core System Features
- **Property Registration**: Register and manage land properties with detailed information
- **Ownership Transfer**: Secure and transparent property transfer system using smart contracts
- **QR Code Integration**: Quick property verification and information access
- **Blockchain Integration**: 
  - Immutable record-keeping and transaction history
  - Smart contract-based property transfers
  - NFT representation of properties
  - Cross-chain compatibility
- **Advanced Security**:
  - Multi-factor authentication (MFA)
  - Biometric authentication
  - Advanced encryption for documents
  - Real-time security monitoring
- **User Authentication**: Secure login and role-based access control
- **Document Management**: 
  - Upload and manage property-related documents
  - Automated document generation
  - Version control
  - Digital notary services
- **Search Functionality**: Advanced property search and filtering

### Technical Features
- **AutoLoader**: Automatic class loading for efficient code organization
- **Log System**: Comprehensive error tracking and debugging
- **Request Handler**: Advanced HTTP request processing
- **Response Manager**: Flexible HTTP response handling
- **Router**: Dynamic URL routing and controller mapping
- **View Engine**: Powerful templating system
- **Session Management**: Secure user session handling
- **Hash Utilities**: Advanced security features
- **Data Validation**: Robust input validation system
- **Smart Contract Integration**: Automated property transfer workflows
- **API Integration**: Banking, government, and insurance system integration

### Advanced Features
- **AI-Powered Analytics**:
  - Property value prediction
  - Market trend analysis
  - Investment opportunity scoring
  - Risk assessment
- **Mobile Application**:
  - Native iOS and Android support
  - Offline functionality
  - Push notifications
  - Mobile document scanning
- **IoT Integration**:
  - Property monitoring
  - Smart access control
  - Utility management
- **Financial Tools**:
  - Investment portfolio management
  - Automated payment processing
  - Financial reporting
  - Risk assessment

## 🚀 Getting Started

### Prerequisites
- PHP 8.3 or higher
- Apache 2.4.6 or higher
- MySQL 8.0 or higher
- Composer (for dependency management)

### Installation

#### Windows Setup

1. **Install PHP**
   ```bash
   # Download PHP 8.3 VS16 X64 Thread Safe
   # Link: https://windows.php.net/download#php-8.3
   # Extract to C:/php
   ```

2. **Install Apache**
   ```bash
   # Download Apache 2.4.6 Win64
   # Link: https://www.apachelounge.com/download/
   # Extract to C:/apache24
   ```

3. **Configure Apache**
   - Navigate to `C:/Apache24/conf/httpd.conf`
   - Update the following configurations:
     ```apache
     DocumentRoot "C:/Apache24/htdocs/LandRegistrysystem/public"
     <Directory "C:/Apache24/htdocs/LandRegistrysystem/public">
         AllowOverride All
         Require all granted
     </Directory>
     DirectoryIndex index.php
     ServerName localhost:80
     ```

4. **Enable PHP in Apache**
   ```apache
   # Add to httpd.conf
     PHPIniDir "C:/php"
     LoadModule php_module "C:/php/php8apache2_4.dll"
     AddType application/x-httpd-php .php
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

5. **Configure PHP**
   - Open `C:/php/php.ini`
   - Enable required extensions:
     ```ini
     extension=pdo_mysql
     extension=mysqli
     ```

6. **Install MySQL**
   - Download MySQL Installer 8.0
   - Install MySQL Server and MySQL Workbench
   - Configure database connection

7. **Start Services**
   ```bash
   # Install Apache service
     C:/Apache24/bin/httpd.exe -k install
   
   # Start Apache
     C:/Apache24/bin/httpd.exe -k start
     ```

### Project Setup

1. **Clone Repository**
   ```bash
   git clone https://github.com/GhaithAlmomani/LandRegistrysystem.git
   ```

2. **Configure Permissions**
   ```bash
   # Ensure write permissions for:
   storage/log
   storage/session
   ```

3. **Database Setup**
   - Import the provided SQL schema
   - Configure database connection in config file

## 📁 Project Structure

```
LandRegistrysystem/
├── public/          # Public entry point
├── src/             # Source code
│   ├── controller/  # Controllers
│   ├── core/        # Core framework
│   ├── blockchain/  # Blockchain integration
│   ├── smartcontracts/ # Smart contract implementations
│   ├── api/         # API integrations
│   ├── services/    # Business logic services
│   └── view/        # Views and templates
├── storage/         # Storage for logs and sessions
├── mobile/          # Mobile application code
├── tests/           # Test suites
└── vendor/          # Dependencies
```

## 🔧 Configuration

### Environment Variables
Create a `.env` file in the root directory:
```env
DB_HOST=localhost
DB_NAME=land_registry
DB_USER=your_username
DB_PASS=your_password
BLOCKCHAIN_NETWORK=mainnet
API_KEY=your_api_key
SMART_CONTRACT_ADDRESS=your_contract_address
```

### Required Services
- Apache 2.4.6 or higher
- PHP 8.3 or higher
- MySQL 8.0 or higher
- Node.js 16+ (for blockchain integration)
- Composer (for PHP dependencies)
- npm (for blockchain dependencies)

## 📱 Mobile Application

The system includes native mobile applications for both iOS and Android platforms, providing:
- Property search and viewing
- Document management
- QR code scanning
- Push notifications
- Offline functionality
- Biometric authentication

## 🔗 API Integration

The system provides RESTful APIs for integration with:
- Banking systems
- Government agencies
- Insurance companies
- Real estate platforms
- Payment gateways

## 🔒 Security Features

- Multi-factor authentication
- Biometric verification
- Blockchain-based verification
- Advanced encryption
- Real-time monitoring
- Automated fraud detection

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

- **WISE University Team** - *Initial work*

## 🙏 Acknowledgments

- Thanks to all contributors who have helped shape this project
- Special thanks to the open-source community for their invaluable tools and resources

## 📞 Support

For support, please:
1. Open an issue in the GitHub repository
2. Contact the development team
3. Check our documentation at [docs.landregistry.com](https://docs.landregistry.com)
4. Join our community forum

---

Made with ❤️ by WISE University Team
