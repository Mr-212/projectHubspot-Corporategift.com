<!-- Instructions -->
1-Clone project from repo
2-Run composer install from project directory
3-copy .env.example and rename it to .env
4-add database creds to .env
5-add hubspot and corporate gift credentials in confg/constants file, you dont need to provide coporategift token field in contants.php leave it as it is. Rest update with appropraite.
6-run 
php artisan config:clear, config:cache, route:clear , route:cache, and optimize
7-run php artisan migrate to generate databaase tables
