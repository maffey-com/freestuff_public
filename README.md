# Clone

```
git clone https://github.com/maffey-com/freestuff_public.git
```

# PHP Composer

```
cd composer
composer install
```

# Docker

You need to have docker installed locally.

## Build

```
docker compose up -d --build
```

## Run

```
docker compose up -d
```

# Database

You can connect to the local database with the following credentials

```
root:thing1@localhost:3306
```

To create an empty database and import some starting data, run the following commands

```
docker exec -i freestuff-mysql sh -c 'mysql -uroot -pthing1 < /tmp/freestuff.sql'
```

Create a temporary listing ids file

```
mkdir ./storage/site_files
echo 1 > ./storage/site_files/temporary_listing_ids.txt
```

Create required storage directories

```
mkdir ./storage/cache
mkdir ./storage/site_files/flood
```

# Migration (skip if setting up fresh)

```sql
ALTER TABLE listing_request ADD COLUMN no_show ENUM('y','n') NOT NULL DEFAULT 'n';
ALTER TABLE listing ADD INDEX idx_user_id (user_id);
ALTER TABLE listing ADD INDEX idx_district_id (district_id);
ALTER TABLE listing ADD INDEX idx_listing_status (listing_status);
ALTER TABLE user ADD INDEX idx_email (email);
ALTER TABLE user ADD INDEX idx_mobile (mobile);
```

# Usage

local frontend url:
http://localhost:8087/

Backend url:
http://localhost:8087/cr

Email is captured by mailhog:
http://localhost:8025/

test user credentials:

```
email: admin@freestuff.co.nz
password: password
```
