# Generate image
docker build -t chatbot:v1 .

# Run a container
```
docker run -d -p 80:80 --name chatbot chatbot:v1
```

# Run a container using a local source folder
## 1. Run composer install for php 5 on local folder
```
docker run -v $(pwd)/source:/app composer/composer:php5 install
```

## 2. Run the container linking the local folder
```
docker run -d -p 8080:80 --name chatbotLocal -v $(pwd)/source:/var/www/html chatbot:v1
```
