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

**IMPORTANT:** If you don't load any original_uri at all or if the url parsed is not in the database the user will be redirected to the root of REDIRECT_DESTINATION_DOMAIN anyway.

You can change the behaviour of the redirection login in web.php