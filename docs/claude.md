# CareerOS - Engineering Rules

## Project Overview

CareerOS is a Software Engineering Career Growth Platform.

The goal is to help:

- Placement preparation students
- Junior Software Developers (0-2 years)
- Mid-level Software Engineers (2-5 years)

prepare for Software Engineering and Full Stack Developer interviews.

CareerOS is not just a question bank.

The product focuses on:

- Learning engineering concepts
- Practicing interview questions
- Understanding real-world scenarios
- Measuring progress
- Improving weak areas


## Development Philosophy

This project is being built as both:

1. A real software product
2. A learning journey to improve software engineering skills


Always consider:

- Maintainability
- Scalability
- Security
- Developer experience
- Production readiness


Do not optimize only for "working code".

Build with professional engineering practices.


---

# Tech Stack

## Backend

- Laravel 13
- PHP 8.3+
- MySQL
- Laravel Sanctum


## Frontend

- React
- TypeScript
- Vite


## Planned Future Technologies

- Redis
- Queues
- Laravel Horizon
- Docker
- CI/CD
- AI Integration


---

# Architecture Principles


## Backend

Laravel application is responsible for:

- Business logic
- Authentication
- Authorization
- Data management
- API development
- Learning workflows
- Assessment logic


## Frontend

React application is responsible for:

- User interface
- User interaction
- Client-side state
- API communication


Communication:

React Client

↓

REST API

↓

Laravel Backend

↓

MySQL Database


---

# MVP Development Rules


Do not build future features before MVP requirements.

Current MVP focus:

- Authentication
- User profile
- Career goals
- Learning tracks
- Subjects
- Topics
- Lessons
- Questions
- Progress tracking


Future features:

- Coding playground
- AI evaluation
- Mock interviews
- Advanced analytics


Do not introduce complexity for future requirements unless there is a clear current need.


---

# Backend Coding Standards


Follow:

- PSR-12
- Laravel conventions
- Clean code principles


## Controllers

Rules:

- Keep controllers thin.
- Controllers handle HTTP requests only.
- Do not place business logic inside controllers.


Bad:

```php
Controller {

 calculateScore();

 updateProgress();

 sendNotification();

}