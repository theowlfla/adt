<!DOCTYPE html>
<?php
require_once(dirname(__FILE__) . '/lib/functions.php');
checkCaches();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="refresh" content="120">
    <title>Acceptance Live Instances</title>
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico"/>
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" type="text/css" rel="stylesheet" media="all">
    <link href="//netdna.bootstrapcdn.com/bootswatch/2.1.1/spacelab/bootstrap.min.css" type="text/css" rel="stylesheet" media="all">
    <link href="./style.css" media="screen" rel="stylesheet" type="text/css"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
    <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-1292368-28']);
        _gaq.push(['_trackPageview']);

        (function () {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();

    </script>
</head>
<body>
<!-- navbar ================================================== -->
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="/"><?=$_SERVER['SERVER_NAME'] ?></a>
            <ul class="nav">
                <li class="active"><a href="/">Home</a></li>
                <li><a href="/features.php">Features</a></li>
                <li><a href="/servers.php">Servers</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- /navbar -->
<!-- Main ================================================== -->
<div id="wrap">
<div id="main">
<div class="container-fluid">
<div class="row-fluid">
<div class="span12">
<p>These instances are deployed to be used for acceptance tests. Terms of usage and others documentations about this service are detailed in our <a href="https://wiki-int.exoplatform.org/x/loONAg">internal wiki</a>.</p>
<table class="table table-bordered table-hover">
<thead>
<tr>
    <th class="col-center">Status</th>
    <th class="col-center">Name</th>
    <th class="col-center">Snapshot Version</th>
    <th class="col-center" colspan="4">Feature Branch</th>
    <th class="col-center">Built</th>
    <th class="col-center">Deployed</th>
    <th class="col-center">&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
$all_instances = getGlobalAcceptanceInstances();
foreach ($all_instances as $plf_branch => $descriptor_arrays) {
    ?>
    <tr>
        <td colspan="11" style="background-color: #363636; color: #FBAD18; font-weight: bold;">
            <?php
            if ($plf_branch === "4.0.x") {
                echo "Platform " . $plf_branch . " based build (R&D)";
            } elseif ($plf_branch === "UNKNOWN") {
                echo "Unclassified projects";
            } else {
                echo "Platform " . $plf_branch . " based build (Maintenance)";
            }
            ?>
        </td>
    </tr>
    <?php
    foreach ($descriptor_arrays as $descriptor_array) {
        if ($descriptor_array->DEPLOYMENT_STATUS == "Up")
            $status = "<img width=\"16\" height=\"16\" src=\"/images/green_ball.png\" alt=\"Up\"  class=\"left\"/>&nbsp;Up";
        else
            $status = "<img width=\"16\" height=\"16\" src=\"/images/red_ball.png\" alt=\"Down\"  class=\"left\"/>&nbsp;Down !";
        $matches = array();
        if (preg_match("/([^\-]*)\-(.*\-.*)\-SNAPSHOT/", $descriptor_array->PRODUCT_VERSION, $matches)) {
            $base_version = $matches[1];
            $feature_branch = $matches[2];
        } elseif (preg_match("/(.*)\-SNAPSHOT/", $descriptor_array->PRODUCT_VERSION, $matches)) {
            $base_version = $matches[1];
            $feature_branch = "";
        } else {
            $base_version = $descriptor_array->PRODUCT_VERSION;
            $feature_branch = "";
        }
        ?>
        <tr>
            <td><?php if ($descriptor_array->DEPLOYMENT_ENABLED) {
                    echo $status;
                } ?></td>
            <td>
                <?php
                $product_html_label = "-UNSET-";
                if (empty($descriptor_array->PRODUCT_DESCRIPTION)) {
                    $product_html_label = $descriptor_array->PRODUCT_NAME;
                } else {
                    $product_html_label = $descriptor_array->PRODUCT_DESCRIPTION;
                }
                if (!empty($feature_branch)) {
                    $product_html_label = "<span class=\"muted\">" . $product_html_label . "</span>&nbsp;&nbsp;-&nbsp&nbsp&nbsp" . $feature_branch;
                }
                ?>
                <?php if ($descriptor_array->DEPLOYMENT_ENABLED) { ?>
                    <a href="<?=$descriptor_array->DEPLOYMENT_URL?>" target="_blank" rel="tooltip" title="Open the instance in a new window"><i class="icon-home"></i> <?=$product_html_label?></a>
                    <?php if (!empty($descriptor_array->SPECIFICATIONS_LINK)) { ?>
                        <a rel="tooltip" title="Specifications" href="<?=$descriptor_array->SPECIFICATIONS_LINK?>" target="_blank" class="pull-right">&nbsp;<i class="icon-book"></i></a>
                    <?php } ?>
                <?php } else { ?>
                    <?= $product_html_label ?>
                <?php } ?>
            </td>
            <td class="col-center"><?php if ($descriptor_array->DEPLOYMENT_ENABLED) { ?><a href="<?=$descriptor_array->DEPLOYMENT_URL?>" target="_blank" rel="tooltip" title="Open the instance in a new window"><?= $base_version ?></a><?php } else { ?><?= $base_version ?><?php } ?></td>
            <?php if (empty($feature_branch)) { ?>
                <td class="col-center" colspan="4"></td>
            <?php } else { ?>
                <td class="col-center">
                    <?php
                    $acceptance_state_class = "";
                    if ($descriptor_array->ACCEPTANCE_STATE === "Implementing") {
                        $acceptance_state_class = "label-info";
                    } else if ($descriptor_array->ACCEPTANCE_STATE === "Engineering Review") {
                        $acceptance_state_class = "label-warning";
                    } else if ($descriptor_array->ACCEPTANCE_STATE === "QA Review") {
                        $acceptance_state_class = "label-inverse";
                    } else if ($descriptor_array->ACCEPTANCE_STATE === "QA In Progress") {
                        $acceptance_state_class = "label-warning";
                    } else if ($descriptor_array->ACCEPTANCE_STATE === "QA Rejected") {
                        $acceptance_state_class = "label-important";
                    } else if ($descriptor_array->ACCEPTANCE_STATE === "Validated") {
                        $acceptance_state_class = "label-success";
                    }
                    ?>
                    <span class="label <?=$acceptance_state_class?>"><?=$descriptor_array->ACCEPTANCE_STATE?></span></td>
                <td class="col-center"><?php if (!empty($descriptor_array->SCM_BRANCH)) { ?><a href="features.php#<?=str_replace(array("/", "."), "-", $descriptor_array->SCM_BRANCH)?>" rel="tooltip" title="SCM Branch used to host this FB development"><img src="images/fork_icon.png" alt="SCM Branch" title="SCM Branch" class="icon"/>&nbsp;<?= $descriptor_array->SCM_BRANCH ?><?php } ?></a></td>
                <td class="col-center"><?php if (!empty($descriptor_array->ISSUE_NUM)) { ?><a href="https://jira.exoplatform.org/browse/<?=$descriptor_array->ISSUE_NUM?>" target="_blank" rel="tooltip" title="Open the issue where to put your feedbacks on this new feature">&nbsp;<?= $descriptor_array->ISSUE_NUM ?></a><?php } ?></td>
                <td class="col-center"><a rel="tooltip" title="Edit feature branch details" href="#edit-<?=$descriptor_array->PRODUCT_NAME?>-<?=str_replace(".", "_", $descriptor_array->PRODUCT_VERSION)?>" data-toggle="modal"><i class="icon-pencil"></i></a></td>
            <?php } ?>
            <td class="col-center <?=$descriptor_array->ARTIFACT_AGE_CLASS?>"><?=$descriptor_array->ARTIFACT_AGE_STRING?></td>
            <td class="col-center"><?php if ($descriptor_array->DEPLOYMENT_ENABLED) {
                    echo $descriptor_array->DEPLOYMENT_AGE_STRING;
                } ?></td>
            <td class="col-center"><?php if ($descriptor_array->DEPLOYMENT_ENABLED) { ?><a href="<?=$descriptor_array->DEPLOYMENT_LOG_APPSRV_URL?>" rel="tooltip" title="Instance logs" target="_blank"><img src="/images/terminal_tomcat.png" width="32" height="16" alt="instance logs" class="left"/></a>&nbsp;<a href="<?=$descriptor_array->DEPLOYMENT_LOG_APACHE_URL?>" rel="tooltip" title="apache logs" target="_blank"><img src="/images/terminal_apache.png" width="32" height="16" alt="apache logs" class="right"/></a>&nbsp;<a href="<?=$descriptor_array->DEPLOYMENT_JMX_URL?>" rel="tooltip" title="jmx monitoring" target="_blank"><img src="/images/action_log.png" alt="JMX url" width="16" height="16" class="center"/></a>&nbsp;<a href="<?=$descriptor_array->DEPLOYMENT_AWSTATS_URL?>" rel="tooltip" title="<?=$descriptor_array->PRODUCT_NAME . " " . $descriptor_array->PRODUCT_VERSION?> usage statistics" target="_blank"><img src="/images/server_chart.png"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         alt="<?=$descriptor_array->DEPLOYMENT_URL?> usage statistics"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         width="16" height="16" class="center"/></a><?php } ?>
                &nbsp;<a rel="tooltip" title="Download <?=$descriptor_array->ARTIFACT_GROUPID?>:<?=$descriptor_array->ARTIFACT_ARTIFACTID?>:<?=$descriptor_array->ARTIFACT_TIMESTAMP?> from Acceptance" href="<?=$descriptor_array->ARTIFACT_DL_URL?>"><i class="icon-download-alt"></i></a></td>
        </tr>
        <?php if (!empty($feature_branch)) { ?>
            <form class="form" action="http://<?=$descriptor_array->ACCEPTANCE_SERVER?>/editFeature.php" method="POST">
                <div class="modal bigModal hide fade" id="edit-<?=$descriptor_array->PRODUCT_NAME?>-<?=str_replace(".", "_", $descriptor_array->PRODUCT_VERSION)?>" tabindex="-1" role="dialog" aria-labelledby="label-<?=$descriptor_array->PRODUCT_NAME?>-<?=$descriptor_array->PRODUCT_VERSION?>" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="label-<?=$descriptor_array->PRODUCT_NAME?>-<?=$descriptor_array->PRODUCT_VERSION?>">Edit Feature Branch</h3>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="from" value="<?=currentPageURL() ?>">
                        <input type="hidden" name="product" value="<?=$descriptor_array->PRODUCT_NAME?>">
                        <input type="hidden" name="version" value="<?=$descriptor_array->PRODUCT_VERSION?>">
                        <input type="hidden" name="server" value="<?=$descriptor_array->ACCEPTANCE_SERVER?>">

                        <div class="row-fluid">
                            <div class="span4"><strong>Product</strong></div>
                            <div class="span8"><?php if (empty($descriptor_array->PRODUCT_DESCRIPTION)) echo $descriptor_array->PRODUCT_NAME; else echo $descriptor_array->PRODUCT_DESCRIPTION;?></div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4"><strong>Version</strong></div>
                            <div class="span8"><?=$base_version?></div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4"><strong>Feature Branch</strong></div>
                            <div class="span8"><?=$feature_branch?></div>
                        </div>
                        <hr/>
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="control-group">
                                    <label class="control-label" for="specifications"><strong>Specifications link</strong></label>

                                    <div class="controls">
                                        <input class="input-xxlarge" type="url" id="specifications" name="specifications" placeholder="Url" value="<?=$descriptor_array->SPECIFICATIONS_LINK?>">
                                        <span class="help-block">eXo intranet URL of specifications</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4">
                                <div class="control-group">
                                    <label class="control-label" for="issue"><strong>Issue key</strong></label>

                                    <div class="controls">
                                        <input class="input-medium" type="text" id="issue" name="issue" placeholder="XXX-nnnn" value="<?=$descriptor_array->ISSUE_NUM?>">
                                        <span class="help-block">Issue key where testers can give a feedback.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="span4">
                                <div class="control-group">
                                    <label class="control-label" for="status"><strong>Status</strong></label>

                                    <div class="controls" id="status">
                                        <select name="status">
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "Implementing") {
                                                echo "selected";
                                            }?>>Implementing
                                            </option>
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "Engineering Review") {
                                                echo "selected";
                                            }?>>Engineering Review
                                            </option>
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "QA Review") {
                                                echo "selected";
                                            }?>>QA Review
                                            </option>
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "QA In Progress") {
                                                echo "selected";
                                            }?>>QA In Progress
                                            </option>
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "QA Rejected") {
                                                echo "selected";
                                            }?>>QA Rejected
                                            </option>
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "Validated") {
                                                echo "selected";
                                            }?>>Validated
                                            </option>
                                            <option <?php if ($descriptor_array->ACCEPTANCE_STATE === "Merged") {
                                                echo "selected";
                                            }?>>Merged
                                            </option>
                                        </select>
                                        <span class="help-block">Current status of the feature branch</span>
                                    </div>
                                </div>
                            </div>
                            <div class="span4">
                                <div class="control-group">
                                    <label class="control-label" for="branch"><strong>Git branch</strong></label>

                                    <div class="controls" id="branch">
                                        <select name="branch">
                                            <option value="UNSET">=== Undefined ===</option>
                                            <?php
                                            //List all projects
                                            $features = getFeatureBranches();
                                            foreach ($features as $feature => $FBProjects) {
                                                if ((!empty($descriptor_array->SCM_BRANCH) && $descriptor_array->SCM_BRANCH === $feature) || !in_array($feature, getFeatureBranches())) {
                                                    ?>
                                                    <option <?php if (!empty($descriptor_array->SCM_BRANCH) && $descriptor_array->SCM_BRANCH === $feature) {
                                                        echo "selected";
                                                    }?>><?=$feature?>
                                                    </option>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="help-block">Git branch hosting this development</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        <?php
        }
    }
    next($all_instances);
}
?>
</tbody>
</table>
<p>Each instance can be accessed using JMX with the URL linked to the monitoring icon and these credentials : <strong><code>acceptanceMonitor</code></strong> / <strong><code>monitorAcceptance!</code></strong></p>

<p><a href="/stats/awstats.pl?config=<?=$_SERVER['SERVER_NAME'] ?>" title="http://<?=$_SERVER['SERVER_NAME'] ?> usage statistics" target="_blank"><img src="/images/server_chart.png" alt="Statistics" width="16" height="16" class="left"/>http://<?=$_SERVER['SERVER_NAME'] ?> usage statistics</a></p>
</div>
</div>
</div>
<!-- /container -->
</div>
</div>
<!-- Footer ================================================== -->
<div id="footer">Copyright © 2000-2013. All rights Reserved, eXo Platform SAS.</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('body').tooltip({ selector: '[rel=tooltip]'});
        $('body').popover({ selector: '[rel=popover]'});
    });
</script>

</body>
</html>
