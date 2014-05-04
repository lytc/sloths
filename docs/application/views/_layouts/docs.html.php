<?php $this->setLayout($this->path . '/_layouts/default') ?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <br>
            <br>
            <ul class="nav">
                <li>
                    <a href="#"><b>Getting Started</b></a>
                    <ul>
                        <li><a href="/lazy/docs/installation.html">Installation</a></li>
                        <li><a href="/lazy/docs/system-requirements.html">System Requirements</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><b>Application</b></a>
                    <ul>
                        <li><a href="/lazy/docs/application-structure.html">Application Structure</a></li>
                        <li><a href="/lazy/docs/server-configuration.html">Server Configuration</a></li>
                        <li><a href="#">Request</a></li>
                        <li><a href="/lazy/docs/routing.html">Routing</a></li>
                        <li><a href="#">View</a></li>
                        <li><a href="#">Response</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#"><b>Database</b></a>
                    <ul>
                        <li><a href="/lazy/docs/databases/connection.html">Connection</a></li>
                        <li><a href="/lazy/docs/databases/query-builder.html">Query Builder</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/lazy/docs/orm/define-model.html"><b>ORM</b></a>
                    <ul>
                        <li><a href="/lazy/docs/orm/define-model.html">Defining Models</a></li>
                        <li>
                            <a href="/lazy/docs/orm/model-overview.html">Model Interaction</a>
                            <ul>
                                <li><a href="/lazy/docs/orm/model-overview.html">Overview</a></li>
                                <li><a href="/lazy/docs/orm/model.html">Model</a></li>
                                <li><a href="/lazy/docs/orm/collection.html">Collection</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/lazy/docs/orm/relationship-overview.html">Relationship</a>
                            <ul>
                                <li><a href="/lazy/docs/orm/relationship-overview.html">Overview</a></li>
                                <li><a href="/lazy/docs/orm/relationship-has-one.html">Has One</a></li>
                                <li><a href="/lazy/docs/orm/relationship-has-many.html">Has Many</a></li>
                                <li><a href="/lazy/docs/orm/relationship-belongs-to.html">Belongs To</a></li>
                                <li><a href="/lazy/docs/orm/relationship-has-many-through.html">Has Many Through</a></li>
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