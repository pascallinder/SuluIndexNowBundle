# SuluIndexNowBundle

**Sulu bundle that integrates IndexNow API to instantly inform search engines and web crawlers about latest content changes.**
See [here](https://www.indexnow.org/index) for more information.

## Installation

This bundle requires PHP 8.2 and Sulu 2.6

1. Open a command console, enter your project directory and run:

```console
composer require linderp/sulu-index-now-bundle
```

If you're **not** using Symfony Flex, you'll also need to add the bundle in your `config/bundles.php` file:

```php
return [
    //...
    Linderp\SuluIndexNowBundle\SuluIndexNowBundle::class => ['all' => true],
];
```

2. Register the new routes by adding the following to your `routes_admin.yaml`:

```yaml
SuluIndexNowBundle:
    resource: "@SuluIndexNowBundle/Resources/config/routes_admin.yml"
```

3. If you don't have the IndexNow setup already, generate your key [here](https://www.bing.com/indexnow/getstarted). Then follow the instructions and put the file in the /public folder:
4. Extend your .env file
```dotenv
INDEX_NOW_API_KEY=xxx
```
5. Reference the frontend code by adding the following to your `assets/admin/package.json`:

```json
"dependencies": {
    "sulu-index-now-bundle": "file:../../vendor/linderp/sulu-index-now-bundle/src/Resources/js"
}
```

5. Import the frontend code by adding the following to your `assets/admin/app.js`:

```javascript
import "sulu-index-now-bundle";
```

6. Build the admin UI:

```bash
cd assets/admin
npm run build
```
