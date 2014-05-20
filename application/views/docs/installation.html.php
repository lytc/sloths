<div class="page-header">
    <h1>Installation</h1>
</div>

<p><b>Step 1:</b> Install Composer</p>
<p>Install <a href="https://getcomposer.org/download/">Composer</a> if you don't have Composer installed yet:</p>
<br>

<p><b>Step 2:</b> Create a <code>composer.json</code> file in your project root</p>
<code>&lt;path-to-your-project&gt;/composer.json</code>
<textarea class="code" data-type="javascript">
    {
      "require": {
        "lytc/sloths": "~3.*"
      }
    }
</textarea>
<br>

<p><b>Step 3:</b> Install via composer command <code>$ composer install</code></p>

<br>

<p><b>Step 4:</b> Setting up your <a href="/sloths/docs/application-structure.html">application structure</a> & <a href="/sloths/docs/server-configuration.html">config web server</a></p>