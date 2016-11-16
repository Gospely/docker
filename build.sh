cd ubuntu && docker build -t gospel-ubuntu .
&& cd ../nginx && docker build -t gospel-nginx .
&& cd ../nginx-php && docker build -t gospel-nginx-php .
