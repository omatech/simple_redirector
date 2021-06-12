# Description

**simple_redirector** allow you to setup a redirection project in no time!

# Setup

Clone the project and setup the .env to be able to connect to your database

1. Set the .env variable of the destination domain, for example:

```
REDIRECT_DESTINATION_DOMAIN=https://www.omatech.com
```

2. Create a csv file semi-colon separated with two columns (original_uri;redirect_uri)

Example data (you can find this sample data in storage/sample):

```
original_uri;redirect_uri
/blog/es/quienes-somos/;/es/quienes-somos
/es/blog/que-hacemos;/es/que-hacemos
/blog/es/proyectos;/es/proyectos
```

By default the table used to store redirects is omatech_simple_redirects you can override this setting setting in .env:
```
REDIRECTS_TABLE=omatech_simple_redirects
```

3. To load the redirects CSV into the database use the command:
```
php artisan simple_redirector:load <path to your csv file>
```

For example:
```
php artisan simple_redirector:load storage/sample/redirects.csv
```

By default the command ADDS the new urls, only replacing records if original_uri previously exists.

The first time the command is launched it creates automatically the redirects table if it doesn't exists, you don't have to run any migration script.

If you want to start over with a completly new set of urls you can force a refresh using the --refresh flag:

```
php artisan simple_redirector:load <path to your csv file> --refresh
```

Following our previous example:
```
php artisan simple_redirector:load storage/sample/redirects.csv --refresh
```

# Behaviour config

There are two more config variables you can set in the .env file

```
REDIRECT_HOME_URI - default / you can change that if for example your home page is /home/index.html or anytihing else to avoid redirection loops once the destination is reached. (starts with slash)

CHECK_EXISTENCE_BEFORE_REDIRECT - Default false, this adds an additional roundtrip but checks if the destination url is valid, if it's not then the redirection will be made to the REDIRECT_HOME_URI
```

You can change the behaviour of the redirection login in web.php