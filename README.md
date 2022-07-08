#### Run locally

First, copy the config file and set the settings you wish:

```bash
cp config.sample.php config.php
```

### Option 1: Run with JavaScript, PHP and Tailwind compiler

```bash
nvm use
npm i
composer install
npm run dev
npm run dev-styles # In separate console
```

Now you can visit:

http://localhost:8080/


### Option 2: Run with PHP and development Tailwind compiler

First, set `development => 'true'` in `config.php`.

```bash
composer install
php -S localhost:8080 -t src
```

Now you can visit:

http://localhost:8080/

#### Subfolder configuration

If you put this project into a subfolder, edit the `$baseFolder` variable in `index.php`