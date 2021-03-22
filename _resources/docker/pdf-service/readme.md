Docker html to pdf service with wkhtmltopdf
================================
Build the docker image and run it in daemon mode:

$ docker build -t wkhtmltopdf-http-service .
$ docker run -d --net="host" wkhtmltopdf-http-service
