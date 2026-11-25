# ComfortRoom — Developer Guide

This guide explains how our team organizes the project and collaborates using GitHub.

For installation and instructions on running the website, see [setup.md](setup.md).

## 1. Project Structure

Our current project structure is:

```text
IOT-group-project/
├── docs/
│   ├── setup.md
│   └── development.md
├── Project_sensor_data/
├── project-deliverable/
├── src/
│   ├── index.php
│   ├── pages/
│   │   └── index.php
│   └── assets/
│       ├── css/
│       │   └── style.css
│       └── images/
└── .gitignore
```

### Folder purposes

| Location | Purpose |
|---|---|
| `src/` | Website application files |
| `src/index.php` | Redirect to the homepage |
| `src/pages/` | Website pages |
| `src/assets/css/` | Stylesheets |
| `src/assets/images/` | Images and icons |
| `Project_sensor_data/` | Provided room and sensor datasets |
| `project-deliverable/` | Use cases, class diagrams, and project documentation |
| `docs/` | Setup instructions, developer guide, and other documentation |

Additional folders will be introduced when the backend, database, and hardware integrations are implemented.

## 2. Technologies

Our initial website uses:

- PHP for serving pages
- HTML for page structure
- CSS for styling
- JavaScript when interactive functionality is needed

The website is currently being developed without a database or frontend framework.

Additional technologies will be documented when the team agrees on them.

## 3. Coding Conventions

To keep the code readable and consistent:

- Use lowercase filenames, for example `find-room.php`.
- Use descriptive names for files and variables.
- Put website pages inside `src/pages/`.
- Put CSS files inside `src/assets/css/`.
- Put images inside `src/assets/images/`.
- Keep styling in CSS rather than repeating it in HTML.
- Use comments when code needs additional explanation.

As the project grows, keep data access, decision logic, and page presentation separate.

## 4. Running the Website

From the project root:

### macOS

```bash
php -S localhost:8000 -t src
```

### Windows

```powershell
& "C:\php\php.exe" -S localhost:8000 -t src
```

Open http://localhost:8000 in your browser.

See [setup.md](setup.md) for installation instructions.

## 5. Team Organization

Our group consists of six members.

### Software Team — 3 Members

Responsible for:

- Website development
- Database and backend
- Decision support algorithm
- REST APIs
- User and administrator interfaces

### Hardware Team — 3 Members

Responsible for:

- Arduino and sensors
- Raspberry Pi configuration
- Sensor communication
- Hardware integration
- Physical testing

Both teams will collaborate on connecting sensor measurements to the website and backend.

## 6. Working with GitHub

We use separate branches so that multiple team members can work without accidentally changing each other's work.

The `main` branch contains the shared version of the project.

New features should be developed on separate branches.

### Step 1 — Check your current branch

Open the VS Code terminal:

```bash
git status
```

Check whether there are uncommitted changes before switching branches.

### Step 2 — Update your local main branch

If your working tree is clean:

```bash
git switch main
git pull --ff-only origin main
```

If Git reports local changes or an error, resolve them before continuing. Do not discard another person's work.

### Step 3 — Create a feature branch

For example:

```bash
git switch -c feature/find-room
```

Use descriptive branch names such as:

```text
feature/website-setup
feature/find-room
feature/database
feature/decision-engine
feature/sensor-integration
feature/admin-dashboard
```

### Step 4 — Develop and test

Make your changes on your feature branch.

Before committing:

- Check that your code works.
- Run the website locally.
- Check that existing features still work.
- Make sure no unrelated files were changed.

### Step 5 — Commit your changes

Check the branch and modified files:

```bash
git status
```

Stage only the files you intend to commit. For example:

```bash
git add src/pages/find-room.php
```

Commit with a descriptive message:

```bash
git commit -m "Add find room page"
```

### Step 6 — Push your branch

```bash
git push -u origin feature/find-room
```

Replace `feature/find-room` with your actual branch name.

Pushing a feature branch does not automatically merge it into `main`.

### Step 7 — Create a Pull Request

On GitHub:

1. Open the repository.
2. Select **Compare & pull request**.
3. Set `main` as the base branch.
4. Choose your feature branch.
5. Describe your changes and how you tested them.
6. Request a teammate's review.

After approval, merge the pull request according to the team's agreement.

Do not force-push or merge another teammate's work without agreement.

## 7. Important Collaboration Rules

- Do not develop new features directly on `main`.
- Always check your branch before committing.
- Do not overwrite another member's work.
- Communicate when changing files shared by multiple features.
- Review changes before merging.
- Do not commit passwords, API keys, or private configuration files.
- Keep the setup documentation updated whenever installation steps change.

## 8. Future Development

The website will be expanded gradually.

Planned features include:

1. Homepage
2. Find a Room interface
3. Room ranking results
4. User preferences and AHP comparisons
5. Backend and database integration
6. Sensor data integration
7. Administrator monitoring dashboard
8. Swagger REST API documentation

Each feature should be developed and tested separately before integration.

This document will be updated as our architecture and development process evolve.
