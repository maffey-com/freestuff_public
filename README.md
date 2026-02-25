# Clone
```
git clone git@github.com:maffey-com/freestuff_public.git
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
echo 1 > ./storage/site_files/temporary_listing_ids.txt
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