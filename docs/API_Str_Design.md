1) API versoning : 

 routes/api.php 

 Route::prefix('v1')->group(function(){
    //apis routes
 });

API format : /api/v1/endpoint

2) Dir structure Design : 

Folder structure 
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   ├── Requests/
│   └── Resources/
│
├── Services/
└── Actions/


Controllers → Handle HTTP requests only
Requests → Validation
Resources → API response formatting
Services → Business logic

3) Authentication API's : 
/api/v1/auth 
endpoints : 
POST /register 
POST /login 
POST /logout 
GET /me


4) Profile API's : 
api/v1/profile

endpoints:
GET /profile 
PUT /profile

5) Learning API's :

/api/v1/tracks 

GET /tracks
GET    /tracks/{track}
GET    /subjects/{subject}
GET    /topics/{topic}
GET    /lessons/{lesson}

6) Assesment API's : 

/api/v1/questions

GET    /questions
POST   /attempts
GET    /attempts/{attempt}

7) API Response Standard : 
```json
Success :
{
    "success":true,
    "message":"Profile fetched successfully",
    "data":{},
}
```
Error :
```json
{
    "success":false,
    "message":"Validation failed",
    "errors":{},
}
```

8) Authentication Middleware : 
Route::middleware('auth:sanctum')

Public:
register 
login 

Protected:
profile
progress
assessment