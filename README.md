# Chatbot Exercise
This exercise simulates a chatbot. With the exposed lifecycle:
1. Bot will wait until a user sends a first message (hello for example).
2. Bot will present a welcome message: "Hello! I will ask you some questions ok?"
3. Bot will inmediately present the first question and wait for the user first answer:
    a. if the answer is not valid it will ask the same question again.
    b. if the answer is valid and there are questions left it will ask the next question
4. When all the answer are answered correctly the bot will return the thanks message.

## The questions
 - What is your name?
 - What is your email?
 - How old are you?


## Requirements
 - Chat shoud be SPA
 - Maintain the conversation even if users refresh the page.
 - Memorise the valid information provided by the user so the bot can display it at the end.
 - Use php 5.5

# Run the bot
The project requires to run the bot in php 5.5. So tho ensure this environment the project is prepared to run inside a Docker conainter.

**Requirement**
 - Docker: *https://www.docker.com*
 
## Generate docker image and run a container
In the root folder of this project, run: 
```
$ docker build -t chatbot:v1 .
$ docker run -d -p 80:80 --name chatbot chatbot:v1
```
The first time build proces can take up to 10 minutes, because it need to install all composer dependencies.

When the process has finished you can obpen bot in `http://localhost`

## Run a container using a local source folder
This is the way you can run the page in docker but you can still editing the code with out rebuild the image.
```
$ docker run -v $(pwd)/source:/app composer/composer:php5 install
$ docker build -t chatbot:v1 .
$ docker run -d -p 8080:80 --name chatbotLocal -v $(pwd)/source:/var/www/html chatbot:v1
```
When the process has finished you can obpen bot in `http://localhost:8080`

The first command is for install composer components in your local folder using composer under php5 environment.