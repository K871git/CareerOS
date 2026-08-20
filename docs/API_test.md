Base URL : http://localhost:8000/api

1. User Registration :

POST /v1/auth/register

Headers
content-type : application/json
accept : application/json

input :

```json
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

output :

```json
{
  "success": true,
  "message": "Registration successful.",
  "data": {
    "user": {
      "id": 2,
      "name": "Test User",
      "email": "test@example.com",
      "email_verified_at": null,
      "created_at": "2026-08-05T05:13:16.000000Z"
    },
    "token": "2|ZLaJFc1UXwlhEWqZKqmblhlTFlMZ2Z89YDJgYArG920a5e53"
  }
}
```

2. User : Login

POST /api/v1/auth/login

Headers
content-type : application/json
accept : application/json

Data :

```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

Response:

```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "user": {
      "id": 2,
      "name": "Test User",
      "email": "test@example.com",
      "email_verified_at": null,
      "created_at": "2026-08-05T05:13:16.000000Z"
    },
    "token": "3|uyb2ktZBQLplcNrXikXLMSx4rCExMOeIMrtAMQTGcfeec206"
  }
}
```

3. Authenticated User

GET v1/auth/me

Headers :
Accept : application/json
Authorization : Bearer <token>

No body :

response :

```json
{
  "success": true,
  "message": "Authenticated user retrieved.",
  "data": {
    "id": 2,
    "name": "Test User",
    "email": "test@example.com",
    "email_verified_at": null,
    "created_at": "2026-08-05T05:13:16.000000Z"
  }
}
```

4. Update Profile

PUT /api/v1/profile

Headers :
Accept, content-type, Authorization

Data:

```json
{
  "current_role": "Junior Developer",
  "experience_level": "junior",
  "target_role": "Full Stack Developer",
  "career_goal": "Land a full stack role in a product company"
}
```

response :

```json
{
  "success": true,
  "message": "Profile updated successfully.",
  "data": {
    "id": 1,
    "user_id": 2,
    "current_role": "Junior Developer",
    "experience_level": "junior",
    "target_role": "Full Stack Developer",
    "career_goal": "Land a full stack role in a product company",
    "created_at": "2026-08-05T07:16:28.000000Z",
    "updated_at": "2026-08-05T07:16:28.000000Z"
  }
}
```

5. Get Profile

GET /v1/profile

Accept, Authorization

Response :

```json
{
  "success": true,
  "message": "Profile retrieved successfully.",
  "data": {
    "id": 1,
    "user_id": 2,
    "current_role": "Junior Developer",
    "experience_level": "junior",
    "target_role": "Full Stack Developer",
    "career_goal": "Land a full stack role in a product company",
    "created_at": "2026-08-05T07:16:28.000000Z",
    "updated_at": "2026-08-05T07:16:28.000000Z"
  }
}
```

6. Store Career Assessment

POST /v1/career-assessment

content-type, accept, authorization

Body:

```json
{
  "target_role": "Full Stack Developer",
  "skills": [
    {
      "skill_id": 1,
      "level": "Intermediate",
      "score": 80
    },
    {
      "skill_id": 2,
      "level": "Beginner",
      "score": 45
    }
  ]
}
```

Response:

```json
{
  "success": true,
  "message": "Career assessment saved successfully.",
  "data": {
    "target_role": "Full Stack Developer",
    "skills": [
      {
        "id": 1,
        "skill_id": 1,
        "skill_name": "PHP",
        "level": "Intermediate",
        "score": 80
      },
      {
        "id": 2,
        "skill_id": 2,
        "skill_name": "Laravel",
        "level": "Beginner",
        "score": 45
      }
    ]
  }
}
```

7. Update Career Assessment

PUT v1/career-assessment

content-type, accept, authorization

Body:

```json
{
  "target_role": "Backend Engineer",
  "skills": [
    {
      "skill_id": 1,
      "level": "Advanced",
      "score": 90
    }
  ]
}
```

Response:

```json
{
  "success": true,
  "message": "Career assessment updated successfully.",
  "data": {
    "target_role": "Backend Engineer",
    "skills": [
      {
        "id": 1,
        "skill_id": 1,
        "skill_name": "PHP",
        "level": "Advanced",
        "score": 90
      },
      {
        "id": 2,
        "skill_id": 2,
        "skill_name": "Laravel",
        "level": "Beginner",
        "score": 45
      }
    ]
  }
}
```

8. Get Career Assessment

GET /v1/career-assessment

Accept, Authorization

Response :

```json
{
  "success": true,
  "message": "Career assessment retrieved successfully.",
  "data": {
    "target_role": "Backend Engineer",
    "skills": [
      {
        "id": 1,
        "skill_id": 1,
        "skill_name": "PHP",
        "level": "Advanced",
        "score": 90
      },
      {
        "id": 2,
        "skill_id": 2,
        "skill_name": "Laravel",
        "level": "Beginner",
        "score": 45
      }
    ]
  }
}
```

9. Logout

POST /v1/auth/logout

Accept, Authorization

Response :

```json
{
  "success": true,
  "message": "Logged out successfully.",
  "data": []
}
```
