# Contributing to Dental Clinic Management System

First off, thank you for considering contributing to this project! 🎉

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)

## 📜 Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code.

## 🤝 How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce**
- **Expected vs actual behavior**
- **Screenshots** (if applicable)
- **Environment details** (PHP version, OS, browser)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Clear title and description**
- **Use case and benefits**
- **Possible implementation approach**
- **Mockups or examples** (if applicable)

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Make your changes
4. Test thoroughly
5. Commit with clear messages
6. Push to your fork
7. Open a Pull Request

## 🛠️ Development Setup

### Prerequisites

```bash
# Required
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+
- Composer (optional)

# Recommended
- XAMPP or similar local server
- Git
- Code editor (VS Code, PHPStorm)
```

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/dental-clinic-system.git
cd dental-clinic-system

# Import database
mysql -u root -p < database/db.sql

# Configure database
cp config/database.example.php config/database.php
# Edit config/database.php with your credentials

# Set permissions
chmod 755 backups/
chmod 755 database/
```

### Running Locally

```bash
# Using PHP built-in server
php -S localhost:8000

# Or use XAMPP
# Place in C:\xampp\htdocs\teeth
# Access: http://localhost/teeth
```

## 📝 Coding Standards

### PHP

- Follow PSR-12 coding standard
- Use meaningful variable and function names
- Add PHPDoc comments for functions
- Keep functions small and focused
- Use type hints where possible

```php
/**
 * Get patient by ID
 * 
 * @param int $id Patient ID
 * @return array|null Patient data or null
 */
function getPatient(int $id): ?array {
    // Implementation
}
```

### JavaScript

- Use ES6+ features
- Use `const` and `let`, avoid `var`
- Use arrow functions where appropriate
- Add JSDoc comments for functions
- Keep functions pure when possible

```javascript
/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of notification (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds
 */
function showToast(message, type = 'success', duration = 3000) {
    // Implementation
}
```

### CSS

- Use Tailwind utility classes
- Avoid custom CSS unless necessary
- Keep custom CSS in `assets/css/`
- Use mobile-first approach
- Follow BEM naming for custom classes

### Database

- Use prepared statements (PDO)
- Never use string concatenation for queries
- Add indexes for frequently queried columns
- Use transactions for multiple operations
- Document schema changes

## 📦 Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `perf`: Performance improvements
- `test`: Adding tests
- `chore`: Maintenance tasks

### Examples

```bash
feat(patients): add bulk delete functionality

- Add checkbox selection
- Implement bulk delete API
- Add confirmation dialog

Closes #123

fix(medicines): correct expiry date query

Changed SQLite syntax to MySQL for date calculations

perf(dashboard): optimize query performance

Reduced queries from 10 to 6, improving load time by 50%
```

## 🔍 Pull Request Process

1. **Update Documentation**
   - Update README.md if needed
   - Update CHANGELOG.md
   - Add/update code comments

2. **Test Your Changes**
   - Test on different browsers
   - Test on mobile devices
   - Test with different user roles
   - Verify no breaking changes

3. **Code Review**
   - Address review comments
   - Keep discussions professional
   - Be open to suggestions

4. **Merge Requirements**
   - All tests pass
   - Code review approved
   - Documentation updated
   - No merge conflicts

## 🧪 Testing

### Manual Testing Checklist

- [ ] Feature works as expected
- [ ] No console errors
- [ ] Mobile responsive
- [ ] Works in Chrome, Firefox, Safari
- [ ] Works with different user roles
- [ ] No SQL errors
- [ ] No PHP warnings/errors

### Test User Accounts

```
Admin:
- Username: admin
- Password: admin123

Dentist:
- Username: dentist
- Password: dentist123

Secretary:
- Username: secretary
- Password: secretary123
```

## 📚 Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Chart.js](https://www.chartjs.org/docs/)

## ❓ Questions?

Feel free to:
- Open an issue for discussion
- Contact maintainers
- Join our community chat

## 🙏 Thank You!

Your contributions make this project better for everyone. Thank you for taking the time to contribute! ❤️
