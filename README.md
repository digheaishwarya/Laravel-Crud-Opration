
# 1. Clone the repository

```bash
# from a terminal
git clone https://github.com/digheaishwarya/Laravel-Crud-Opration.git
```

# 2. Install PHP dependencies

```cmds
composer install
composer create-project laravel/laravel Laravel-Crud-Opration
php artisan make:migration create_users_table
php artisan migrate
php artisan make:controller UserController
php artisan serve

```

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=users-microservice
DB_USERNAME=root
DB_PASSWORD=
```


# 3. Migrations & Model

If the repo already contains migrations, run:

```bash
php artisan migrate
```

If you don't have migrations and want to create one for the `users` table used in the project, use this migration example:

```php
// database/migrations/xxxx_xx_xx_create_users_table.php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('subject');
        // store path relative to storage/app/public, e.g. "profile_images/xxx.jpg"
        $table->string('profile_image')->nullable();
        $table->timestamps();
    });
}
```

Then run `php artisan migrate`.

---

# 4. Storage link (for public access to profile images)

Laravel stores files in `storage/app/public` when you use the `public` disk. Create the symlink so images are accessible from `public/storage`:

```bash
php artisan storage:link
```

When you save files with `$request->file('profile_image')->store('profile_images', 'public')`,
`$user->profile_image` will be saved like `profile_images/abcd.jpg` and the public URL is `asset('storage/'.$path)` -> `/storage/profile_images/abcd.jpg`.

---

# 5. Run the app

```bash
php artisan serve
# defaults to http://127.0.0.1:8000
```

---

# 6. Routes (API endpoints)

Your `routes/web.php` contains the endpoints used by the frontend. API-style endpoints:

```
GET    /users          -> UserController@index    (list users)
POST   /users          -> UserController@store    (create new user)
GET    /users/{id}     -> UserController@show     (get single user)
POST   /users/{id}     -> UserController@update   (update user using POST + _method=POST or you can change to PUT/PATCH)
DELETE /users/{id}     -> UserController@destroy  (delete user)
```

# 7. Controller behaviour (important notes)

* The controller uses validation:

```php
$request->validate([
    'name' => 'required|string|max:255',
    'subject' => 'required|string|max:255',
    'profile_image' => 'nullable|image|max:2048'
]);
```
# 8. Frontend examples (AJAX)

Basic JS / jQuery examples (already present in your repo):

# 9. Database structure

Simple `users` table columns used by the project:

* `id` (bigint, primary)
* `name` (string)
* `subject` (string)
* `profile_image` (string, nullable) — stores path relative to `storage/app/public` like `profile_images/abc.jpg`
* `created_at`, `updated_at` (timestamps)

You can extend the model and migrations as needed.
cmd 
php artisan migarte


* Create the migration file and factory for you.
* Convert your routes/controllers to `api.php` and return `API Resource` responses.
* Open a PR in your GitHub repo with modal frontend + improved delete logic + Storage::delete fix.

Tell me which of the three you'd like me to prepare.
