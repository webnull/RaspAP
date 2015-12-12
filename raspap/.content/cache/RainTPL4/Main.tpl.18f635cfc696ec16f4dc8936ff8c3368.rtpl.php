<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php extract($this->variables); require $this->checkTemplate("Layout/Header.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/DHCPLeases/Main.tpl", 1, 0);?>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-exchange fa-fw"></i> Connected devices</div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>MAC</th>
                        <th>Name</th>
                        <th>DHCP Lease Started</th>
                        <th>DHCP Lease Ends</th>
                    </tr>
                    </thead>

                    <tbody>
                        <?php if(!$leases->getLeases()){?>

                            <tr>
                                <td colspan="4">
                                    No DHCP clients present
                                </td>
                            </tr>
                        <?php }?>


                        <?php $counter1=-1; $newVar=$leases->getLeases(); if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $address => $lease){ $counter1++; ?>

                            <tr>
                                <td>
                                    <?php print($lease["hardware"][2]);?>

                                </td>
                                <td>
                                    <?php print($address);?> <?php if(isset($lease["name"])){ print($lease["name"]); }?>

                                </td>
                                <td>
                                    <?php print($lease["starts_ipv4"]);?>

                                </td>
                                <td>
                                    <?php print($lease["ends_ipv4"]);?>

                                </td>
                            </tr>
                        <?php }?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php extract($this->variables); require $this->checkTemplate("Layout/Footer.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/DHCPLeases/Main.tpl", 49, 1793);?>