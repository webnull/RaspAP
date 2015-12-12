<?php if(!class_exists('Rain\RainTPL4')){exit;}?><?php echo $this->modifiers["addJS"]("js/ManagementDashboard/ManagePasswords.js");?>

<?php extract($this->variables); require $this->checkTemplate("Layout/Header.tpl", "/srv/http/raspap-webgui/raspap/.content/Packages/ManagementDashboard/Templates/Admin/ConfigureInterface/ManagePasswords.tpl", 2, 52);?>

<script type="text/javascript">currentInterfaceName = '<?php print($interface->getName());?>';</script>

<?php if(isset($redirect) && $redirect){?>

    <script>
        window.location.href = 'managePasswords,' + currentInterfaceName;
    </script>
<?php }?>


<?php if($statusMessage){?>

    <div class="alert alert-danger">
        <b>Save action failed:</b> <?php print($statusMessage);?>

    </div>
<?php }?>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-signal fa-fw"></i> <?php print($interface->getName());?></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Computer name</th>
                        <th>Mac Address</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=0;?>

                    <?php $counter1=-1; $newVar=$psk->get(); if(isset($newVar)&&(is_array($newVar)||$newVar instanceof Traversable)&& sizeof($newVar))foreach($newVar as $mac => $details){ $counter1++; ?>

                        <?php $i=$i+1;?>

                        <tr>
                            <td><form action="managePasswords,<?php print($interface->getName());?>?action=addMachine" method="POST"><?php print($i);?></td>
                            <td><input type="text" name="PSK_Title" value="<?php print($details["name"]);?>" style="width: 100%;"></td>
                            <td><input type="text" name="PSK_Mac" value="<?php print($mac);?>" data-origina-mac="<?php print($mac);?>" style="width: 100%;" onchange="PSKMacChanged(this);"></td>
                            <td><input type="text" name="PSK_Secr" value="<?php print($details["password"]);?>" style="width: 100%;"></td>
                            <td>
                                <button class="btn btn-primary saveButton" type="submit">Save</button>
                                <button class="btn btn-primary" type="button" onclick="removeMac(this);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php }?>


                    <tr>
                        <td><form action="managePasswords,<?php print($interface->getName());?>?action=addMachine" method="POST"></td>
                        <td><input type="text" name="PSK_Title" style="width: 100%;"></td>
                        <td><input type="text" name="PSK_Mac" style="width: 100%;"></td>
                        <td><input type="text" name="PSK_Secr" style="width: 100%;"></td>
                        <td><button class="btn btn-primary" type="submit">Save</button></form></td>
                    </tr>
                    </tbody>
                </table>

                <a href="configureInterface,<?php print($interface->getName());?>">
                    <button class="btn btn-primary" type="submit">Back to <?php print($interface->getName());?></button>
                </a>
            </div>
        </div>
    </div>
</div>