# ITS120L-PROJECT

## Local Development Setup

### Prerequisites
- XAMPP (with Apache) installed on your system
- Git installed on your system (optional)
- A code editor (VS Code, Sublime Text, etc.)

### Installation Steps

1. Install XAMPP
- Download XAMPP from the official website: https://www.apachefriends.org/
- Run the installer and follow the installation wizard
- Recommended components to install: Apache, MySQL, PHP

2. Set up the Project
Method A: Direct Download
- Download this project as a ZIP file
- Extract the contents to `C:\xampp\htdocs\ITS120L-PROJECT`

Method B: Using Git
```bash
cd C:\xampp\htdocs
git clone https://github.com/yourusername/ITS120L-PROJECT.git
```

3. Project Structure
```
ITS120L-PROJECT/
├── assets/        # Contains images and other media files
├── pages/         # Website pages
├── styles/        # CSS stylesheets
├── script/        # JavaScript files
├── index.html     # Main entry point
└── style.css      # Main stylesheet
```

4. Running the Project
- Start XAMPP Control Panel
- Start the Apache server by clicking the "Start" button next to Apache
- Open your web browser and visit:
  ```
  http://localhost/ITS120L-PROJECT/
  ```
  or
  ```
  http://127.0.0.1/ITS120L-PROJECT/
  ```

5. Development
- All project files should be in the `C:\xampp\htdocs\ITS120L-PROJECT` directory
- Edit files directly in this location
- Refresh your browser to see changes
- No need for additional server setup - XAMPP handles everything

### Troubleshooting

If you encounter any issues:
1. Check if XAMPP's Apache server is running (green in XAMPP Control Panel)
2. Verify your files are in the correct htdocs directory
3. Common Apache port conflicts:
   - If port 80 is in use, you can change Apache's port in XAMPP Control Panel
   - Click 'Config' → 'Apache (httpd.conf)' → Change 'Listen 80' to another port (e.g., 8080)
4. If you change ports, access the site using the new port:
   ```
   http://localhost:8080/ITS120L-PROJECT/
   ```
5. Clear your browser cache if needed

### Contributing
1. Fork the repository
2. Create a new branch for your feature
3. Make your changes
4. Submit a pull request

For any additional help or questions, please open an issue in the repository.

### Database Setup in phpMyAdmin

1. Start MySQL Database
   - In XAMPP Control Panel, click "Start" next to MySQL
   - Click "Admin" next to MySQL, or visit:
     ```
     http://localhost/phpmyadmin
     ```

2. Create New Database
   - Click "New" in the left sidebar
   - Enter database name: `hope_for_strays`
   - Select "utf8mb4_general_ci" as the collation
   - Click "Create"

3. Set Up Database User and Password
   - Click on "User Accounts" at the top menu
   - Click "Add user account"
   - Fill in the following details:
     ```
     User name: root
     Host name: localhost
     Password: 1234
     Re-type: 1234
     ```
   - Under "Global privileges", check "Check All"
   - Click "Go" at the bottom to create the user

4. Import Database Structure
   - Select the `hope_for_strays` database from the left sidebar
   - Click the "Import" tab at the top
   - Click "Choose File" and select the `database/hope_for_strays.sql` file from the project
   - Scroll down and click "Import"
   - You should see a success message after import

5. Verify Database Setup
   - Click on `hope_for_strays` in the left sidebar
   - You should see all the tables listed
   - Click on each table to verify the structure

6. Database Connection Settings
   - Update the database connection settings in your project's configuration:
     ```
     Host: localhost
     Username: root
     Password: 1234
     Database: hope_for_strays
     ```

7. Verify Connection
   - Navigate to your project's URL:
     ```
     http://localhost/ITS120L-PROJECT/index.php
     ```
   - If the website loads without any database errors, your connection is successful
   - You should see the homepage with data from the database
   - If you see a blank page or errors:
     - Check the browser's developer console (F12) for error messages
     - Verify your database credentials in the project's configuration file
     - Make sure all tables were imported correctly

### Troubleshooting Database Issues

1. MySQL Connection Issues:
   - Verify MySQL is running in XAMPP Control Panel (should be green)
   - Check if port 3306 is available
   - Double-check your password is correctly set to "1234"
   - Ensure your PHP files have the correct database credentials

2. Import Errors:
   - Check if the SQL file is not corrupted
   - Verify the SQL file uses correct syntax
   - Try importing in smaller chunks if the file is large

3. Access Denied:
   - Make sure you're using username "root" and password "1234"
   - Restart MySQL in XAMPP Control Panel
