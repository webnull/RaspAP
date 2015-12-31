{include "Layout/Header.tpl"}
{include "Admin/ModalResponse.tpl"}

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-gears fa-fw"></i> Diagnostic</div>
            <div class="panel-body">
                <div class="row">
                    <div class="row interfaceForm">
                        {foreach from="$commands" as $title => $command}
                            <a href="diagnostic?command={$title}">
                                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="{$command}" data-original-title="{$command}" style="margin-bottom: 9px;">{$title}</button>
                            </a>
                        {/foreach}
                    </div>
                </div>

                {if $response}
                    <div class="col-lg-4" style="width: 100%; margin-top: 50px;">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                {$selectedCommand}
                            </div>
                            <div class="panel-body">
                                <pre>{$response}</pre>
                            </div>
                        </div>
                    </div>
                {/if}
            </div><!-- /.panel-default -->
        </div><!-- /.col-lg-12 -->
    </div><!-- /.row -->
{include "Layout/Footer.tpl"}