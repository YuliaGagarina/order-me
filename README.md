# OrderMe application

OrderMe application is a replica of commercial application aimed to yield reviews to different products.
Thus I cannot share an original piece of code, I recreated a small piece of functionality to demonstrate my coding 
skills.

The provided here prototype implements a REST API and has been created with Laravel leveraging.

To learn how it works, follow the instructions below.
All you need for testing is CLI and Postman.

### Initial Setup

### Start (or create and start) Docker containers:
```bash
docker-compose up -d
```

### Entrance Docker container to run necessary commands:
```bash
docker exec -it om_api_container bash -c 'php artisan key:generate'
docker exec -it om_api_container bash -c 'php artisan migrate'
docker exec -it om_api_container bash -c 'php artisan db:seed'
```

### Stop Docker containers
```bash
docker-compose stop
```

After you fill your local database with seed data, you are can run several requests 
to explore the data:

To get Info about products and their variations and attribute run the next request:
GET, localhost:8008/api/getAllProducts

To get information about campaigns containing product and users requested product run
the next request:
GET, localhost:8008/api/explore/{$PID}

To explore migration functionality run the next request:
POST, localhost:8008/api/migrate_pid
body = [{
    "migrationModifier":""
    "old_product_id":"{$oldPID}",
    "new_product_id":"{$newPID}",
    "vendor_id":"{$vendorId}",
    "campaign_id":"{$campaignId}",
    "review_id":"{$reviewId}",
}];

"migrationModifier": nullable, "", "Campaign", "Rewiew"
"old_product_id" - required
"new_product_id" - required
"vendor_id" - required
"campaign_id" - nullable. Must be set if "migrationModifier" is "Campaign".
If set, product ID will be replaced in orders, campaign_products tables, and duplicate of old product 
will be created in products and product_variations tables.
"review_id" - nullable. Must be set if "migrationModifier" is "Review".
If set, product ID will be replaced in orders for the specified reviewId, and duplicate of old product
will be created in products, campaign_products and product_variations tables.
