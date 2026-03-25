# 💸 Budget Tracker — Personalized & Collective Financial Management

> **Version:** 1.0 (January 2026)
> **Project Owner:** Rihab Mahdi
> **Status:** Specification Phase

---

## 📖 Table of Contents

- [Project Overview](#project-overview)
- [Key Features](#key-features)
- [Technical Specifications](#technical-specifications)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [Development Roadmap](#development-roadmap)
- [Security & Compliance](#security--compliance)

---

## 🗂 Project Overview

Budget Tracker is a comprehensive solution for **personal and collective financial management**. It combines traditional budget tracking with gamification elements, consistency streaks, and an AI-powered assistant.

The application is designed for:
- **Individuals** managing their personal finances
- **Groups** such as families, roommates, or friends who share a budget

---

## ✨ Key Features

### 💰 Financial Management

| Feature | Description |
|---|---|
| **Transaction Tracking** | Add, modify, or delete income and expenses with categories, dates, and descriptions |
| **Visual Dashboard** | Real-time monitoring of balance, total income/expenses, and distribution charts |
| **Smart Receipt Scan (OCR)** | Upload JPEG/PNG images to automatically extract transaction data via Tesseract.js or Google Vision |
| **Budgeting** | Set monthly limits per category with visual alerts and track savings goals |

### 🎮 Gamification & Engagement

- **Streaks** — A consistency counter tracking consecutive days of use, resetting after 48 hours of inactivity.
- **Rewards System** — Earn points for activity:
  - `+5 pts` per transaction logged
  - `+20 pts/day` for streaks of 4 or more consecutive days
- **Avatars & Badges** — Unlock customizable avatar items and earn achievements like **"Gold Saver"** or **"Streak Master"**.

### 👥 Collaboration & AI

- **Collective Mode** — Create groups with specific roles (`Admin` / `Member`) and split shared expenses.
- **AI Chatbot** — A conversational interface providing proactive financial advice and natural language queries.

---

## 🛠 Technical Specifications

### Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.1+, MVC Architecture, PDO |
| **Frontend** | HTML5, CSS3, Tailwind CSS, JavaScript ES6+ |
| **Data Visualization** | Chart.js |
| **Database** | MySQL |
| **AI / OCR** | OpenAI / Mistral API, Tesseract.js / Google Vision |

---

## 🗄 Database Schema

The database is composed of the following core tables:

| Table | Purpose |
|---|---|
| `users` | Tracks profiles, streaks, points, and levels |
| `transactions` | Stores all financial records |
| `groups` | Manages collective budgets |
| `group_members` | Handles roles and access within groups |
| `avatar_items` | Stores gamification avatar assets |
| `achievements` | Tracks earned badges and rewards |

---

## ⚙️ Installation

> Detailed setup instructions will be added upon release of Phase 1.

**Prerequisites:**
- PHP 8.1+ / Laravel
- MySQL
- Node.js (for Tesseract.js)
- Composer


## 🔒 Security & Compliance

- **Password Security** — Passwords hashed using `password_hash()`.
- **SQL Injection Protection** — All database interactions use PDO prepared statements.
- **CSRF Protection** — CSRF tokens enforced on all forms and state-changing requests.
- **Privacy (RGPD)** — Explicit user consent required before any AI-based data processing.
- **Accessibility** — UI conforms to **WCAG 2.1 AA** standards.

---

## 🗺 Development Roadmap

```
Phase 1 ── Authentication & Core Database
Phase 2 ── Transactions & Category Management
Phase 3 ── Dashboards & Budgeting Tools
Phase 4 ── OCR Integration & Gamification
Phase 5 ── Collective Management & AI Assistant
```

---

## 📄 License

This project is proprietary. All rights reserved © 2026 Rihab Mahdi.