# Facebook Anonymous Publisher

[![StyleCI](https://styleci.io/repos/59216090/shield?style=flat)](https://styleci.io/repos/59216090)

# Feature

- Image Upload

- HD images

- Append Link to Hashtag

- Url Detect

- Daily, Weekly and Monthly Top Posts

- Block Words Replace ( Support Simple and Traditional Chinese )

- Block Blacklist Ips ( Support using CloudFlare )

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

The final step is set up the schedule crontab, please refer the following link

> https://laravel.com/docs/5.2/scheduling#introduction
