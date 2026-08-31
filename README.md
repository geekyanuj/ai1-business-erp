## PHP Configuration (Shared Hosting)

To ensure smooth processing of large files and long-running operations (such as PDF processing or imports), update the following settings in your **`php.ini`** or **`.htaccess`** file on shared hosting.

### Required PHP Settings

```ini
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
