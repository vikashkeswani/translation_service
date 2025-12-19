# Translation Management Service

A Laravel 12 API-driven service to manage translations for multiple languages/locales with tagging, search, and JSON export functionality.
Designed for performance, scalability, and clean code, following PSR-12 and SOLID principles.

# Features
- Store translations for multiple locales (e.g., en, fr, es)
- Tag translations for context (e.g., mobile, web)
- CRUD endpoints for translations, languages, and tags
- Search translations by key, value, or tag
- JSON export endpoint for frontend applications (Vue.js, React)
- Token-based authentication using Laravel Sanctum
- Efficient handling of large datasets (>100k translations)
- Docker-ready for easy setup
- OpenAPI documentation available at openapi,yml file

# Tech Stack
- PHP 8.3 + Laravel 12
- MySQL / MariaDB
- Docker + Docker Compose
- Sanctum for authentication
- OpenAPI for API documentation

# Design Choices

  # Database Schema
  - translations table stores keys and values as JSON for flexibility across languages.
  - languages table allows adding new locales dynamically with an is_active flag.
  - translation_tags table supports context tagging for translations.

# Scalability
- Eager loading with with(['language', 'tag']) for optimized queries.
- Export endpoint uses LazyCollection / cursor() to handle millions of records efficiently.
- Indexed key column for fast search queries.

# SOLID & PSR-12 Compliance
- Service layer (TranslationService) separates business logic from controllers.
- Requests and validations use FormRequest classes (TranslationRequest, LanguageRequest).
- API Resources (TranslationResource, LanguageResource) ensure consistent JSON responses.

# Performance Optimization
- JSON export response is streamed to minimize memory usage.
- Pagination applied on index/search endpoints.
- Factory seeding supports 100k+ records for stress testing.

# Security
- All protected endpoints use Sanctum token-based authentication.
- Input validation to prevent SQL injection or invalid data.

# Documentation & Testing
- OpenAPI/Swagger documentation with interactive testing (/api/documentation).
- Unit, feature, and performance tests included to ensure reliability and speed.

# Setup Instructions
1. Clone Repository
- git clone https://github.com/vikashkeswani/translation_service.git
- cd translation_service

2. Create .env File
  - Copy .env.example:
  - cp .env.example .env

Update database credentials:
  - DB_CONNECTION=mysql
  - DB_HOST=mysql
  - DB_PORT=3306
  - DB_DATABASE=translation_service
  - DB_USERNAME=user
  - DB_PASSWORD=secret

  - CACHE_DRIVER=redis
  - REDIS_CLIENT=phpredis
  - REDIS_HOST=redis
  - REDIS_PASSWORD=null
  - REDIS_PORT=6379

3. Build & Start Docker Containers
 - docker-compose up -d --build

- App runs on http://localhost:8000
- MySQL container runs on port 3306

4. Run Migrations & Seed Database
 - docker exec -it translation_service php artisan migrate
 - docker exec -it translation_service php artisan db:seed 

User Credentials
email: test@example.com
password: test@123



5. Testing
- Run unit, feature, and performance tests:
- docker-compose exec app php artisan test
- Performance tests simulate high load on CRUD, search, and export endpoints.
- Postman collection added in repository so you can check apis using postman as well.
  
# Performance Testing
 - Performance tests ensure the service meets the following requirements:

Endpoint	Max Response Time
- /translations	< 200ms
- /translation/search	< 200ms
- /export/translations	< 500ms

1. Using Laravel Performance Test
  A sample TranslationPerformanceTest.php is included under tests/Performance. Run:
  docker-compose exec app php artisan test --testsuite=Performance

- Measures response times for index, search, and export endpoints.
- Validates that response times meet performance thresholds.

2. Manual Benchmarking via Artisan Tinker
  docker-compose exec app php artisan tinker

- $start = microtime(true);
- $response = Http::withToken('YOUR_SANCTUM_TOKEN')->get('http://localhost:8000/api/export/translations');
- $end = microtime(true);
- echo "Export took " . (($end - $start) * 1000) . " ms\n";

Useful for measuring real response times on large datasets.


API Endpoints Overview
Endpoint	Method	Auth	Description
- /register	POST	❌	Register new user
- /login	POST	❌	Login user
- /logout	POST	✅	Logout user
- /languages	GET, POST	✅	List/Create languages
- /languages/{id}/toggle	PATCH	✅	Toggle active status
- /translations	GET, POST	✅	List/Create translations
- /translations/{id}	GET, PUT, DELETE	✅	Retrieve/Update/Delete translation
- /translation/search	GET	✅	Search translations
- /export/translations	GET	❌	Export all active translations in JSON

# Notes
- Adding a new language: Use /languages POST endpoint with code and name. The translation values JSON in the translations table will support the new locale automatically.
- Large datasets: Export and index endpoints are optimized for memory and speed using cursor() and pagination.
- Authentication: Pass Authorization: Bearer <token> in headers for all protected routes.

# Author
Vikash Keswani – Senior PHP/Laravel Developer
