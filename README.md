Cacti Patch
===========
Patches will be listed here for bug and additions that has been discovered of Cacti.

1) create_graph_template_to_host.patch (for cacti version 0.8.8a)

   The patch adds a new action "Create Graph Templates for Host" when dealing with "Devices". 
   The main features is the addition of "singles" Graph Templates on one or more devices.

   Screenshot: http://shurshun.ru/cacti-macsovoe-dobavlenie-grafikov-iz-web-patch/

2) autom8_add_tree.php CLI for Apply Autom8 Rules to Device(s) (autom8)
   download in cacti/plugins/autom8/
   
   #cacti_clear_tree

   #!/bin/bash
   #  php add_tree.php --list-trees
   for y in {3,5,6,8,12,13,14,15,16}
   do
   for i in $( php -q /cacti/cli/add_tree.php --list-nodes --tree-id=${y}|awk '{print $2}'|egrep '[0-9]{1,10}'    ); do
      /usr/bin/php -q /cacti/cli/delete_tree.php --tree-id=$i
   done
   done

   php -q /cacti/plugins/autom8/autom8_add_tree.php
