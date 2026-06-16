<div align="center">
  🍺
  
  # ARTavern
  ### Art Belongs to the Artist.
  
  [![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
  [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)
  [![Status](https://img.shields.io/badge/status-preview-orange.svg)]()

  <p>
    <strong>The first platform built for artists, not algorithms.</strong><br/>
    Draw. Collaborate. Protect. All in one place.
  </p>
</div>

---

## 🎥 See It in Action (Basically nothing right now)

[![ArtAvern Demo](https://img.youtube.com/vi/zY2lNQP15iM/maxresdefault.jpg)](https://www.youtube.com/watch?v=zY2lNQP15iM)

---

## ⚠️ Work in Progress

Artavern is currently in **preview mode**. Features are actively being developed and things may change. You're welcome to explore, test, and share feedback — it all helps shape what this becomes.

---

## 📌 The Problem

Artists working online today lack a space that truly supports them. Current platforms fall short in several key areas:

- **Fragmented workflows** — There's no dedicated space where artists can draw and publish from the same place. Most workflows require jumping between multiple apps just to post a single piece.
- **Limited collaboration** — Artists have few ways to learn from each other or work together in real time. Structured one-on-ones, shared canvases, and community-driven skill-building are largely absent.
- **AI scraping without consent** — Artwork posted online is constantly at risk of being scraped and used for AI training without the artist's knowledge or permission.
- **Inaccessible references** — Finding good reference material is often expensive or out of reach. Many artists resort to whatever they can find online rather than what they actually need.
- **No copyright recourse** — When work gets stolen or misused, most artists have nowhere to turn and little understanding of their legal options.
- **Devaluation by AI-generated content** — AI art continues to flood online spaces, drowning out human-made work and treating creative labor as disposable content.

Artists deserve better.

---

## ✨ What ArtAvern Does

Artavern was built around a single principle: **art belongs to the artist.** Every feature reflects that.

| Feature | What It Solves |
|---|---|
| 🎨 **Built-in Whiteboard & Portfolio** | Draw and publish directly on the platform — no app-hopping. Your portfolio builds automatically as you create. |
| 🤝 **Collaboration & Learning** | One-on-one tutoring, shared canvases, and randomized matchmaking for skill-building and friendly competition. |
| 🛡️ **AI Protection** | Every piece is automatically watermarked with your choice and processed to block use in AI training datasets. |
| 📸 **Reference Commissions** | Hire IRL models or 3D modelers — both free and paid options — to create references tailored to your needs. |
| ⚖️ **Copyright Support** | Direct access to legal resources and copyright agents when your work is stolen or misused. |
| 🚫 **No AI Art** | A strict no-AI-art policy. This space is reserved entirely for work made by human hands. |

---

## 🔧 Running Locally (Preview)

Artavern is still in active development. To run it locally, follow these steps:

### What You'll Need
- A web server with PHP support (XAMPP, WAMP, MAMP, or similar)
- MySQL (comes bundled with the above)
- A browser

### Setup Steps

1. **Download the project**
   ```bash
   git clone https://github.com/your-username/artavern.git
Or download the ZIP and extract it.

2. Move the files

Place the entire artavern folder into your web server's document root:
XAMPP: C:/xampp/htdocs/artavern/
WAMP: C:/wamp64/www/artavern/
MAMP: /Applications/MAMP/htdocs/artavern/

3. Start your local server
Fire up your web server (Apache) and MySQL through your stack's control panel (XAMPP Control Panel, WAMP Manager, etc.).

4. Set up the database
Open phpMyAdmin at http://localhost/phpmyadmin
Create a new database called artavern
Import the provided SQL file from the database/ folder (if included)

5. Configure the connection
Open the config file (likely config.php or .env) and update the database credentials:
```php
// Example
DB_HOST = 'localhost'
DB_USER = 'root'
DB_PASS = ''
DB_NAME = 'artavern'
```
6.Open it up
Navigate to http://localhost/artavern in your browser.
