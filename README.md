# Event Booking System

A full-stack event booking application built with **Laravel 12**, **Inertia.js**, **Vue 3**, and **Tailwind CSS**.

Users can create events, book seats, manage booking statuses, and track all changes through an activity timeline.

---

## Tech Stack

- **Backend:** Laravel 12, Eloquent ORM, Form Requests, Service Layer
- **Frontend:** Vue 3 (Composition API), Inertia.js, Tailwind CSS, TipTap (rich text editor)
- **Auth:** Laravel Sanctum (API token authentication)
- **Database:** MySQL
- **Testing:** PHPUnit (19 tests, 45 assertions)

---

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- MySQL

### Installation

```bash
# Clone the repository
git clone https://github.com/DimitarVitanov/booking-system.git


# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate
```

### Database Setup

Create a MySQL database named `event_booking`, then update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_booking
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

### Running the Application

```bash
# Start Laravel dev server
php artisan serve

# In a separate terminal, start Vite
npm run dev
```

Visit the URL shown in the terminal (default: `http://localhost:8000`, port may vary)

### Running Tests

Tests use a separate MySQL database (`event_booking_testing`) to protect development data. Create it first:

```sql
CREATE DATABASE event_booking_testing;
```

Then run:

```bash
php artisan test
```

---

## Custom Artisan Commands

| Command | Description |
|---|---|
| `php artisan app:demo` | Populate the database with 20 demo events and random bookings |
| `php artisan app:clear` | Remove all events, bookings, and activity logs (with confirmation) |

---

## API Endpoints

All API routes are prefixed with `/api`. Protected routes require a Bearer token via Sanctum.

### Testing with Postman

1. **Register** — Send a `POST` request to `/api/register` with the JSON body (name, email, password, password_confirmation). Copy the `token` from the response.
2. **Set up Authorization** — In Postman, go to the **Authorization** tab, select **Bearer Token**, and paste your token.
3. **Make requests** — All subsequent requests to protected endpoints will automatically include the token. You can now hit any event or booking endpoint.
4. **Alternatively**, you can add the header manually: `Authorization: Bearer YOUR_TOKEN`

### Middleware

| Middleware | Applied To | Description |
|---|---|---|
| `auth:sanctum` | All routes except `/api/register` and `/api/login` | Requires a valid Bearer token. Returns `401 Unauthorized` without one. |

### Authentication (Public)

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Register a new user and receive a token |
| POST | `/api/login` | Login and receive a token |

### Authentication (Protected)

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/logout` | Revoke the current token |

### Events (Protected)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/events` | List all events (paginated) |
| POST | `/api/events` | Create a new event |
| GET | `/api/events/{id}` | View a single event |
| DELETE | `/api/events/{id}` | Delete an event |

### Bookings (Protected)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/events/{event_id}/bookings` | List bookings for an event |
| POST | `/api/events/{event_id}/bookings` | Create a booking |
| PATCH | `/api/bookings/{id}` | Update booking status |

---

## Example API Requests

### Register

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Create Event (with token)

```bash
curl -X POST http://localhost:8000/api/events \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "Laravel Meetup",
    "description": "Monthly gathering for Laravel developers.",
    "start_date": "2026-04-01 18:00:00",
    "end_date": "2026-04-01 21:00:00",
    "capacity": 100
  }'
```

### Create Booking

```bash
curl -X POST http://localhost:8000/api/events/1/bookings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "email_address": "attendee@example.com",
    "seats_booked": 3
  }'
```

### Update Booking Status

```bash
curl -X PATCH http://localhost:8000/api/bookings/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"status": "confirmed"}'
```

---

## Project Structure

```
app/
├── Console/Commands/        # app:demo, app:clear
├── Http/
│   ├── Controllers/
│   │   ├── Api/             # REST API controllers (EventController, BookingController, AuthController)
│   │   └── EventController  # Inertia web controller
│   ├── Middleware/
│   ├── Requests/            # StoreEventRequest, StoreBookingRequest, UpdateBookingStatusRequest
│   └── Resources/           # EventResource, BookingResource
├── Models/                  # Event, Booking, ActivityLog, User
├── Observers/               # EventObserver, BookingObserver
└── Services/                # EventService, BookingService

resources/js/
├── Components/
│   └── TiptapEditor.vue     # Rich text editor component
└── Pages/Events/
    ├── Index.vue             # Event listing with cards, progress bars, status badges
    ├── Create.vue            # Event creation form with rich text editor
    └── Show.vue              # Event detail with bookings, booking form, activity timeline
```

---

## Business Rules

- Bookings cannot exceed event capacity
- Only `pending` and `confirmed` bookings count toward capacity; `cancelled` bookings free up seats
- Booking status must be one of: `pending`, `confirmed`, `cancelled`
- Event `end_date` must be after `start_date`
- Events past their `end_date` are marked as "Ended" and cannot accept new bookings
- Deleting an event automatically cancels all its pending bookings (via Observer)

---

## Features Beyond Requirements

### Implemented Optional Features (from spec)
- **Pagination** on event and booking lists
- **Search/filter** bookings by email address
- **API authentication** via Laravel Sanctum (token-based)
- **Event progress** (percentage of seats booked with color-coded progress bars)

### Additional Features
- **Inertia.js + Vue 3 SPA frontend** with Tailwind CSS
- **Rich text editor** (TipTap) for event descriptions with Tailwind Typography styling
- **Activity Log & Timeline** — Eloquent Observers on Event and Booking models log all create/update/delete actions with before/after values, displayed as a visual timeline on the event detail page
- **SweetAlert2** for delete confirmation dialogs
- **Custom Artisan commands** (`app:demo`, `app:clear`) for demo data management
- **N+1 query optimization** — `withSum` on the index query to avoid per-event database calls
- **Event status badges** — Available, Almost Full, Sold Out, Ended
- **Dynamic page titles** via Inertia's `<Head>` component

---

## Assumptions

- No web-based user authentication is required — the web frontend is public-facing (as the spec focuses on API auth as optional)
- The API and web frontend are separate concerns: API uses Sanctum tokens, web uses Inertia sessions
- Past events can be created (the spec only requires `end_date` after `start_date`, no restriction on past dates)
- All dates are stored and displayed in UTC
- The `description` field supports HTML content via the TipTap rich text editor

---

## Production Improvements

If this system were deployed in production, several improvements would be implemented to ensure scalability, reliability, and long-term business value.

### Email Notifications

Automated email notifications would be introduced to improve communication between the platform and attendees. Confirmation emails provide immediate feedback after a booking is made, while reminder emails sent prior to the event help reduce no-show rates. Cancellation notifications ensure transparency and maintain a professional user experience for both attendees and organizers.

### Payment Integration

Integrating payment providers such as Stripe or PayPal would allow the platform to support paid events and ticketing. This enables event organizers to monetize their events directly through the platform while maintaining secure and reliable payment processing. From a business perspective, this also creates opportunities for the platform to generate revenue through service fees or commissions.

### Rate Limiting

API rate limiting would be implemented to protect the system from abuse, automated bots, or excessive traffic spikes. This ensures fair resource usage across users and helps maintain system stability during high-demand periods such as popular event registrations.

### Caching

Introducing caching (e.g., Redis) for frequently accessed data such as event listings can significantly improve response times and reduce database load. Faster performance improves user experience and ensures the platform remains responsive as usage grows.

### Queue / Background Jobs

Background job processing would be used for tasks that do not need to run synchronously, such as sending email notifications or generating ticket PDFs. Offloading these tasks to queue workers keeps the main application responsive and improves overall system performance.

### Web Authentication

Adding web-based authentication (e.g., Laravel Breeze or Jetstream) would allow users to create accounts, manage their bookings, and view their event history. This improves user retention and enables the platform to provide a more personalized experience.

### Role-Based Access Control

Role-based access control would allow different permission levels within the platform. For example, administrators could manage events and bookings, while regular users would only be able to create and manage their own reservations. This structure improves security and operational control.

### Event Updates

Supporting event updates through dedicated endpoints would allow organizers to modify event details such as schedules, descriptions, or capacity. This flexibility ensures that the platform can adapt to real-world event management scenarios where changes are common.

### Soft Deletes

Implementing soft deletes ensures that events and bookings are not permanently removed from the system. Instead, they are archived, allowing administrators to retain historical records for analytics, auditing, and operational review.

### File Uploads

Allowing event organizers to upload event cover images or venue photos improves the presentation of event listings. Rich media content helps increase engagement and provides users with better context about the events they are considering attending.

### API Versioning

Introducing API versioning (e.g., `/api/v1/`) ensures that future changes to the API can be introduced without breaking existing integrations. This approach supports long-term platform evolution while maintaining stability for clients relying on the current API.

### Docker

Containerizing the application with Docker would standardize development and deployment environments. This reduces configuration inconsistencies between development, staging, and production systems, making deployments more reliable.

### CI/CD Automation

Implementing continuous integration and continuous deployment pipelines (e.g., GitHub Actions) would automate testing and deployment processes. This improves development velocity while maintaining code quality and reducing the risk of production errors.

### Monitoring and Error Tracking

Monitoring and error tracking tools such as Laravel Telescope or Sentry would provide visibility into application performance and runtime errors. Early detection of issues enables faster resolution and helps maintain a reliable platform.

### Seat Assignment

Rather than simply reserving a number of seats, a seat assignment system would allow attendees to select specific seats within a venue layout. This provides a more interactive booking experience and is particularly valuable for events with assigned seating such as conferences, theaters, or concerts. From a UI perspective, this could be implemented as a visual seat map where users click to select their preferred seats.

### Booking Expiration

Pending bookings should have a time-based expiration policy. For example, if a booking is not confirmed within 30 minutes, it would automatically be cancelled and the reserved seats released back into the available pool. This prevents capacity from being indefinitely locked by incomplete bookings and ensures fair access for other attendees. This could be implemented using a Laravel scheduled command or a queued job that periodically checks for expired pending bookings.

### Data Export

Providing export functionality (CSV or PDF) allows event organizers to download booking lists and attendee information. This feature supports operational needs such as attendance tracking, reporting, and post-event analysis.
