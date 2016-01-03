{"js/ManagementDashboard/SSH.js"|addJS}
{include "Layout/Header.tpl"}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-plug fa-fw"></i> Web shell</div>
            <div class="panel-body">
                <div class="formInput" style="margin-top: 0px;">
                    <iframe src="{$address}" class="sshFrame">

                    </iframe>

                    <br/>
                    If the shell does not show up try to go to <a href="{$address}" target="blank">this link</a> first and accept the certificate.
                </div>
            </div><!-- /.panel-body -->
        </div><!-- /.panel-default -->
    </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
{include "Layout/Footer.tpl"}