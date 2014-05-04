<div class="page-header">
    <h1>Server Configuration</h1>
</div>

<h2>Nginx</h2>
<textarea class="code" data-type="nginx">
listen 80;
server_name  my-application.dev;
root   <path-to-application-root>/public;
index  index.php index.html index.htm;
client_max_body_size 2M;

location / {
    try_files $uri $uri/ /index.php$is_args$args;
}

location ~ \.php$ {
    fastcgi_pass   127.0.0.1:9000;
    fastcgi_index  index.php;
    fastcgi_param APPLICATION_ENV development;
    fastcgi_param  SCRIPT_FILENAME  <path-to-application-root>/public$fastcgi_script_name;
    include        fastcgi_params;
}
</textarea>

<h2>Apache</h2>