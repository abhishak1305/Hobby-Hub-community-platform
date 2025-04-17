# Hobby Groups Platform

A lightweight web application for hobby groups and clubs to connect, interact, and manage group activities. Built with PHP, MySQL, and Tailwind CSS.

## Features

- **User Authentication**
  - Secure registration and login system
  - Password hashing and session management
  - Protected routes for authenticated users

- **Group Management**
  - Create and join hobby groups
  - Group member management with admin roles
  - Group details and member listings

- **Discussion Board**
  - Create and participate in group discussions
  - Comment system for interactive conversations
  - Support for both group-specific and general discussions

- **Event Management**
  - Create and schedule group events
  - RSVP functionality (Attending, Maybe, Not Attending)
  - Event details with location and time information

- **Member Directory**
  - Searchable member listings
  - Filter members by groups
  - Member profiles with activity information

## Technology Stack

- **Frontend**: HTML, Tailwind CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Icons**: Font Awesome
- **Fonts**: Google Fonts (Inter)

## Setup Instructions

1. **Database Setup**
   ```sql
   # Import the schema.sql file to create the database and tables
   mysql -u your_username -p < schema.sql
   ```

2. **Configuration**
   - Update database credentials in `includes/config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'hobby_platform');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

3. **Web Server Configuration**
   - Configure your Apache web server to point to the `public` directory
   - Ensure PHP has write permissions for session management
   - Enable PDO and MySQL extensions in PHP

4. **File Permissions**
   ```bash
   # Set appropriate permissions for the project directories
   chmod 755 public/
   chmod 755 includes/
   ```

## Directory Structure

```
hobby_platform/
├── includes/
│   ├── config.php       # Database and application configuration
│   ├── functions.php    # Helper functions
│   ├── header.php       # Common header template
│   └── footer.php       # Common footer template
├── public/
│   ├── index.php        # Landing page
│   ├── register.php     # User registration
│   ├── login.php        # User login
│   ├── logout.php       # User logout
│   ├── dashboard.php    # User dashboard
│   ├── groups.php       # Group listing and creation
│   ├── group_detail.php # Individual group view
│   ├── discussion.php   # Discussion board
│   ├── events.php       # Event listing and creation
│   ├── event_detail.php # Individual event view
│   └── members.php      # Member directory
└── schema.sql          # Database schema
```

## Security Features

- Password hashing using PHP's `password_hash()`
- PDO prepared statements for SQL injection prevention
- Input sanitization for XSS prevention
- Session-based authentication
- CSRF protection for forms
- Secure password requirements

## Usage Guidelines

1. **Registration and Login**
   - Users must register with a valid email and password
   - Passwords must be at least 8 characters long
   - Login using registered email and password

2. **Creating Groups**
   - Click "Create New Group" on the groups page
   - Provide group name and description
   - Creator automatically becomes group admin

3. **Managing Events**
   - Group members can create events
   - Users can RSVP to events
   - Event details include date, time, and location

4. **Discussions**
   - Create discussions within groups or general forum
   - Comment on existing discussions
   - Search and filter discussions

5. **Member Directory**
   - Search for members by name
   - Filter members by group membership
   - View member profiles and activity

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
