# Pterodactyl Console

## Installation

### Local Installation
Clone this repository. If you want to run this application locally you will need to install **PHP 8** or greater. In addition, you will need to install the **Composer** package manager.
Run composer install to download the application dependencies.

```shell
composer install
```

### Configuration

The following environment variables must be defined.
Either on system level, the terminal or via a configuration file.
Symfony will prioritize environment variables on system level.
Therefore, if you intend to use the docker setup there is no need for a local config file. **Skip this step and continue to the docker setup**

For now copy the .env.local.dist file to .env.local
All .local files are automatically ignored by git to avoid accidentally pushing secrets to the git repository.
Configure the environment variables and you should be good to go.

```dotenv
PTERODACTYL_BASE_URL=https://your-hostname
PTERODACTYL_API_KEY=your-api-key
```


## Docker

This repository provides a working docker setup to get started quickly.
To use docker I recommend to complete your configuration first.
And when you are done, check out the bottom of this document for the steps needed to run the application in docker.

## Configuration

The following environment variables must be defined.
Either on system level, the terminal or via a configuration file.
Symfony will prioritize environment variables on system level.

For now copy the .env.local.dist file to .env.local
All .local files are automatically ignored by git to avoid accidentally pushing secrets to the git repository.
Configure the environment variables and you should be good to go.

```dotenv
PTERODACTYL_BASE_URL=https://your-hostname
PTERODACTYL_API_KEY=your-api-key
```

## Using Docker

### Requirements

You will need to install docker and docker compose. Check out the docker documentation for instructions on how to instal docker in your OS.

- Docker Compose 3.7 or greater
- Docker

### Configuration

Go to the ./docker directory and copy .env.dist. 
Make sure to correctly configure these environment variables in `./docker/.env`.

```dotenv
PTERODACTYL_BASE_URL=https://your-hostname
PTERODACTYL_API_KEY=your-api-key
```

Change other settings if you need to.

### Build the containers

From within the docker container, run the following commands.

```shell
docker-compose build
```

```shell
docker-compose up
```

*Open localhost:9006 in your browser to verify that the application is running. In case you changed your port in .env, obviously, use this port instead*

Note that at this time, there are no webpages. Perhaps in the future. But why throw something away this fancy, right? In any case... We currently only care about the commandline.

### Using the command line interface

Open a shell into your docker container.  To do this, you will need the name of te container, or the container ID.
I have named the containers, so this should be as easy as this:

```shell
docker exec -it pterodactyl-console-php /bin/sh
```

If you for whatever reason do not know the ID or the name of a container (Dude? I just said I named them. How did you mess that up?! xD) you can easily figure it out.
Run this command to display your containers.
```shell
docker ps
```

You should see something like this:
```shell
CONTAINER ID   IMAGE          COMMAND                  CREATED         STATUS              PORTS                                   NAMES
daab008c460b   docker_nginx   "/docker-entrypoint.…"   2 minutes ago   Up About a minute   0.0.0.0:9006->80/tcp, :::9006->80/tcp   pterodactyl-console-nginx
3a9b7526c1dd   docker_php     "docker-php-entrypoi…"   2 minutes ago   Up About a minute   9000/tcp                                pterodactyl-console-php
```

You can either use the container ID or the name of the container to get a shell.
Assuming the example above, this should work as well.

```shell
docker exec -it 3a9b7526c1dd /bin/sh
```

#### Run the console application

Simply run `bin/console` and everything should work as expected. Enjoy! :)



#### Optional: HTTP Requests 
*This feature is for Jetbrains IDE's only (Phpstorm, IntelliejIDEA)*

To quickly communicate with the pterodactyl API it can be quite useful to send HTTP requests using the Webservices tool in Phpstorm.
Copy http/http-client.private.env.json.dist to http/http-client.private.env.json and configure the base url and api key here as well.


## Usage 


### Available Commands

Run `bin/console` to see all available commands or run `bin/console list server` commands to see which commands are available in the *server* namespace.

At this time the following commands are available:
- **server:get**        Dump server information
- **server:kill**       Send kill signal to a server.
- **server:list**       List your servers
- **server:reinstall**  Reinstall a server
- **server:restart**    Send restart signal to a server.
- **server:send-cmd**   Sends a command to a server
- **server:start**      Send start signal to a server.
- **server:stop**      Send stop signal to a server.

Use the `--help` option to get more information on how to use a command.

**For example:**
```bash
bin/console server:start --help
```

### Interactive vs non-interactive

Most server commands can be used either interactively, or non interactive. Generally speaking, if you  provide the server ID as an argument, the command will be executed non interactively.
If you invoke the commands from a cronjob for example, and you want to be 100% sure that the command does not run interactively, you can provide the `--no-interaction` option.
This functionality is a builtin option from Symfony and is taken into consideration in these commands.

#### Example 1:  Run reinstall interactively

```shell
> bin/console server:reinstall

Reinstall Server
================

Select a server
  [0] Some Server (054c5d81-e2bd-4a8c-86f1-6a5ea0dbce9d)
  [1] Some Other Server,  (69b0c2d3-5fb6-4772-bf8c-b831daf6e3d8)
```

#### Example 2: Start server non-interactively
```shell
> bin/console server:start 054c5d81-e2bd-4a8c-86f1-6a5ea0dbce9d --no-interaction

Start Server
============
                                                                                                               
 [OK] Start signal was sent successfully!                                                                               
                                                                                                                        
```


### Start, Stop, Restart and Kill

By default the start, stop, restart and kill commands will check if the selected server is currently being (re-)installed. 
If so, the command will wait and poll the server until the installation is completed.
To disable this behaviour you can use the `--ignore-install` option.

#### For example:
```shell
> bin/console server:start 054c5d81-e2bd-4a8c-86f1-6a5ea0dbce9d --ignore-install

Start Server
============
                                                                                                               
 [OK] Start signal was sent successfully!                                                                               
                                                                                                                        
```