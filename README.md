# Facebook Anonymous Publisher

[![StyleCI](https://styleci.io/repos/59216090/shield?style=flat)](https://styleci.io/repos/59216090)

# Installation

Clone the repository and set up environment configuration

```sh
git clone https://github.com/BePsvPT/Facebook-Anonymous-Publisher.git

cp .env.example .env
```

Install packages

```sh
composer install --no-dev -o
```

Set the application key and run the database migrations, make sure you set up the environment configuration before migrate

```sh
php artisan key:generate

php artisan migrate
```

The next step is using your browser to visit the install page to set up the rest configuration

> http://your-domain/install
