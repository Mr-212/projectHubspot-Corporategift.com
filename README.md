<!-- Instructions -->
1-Clone project from repo
2-Run composer install from project directory
3-copy .env.example and rename it to .env
4-add database creds to .env
5-add hubspot and corporate gift credentials in confg/constants file, you dont need to provide coporategift token field in constants.php leave it as it is. Rest update with appropraite credentials.
6-run php artisan config:clear, config:cache, route:clear , route:cache, and optimize
7-run php artisan migrate to generate databaase tables.



<!-- Application Structure Controllers-->
AuthController: 
Handles the functionality for user login, signup and forget password.
DashboardController: After user authenticated it will be redirected to dashboard.

HubspotServiceController: 
All HUbspot related activities and methoods are performed under this controller.It also utilize HubspotUtility Class and CoporateGift Connector Class.

KnowledgeBaseController: 
This handles all the pages related knowledge base e.g. setup guide, privacy policy, terms of services etc.





