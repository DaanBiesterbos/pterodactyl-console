# Pterodactyl Console

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