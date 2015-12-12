<?php if(!class_exists('Rain\RainTPL4')){exit;}?><h4>Interface Statistics</h4>
<b>Received Packets:</b> <?php print($info["received"]["packets"]);?><br />
<b>Received Bytes:</b> <?php print($info["received"]["bytes"]);?>b<br /><br />
<b>Transferred Packets:</b> <?php print($info["transmitted"]["packets"]);?><br />
<b>Transferred Bytes:</b> <?php print($info["transmitted"]["bytes"]);?>b<br />