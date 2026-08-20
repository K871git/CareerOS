# CareerOS — Test Deployment Guide

> **Stack:** React + Vite → **Netlify** &nbsp;|&nbsp; Laravel 13 → **Render** &nbsp;|&nbsp; MySQL → **Railway**
>
> This guide assumes you have GitHub already connected to both Netlify and Render.
> Estimated time: **~30 minutes**

---

## Architecture Overview

```
Browser → Netlify (React frontend)
              ↓ HTTPS API calls
         Render  (Laravel 13 backend)
              ↓ MySQL
         Railway (MySQL database)
```

---

## Step 1 — MySQL Database (Railway)

Railway gives a free MySQL database with no credit card needed for small usage.

1. Go to **[railway.app](https://railway.app)** → Sign in with GitHub
2. Click **New Project** → **Deploy MySQL**
3. After it deploys, click the **MySQL service** → **Variables** tab
4. Copy and save these values (you will need them in Step 2):

| Variable | Where to find |
|----------|--------------|
| `MYSQLHOST` | Variables tab |
| `MYSQLPORT` | Variables tab |
| `MYSQLDATABASE` | Variables tab |
| `MYSQLUSER` | Variables tab |
| `MYSQLPASSWORD` | Variables tab |

---

## Step 2 — Backend (Render)

### 2a — Create the Web Service

1. Go to **[render.com](https://render.com)** → Dashboard → **New +** → **Web Service**
2. Connect your GitHub repo → select the **CareerOS** repository
3. Fill in the settings:

| Setting | Value |
|---------|-------|
| **Name** | `careeros-api` (or anything you like) |
| **Root Directory** | `backend` |
| **Runtime** | `PHP` |
| **Build Command** | `composer install --no-dev --optimize-autoloader` |
| **Start Command** | `php artisan serve --host 0.0.0.0 --port $PORT` |
| **Instance Type** | Free |

4. Click **Create Web Service** (it will fail on first deploy — that's OK, we set env vars next)

---

### 2b — Set Environment Variables on Render

Go to your Web Service → **Environment** tab → Add the following variables one by one:

```
APP_NAME=CareerOS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:ZMSd6beas/GmYY/hwOp1yy9YP+6ebGBuQqnrxT2sUpI=

# ← Fill in with your Netlify URL after Step 3 (example below)
APP_URL=https://careeros-api.onrender.com
FRONTEND_URL=https://your-app.netlify.app

# Database — paste values from Railway Step 1
DB_CONNECTION=mysql
DB_HOST=<MYSQLHOST from Railway>
DB_PORT=<MYSQLPORT from Railway>
DB_DATABASE=<MYSQLDATABASE from Railway>
DB_USERNAME=<MYSQLUSER from Railway>
DB_PASSWORD=<MYSQLPASSWORD from Railway>

# Session & Cache — use file driver (no Redis needed)
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Token expiry — 7 days
SANCTUM_EXPIRATION=10080

# Mail — your existing Brevo credentials
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=<your Brevo login email>
MAIL_PASSWORD=<your Brevo API key>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@careeros.app
MAIL_FROM_NAME=CareerOS

LOG_CHANNEL=stack
LOG_LEVEL=error
```

> **APP_KEY** — use the key from your local `backend/.env` file. Do NOT regenerate it or all existing tokens become invalid.

---

### 2c — Trigger a Redeploy

After setting all env vars:

1. Go to **Deploys** tab → click **Deploy latest commit** (or just push any change to GitHub)
2. Watch the deploy logs — it should finish with `Listening on http://0.0.0.0:...`

---

### 2d — Run Migrations + Seeders (one-time setup)

Once the deploy succeeds:

1. On your Render Web Service → **Shell** tab
2. Run these commands one by one:

```bash
php artisan migrate --force
php artisan db:seed --force
```

> This seeds the database with all tracks, subjects, topics, lessons, and questions.

---

## Step 3 — Frontend (Netlify)

### 3a — Create the Site

1. Go to **[netlify.com](https://netlify.com)** → **Add new site** → **Import an existing project**
2. Connect GitHub → select the **CareerOS** repository
3. Fill in the build settings:

| Setting | Value |
|---------|-------|
| **Base directory** | `frontend` |
| **Build command** | `npm run build` |
| **Publish directory** | `frontend/dist` |

4. Do **not** deploy yet — set the env var first.

---

### 3b — Set Environment Variable

On the site settings screen (or **Site configuration → Environment variables**):

```
VITE_API_URL = https://careeros-api.onrender.com/api
```

> Replace `careeros-api` with whatever name you gave your Render service.
> The URL must end with `/api` — no trailing slash.

5. Now click **Deploy site**

---

## Step 4 — Wire Frontend URL back into Render

After Netlify finishes deploying, you get your live URL (e.g. `https://careeros-abc123.netlify.app`).

1. Go back to **Render** → your Web Service → **Environment** tab
2. Update `FRONTEND_URL` to your exact Netlify URL:
   ```
   FRONTEND_URL = https://careeros-abc123.netlify.app
   ```
3. Render will auto-redeploy with the new value.

> This is required for CORS — without the correct `FRONTEND_URL` the browser will block all API calls.

---

## Step 5 — Verify Everything Works

Open your Netlify URL in a browser and run through this checklist:

- [ ] Landing page loads (no blank screen)
- [ ] Register a new account
- [ ] Login with that account — AuthOverlay animation shows
- [ ] Overview/Dashboard loads with stats
- [ ] Learning → JavaScript → Level 1 → opens lessons
- [ ] Practice → FSD → Language → JavaScript quiz works
- [ ] Profile page loads and saves
- [ ] Logout works — "SEE YOU SOON" overlay shows

---

## Common Issues & Fixes

### API calls fail / CORS error in browser console
- Check that `FRONTEND_URL` on Render exactly matches your Netlify URL (no trailing slash, correct https://)
- Verify `VITE_API_URL` on Netlify ends with `/api`
- After changing any env var on Render, wait for the auto-redeploy to finish

### 500 error on all API calls
- Check Render logs → look for missing env var or failed migration
- Run `php artisan config:clear && php artisan optimize` via the Render Shell

### Netlify page refresh shows 404
- The `frontend/public/_redirects` file handles this automatically — it was added during the security hardening step
- If it's missing, create `frontend/public/_redirects` with content: `/* /index.html 200`

### Database migrations fail
- Check Railway is running and the DB credentials in Render env vars are correct
- Run `php artisan migrate:status` via Render Shell to see which migrations are pending

### Render free tier sleeps after 15 minutes of inactivity
- First request after sleep takes ~30 seconds (spin-up time)
- This is normal for Render's free plan — for always-on testing, upgrade to the $7/mo plan

### Mail / OTP not sending
- Double-check Brevo credentials in Render env vars
- Verify `MAIL_FROM_ADDRESS` is a verified sender in your Brevo account

---

## Re-deploying After Code Changes

| What changed | What to do |
|-------------|-----------|
| Frontend only | Push to GitHub → Netlify auto-deploys |
| Backend only | Push to GitHub → Render auto-deploys |
| New migration | After Render deploys → Shell → `php artisan migrate --force` |
| New seeder | After Render deploys → Shell → `php artisan db:seed --class=YourSeeder --force` |
| Both | Push once → both auto-deploy in parallel |

---

*Guide written for CareerOS MVP testing — ghost-K871 (Kishor_K871), Wakad Pune*
