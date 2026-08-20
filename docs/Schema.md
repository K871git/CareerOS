# CareerOS MVP Database Schema (Phase 1)

> **Principle:** Start with the **domain model**, not SQL tables. Tables come after identifying the business entities.

---

# 1. Users

**Purpose:** Authentication and basic account information.

```text
users
------
id
name
email
password
email_verified_at
remember_token
created_at
updated_at
```

**Note:**
Use Laravel's default authentication tables.

---

# 2. User Profiles

**Purpose:** Career-related information.

```text
user_profiles
-------------
id
user_id
current_role
experience_level
target_role
career_goal
created_at
updated_at
```

### Relationship

```text
User
 └── hasOne UserProfile
```

---

# 3. Skills

**Purpose:** Master list of all skills.

### Examples

- PHP
- Laravel
- React
- MySQL
- Git
- Docker

```text
skills
------
id
name
slug
category
description
created_at
updated_at
```

---

# 4. User Skills

**Purpose:** Stores assessment results.

```text
user_skills
-----------
id
user_id
skill_id
level
score
created_at
updated_at
```

### Example

```text
Laravel

Intermediate

78
```

### Relationship

```text
User
    hasMany UserSkills

Skill
    hasMany UserSkills
```

---

# 5. Learning Tracks

### Examples

- Software Engineering
- Backend
- Frontend
- Full Stack
- DevOps

```text
learning_tracks
---------------
id
title
slug
description
display_order
created_at
updated_at
```

---

# 6. Subjects

### Example

- Database
- Networking
- OOP
- React
- Laravel

```text
subjects
--------
id
learning_track_id
title
slug
description
display_order
created_at
updated_at
```

### Relationship

```text
Track

hasMany Subjects
```

---

# 7. Topics

### Example

- Indexes
- Normalization
- Transactions
- Joins

```text
topics
------
id
subject_id
title
slug
description
display_order
created_at
updated_at
```

---

# 8. Lessons

**Each topic contains lessons.**

```text
lessons
-------
id
topic_id
title
content
estimated_minutes
display_order
created_at
updated_at
```

---

# 9. Questions

**Supports both MCQ and Theory from day one.**

```text
questions
---------
id
topic_id
type
difficulty
question
explanation
created_at
updated_at
```

### Enums

#### type

```text
MCQ

THEORY
```

#### difficulty

```text
Easy

Medium

Hard
```

---

# 10. Question Options

**Only used for MCQs.**

```text
question_options
----------------
id
question_id
option_text
is_correct
created_at
updated_at
```

---

# 11. User Progress

**Tracks lesson completion.**

```text
user_progress
-------------
id
user_id
lesson_id
status
completed_at
created_at
updated_at
```

### Status

```text
NOT_STARTED

IN_PROGRESS

COMPLETED
```

---

# 12. Assessment Attempts

**Stores test attempts.**

```text
assessment_attempts
-------------------
id
user_id
score
total_questions
started_at
submitted_at
created_at
updated_at
```

---

# 13. Assessment Answers

**Stores each submitted answer.**

```text
assessment_answers
------------------
id
attempt_id
question_id
selected_option_id
text_answer
is_correct
marks
created_at
updated_at
```

---

# Relationships Overview

```text
User
 ├── UserProfile
 ├── UserSkills
 ├── UserProgress
 └── AssessmentAttempts

Skill
 └── UserSkills

LearningTrack
 └── Subjects
      └── Topics
           ├── Lessons
           └── Questions
                └── QuestionOptions

AssessmentAttempt
 └── AssessmentAnswers
```

---

# Engineering Review

This schema is:

- ✅ Normalized (avoids duplicate data)
- ✅ Easy to extend in later phases (analytics, recommendations, AI)
- ✅ Follows clear one-to-many and many-to-many relationships where appropriate
- ✅ Minimal for the MVP—no premature tables for badges, achievements, notifications, or AI

---

# Recommendation

Freeze this as **Schema v1.0**.

Build the MVP against it, and only evolve it when a real product need emerges.

This gives you a stable foundation without over-engineering.



# How i can improve this ?
-> i wanna connect the dots for this project: like user practice question and its gonna link with past and ready for future data while clearing the concept