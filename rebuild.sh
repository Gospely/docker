cd ubuntu && docker build --no-cache=true -t gospel-ubuntu .
cd ../nginx && docker build --no-cache=true -t gospel-nginx .
cd ../nginx-php && docker build --no-cache=true -t gospel-nginx-php .
