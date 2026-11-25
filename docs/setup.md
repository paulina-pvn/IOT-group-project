# ComfortRoom: Setup Guide

This guide explains how to install and run the website locally.

ComfortRoom is an IoT group project that helps students and teachers find suitable rooms based on environmental conditions, available facilities, and personal preferences.

## 1. Requirements

To run the website, you need:

- Git
- PHP 8 or newer

For now, no database, additional frameworks, or package installations are required.

## 2. Clone the GitHub Repository

Open a terminal and run:

```bash
git clone https://github.com/paulina-pvn/IOT-group-project.git
cd IOT-group-project
```

Open the project in VS Code.

If you have the `code` command installed:

```bash
code .
```

Otherwise, open VS Code and select **File → Open Folder**.

## 3. Install PHP

### macOS

If you have Homebrew installed:

```bash
brew install php
```

Check that PHP is installed:

```bash
php -v
```

You should see the installed PHP version.

If you don't have Homebrew, visit https://brew.sh/ and follow the installation instructions first.

### Windows

1. Visit https://windows.php.net/download/
2. Download a suitable PHP 8.x ZIP package for your system.
3. Extract it to `C:\php`.
4. Open a PowerShell terminal.
5. Verify the installation:

```powershell
& "C:\php\php.exe" -v
```

If PHP displays its version, the installation worked.

## 4. Run the Website

Open the project folder in VS Code.

Open a terminal using **Terminal → New Terminal**.

Make sure you are in the root folder:

```text
IOT-group-project/
```

### macOS

Run:

```bash
php -S localhost:8000 -t src
```

### Windows

Run:

```powershell
& "C:\php\php.exe" -S localhost:8000 -t src
```

Keep the terminal open while using the website.

## 5. Open the Website

In your browser, visit:

http://localhost:8000

The website should automatically open the homepage.

## 6. Stop the Server

Return to the terminal running PHP and press:

**Ctrl + C**

This stops the local development server.

## 7. Troubleshooting

### PHP command not found

PHP may not be installed or may not be available in your PATH.

On macOS, try:

```bash
php -v
```

On Windows, use the full PHP executable path:

```powershell
& "C:\php\php.exe" -v
```

### 404 Not Found

Make sure you started the server from the repository root using:

```bash
php -S localhost:8000 -t src
```

Check that `src/index.php` exists.

### CSS is not loading

Check that the stylesheet exists:

```text
src/assets/css/style.css
```

In `src/pages/index.php`, the stylesheet should be linked as:

```html
<link rel="stylesheet" href="../assets/css/style.css">
```

### Port 8000 is already in use

Stop the other server, or run:

```bash
php -S localhost:8001 -t src
```

Then visit http://localhost:8001.

## 8. Future Setup

As the project develops, this guide will be updated with instructions for:

- Database configuration
- Backend REST APIs
- Decision support algorithm
- Raspberry Pi integration
- Sensor data collection
- Testing

For now, the website runs locally using PHP's built-in development server.

**Note:** The built-in PHP server is intended for local development, not production deployment.
