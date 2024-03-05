git pull -q origin master
composer install --no-dev
#php bin/console md2html
./bin/console doctrine:migrations:migrate
