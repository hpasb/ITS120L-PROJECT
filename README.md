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
