<?php $this->setLayout('default') ?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <br>
            <br>
            <ul class="nav">
                <li>
                    <a href="#"><b>Getting Started</b></a>
                    <ul>
                        <li><a href="/sloths/docs/installation.html">Installation</a></li>
                        <li><a href="/sloths/docs/system-requirements.html">System Requirements</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><b>Application</b></a>
                    <ul>
                        <li><a href="/sloths/docs/application-structure.html">Application Structure</a></li>
                        <li><a href="/sloths/docs/server-configuration.html">Server Configuration</a></li>
                        <li><a href="/sloths/docs/routing.html">Routing</a></li>
                        <li><a href="/sloths/docs/request.html">Request</a></li>
                        <li><a href="/sloths/docs/response.html">Response</a></li>
                        <li><a href="#">View</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><b>Database</b></a>
                    <ul>
                        <li><a href="/sloths/docs/databases/connection.html">Connection</a></li>
                        <li><a href="/sloths/docs/databases/query-builder.html">Query Builder</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/sloths/docs/orm/define-model.html"><b>ORM</b></a>
                    <ul>
                        <li><a href="/sloths/docs/orm/define-model.html">Defining Models</a></li>
                        <li>
                            <a href="/sloths/docs/orm/model-overview.html">Model Interaction</a>
                            <ul>
                                <li><a href="/sloths/docs/orm/model-overview.html">Overview</a></li>
                                <li><a href="/sloths/docs/orm/model.html">Model</a></li>
                                <li><a href="/sloths/docs/orm/collection.html">Collection</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/sloths/docs/orm/relationship-overview.html">Relationship</a>
                            <ul>
                                <li><a href="/sloths/docs/orm/relationship-overview.html">Overview</a></li>
                                <li><a href="/sloths/docs/orm/relationship-has-one.html">Has One</a></li>
                                <li><a href="/sloths/docs/orm/relationship-has-many.html">Has Many</a></li>
                                <li><a href="/sloths/docs/orm/relationship-belongs-to.html">Belongs To</a></li>
                                <li><a href="/sloths/docs/orm/relationship-has-many-through.html">Has Many Through</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>
                <li>
                    <a href="#"><b>Development Tools</b></a>
                    <ul>
                        <li><a href="#">Console</a></li>
                        <li><a href="#">Model Generator</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="col-md-9">
            <?= $this->content() ?>
        </div>
    </div>
</div>

<br>

<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-3">
            <hr>
            <div id="disqus_thread"></div>
            <script type="text/javascript">
                /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                var disqus_shortname = 'sloths-framework'; // required: replace example with your forum shortname

                /* * * DON'T EDIT BELOW THIS LINE * * */
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
            </script>
            <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
            <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
        </div>
    </div>
</div>
