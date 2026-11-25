# ComfortRoom

ComfortRoom is an IoT group project for helping students and teachers find rooms that fit their needs. The project brings together room facilities, environmental sensor readings, room availability, and user preferences. It also describes an administrator view and REST APIs for monitoring and integration.

## Current prototype

The repository contains a small PHP website prototype. The home page links to a room search page with example room data and an illustrative comfort score. The prototype is not yet connected to the sensor JSON files or a database; ranking rules, integrations, and other requirements are documented separately.

## Run locally

Requirements: Git and PHP 8 or newer.

From the repository root, start PHP's built-in development server:

```bash
php -S localhost:8000 -t src
```

Then open [http://localhost:8000](http://localhost:8000). Stop the server with `Ctrl+C`.

For installation help and troubleshooting, see [docs/setup.md](docs/setup.md).

## Repository structure

| Path | Contents |
|---|---|
| `src/` | PHP website prototype and CSS assets |
| `Project_sensor_data/` | Sample room facilities and sensor data in JSON format |
| `project-deliverable/` | Class diagram, use case descriptions, stakeholder stories, and story map |
| `docs/` | Setup guide, development guide, requirements, and assignment checklist |

## Project documentation

- [Requirements](docs/requirements.md) — MVP scope, stakeholders, functional requirements, assumptions, and open questions.
- [Setup guide](docs/setup.md) — local installation, run instructions, and troubleshooting.
- [Development guide](docs/development.md) — project organization and team workflow.
- [Assignment checklist](docs/assigment_checklist.md) — deliverables and suggested build order.
- [Use case descriptions](project-deliverable/use-cases/use-case-descriptions.md)
- [Stakeholder stories](project-deliverable/stakeholder-and-story/stories.md)
- [Story map](project-deliverable/stakeholder-and-story/story-map.md)
- [Class diagram](project-deliverable/class-diagram/class-diagram.md)

## Technologies

The current website uses PHP, HTML, and CSS. PHP's built-in server is intended for local development only. The database, decision engine, APIs, and hardware integrations are described as project requirements and remain future implementation work.
