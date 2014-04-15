<?php

    include(dirname(__FILE__)."/../../include/global.php");
    global $config;

    include_once($config['base_path'] . "/plugins/autom8/autom8_functions.php");
    include_once($config['base_path'] . "/plugins/autom8/autom8_utilities.php");

    $data = array();

    $snmp_queries = db_fetch_assoc("SELECT host.id, host.description,host.hostname FROM host host
                                ");

    foreach ($snmp_queries as $asnmp_queries) {
        if (sizeof($asnmp_queries) <> 0) {
         $data["host_id"] = $asnmp_queries["id"];
            echo $data["host_id"];
         execute_create_tree($data);
        }
    }

    function execute_create_tree($data) {

        autom8_log(__FUNCTION__ ." Host[" . $data["host_id"] . "]", true, "AUTOM8 TRACE", POLLER_VERBOSITY_MEDIUM);
        /* select all graph templates associated with this host, but exclude those where
        *  a graph already exists (table graph_local has a known entry for this host/template) */
        $sql = "SELECT " .
                            "graph_templates.id, " .
                            "graph_templates.name " .
                            "FROM (graph_templates,host_graph) " .
                            "WHERE graph_templates.id=host_graph.graph_template_id " .
                            "AND host_graph.host_id=" . $data["host_id"] . " " .
                            "AND graph_templates.id NOT IN (" .
                            "SELECT graph_local.graph_template_id FROM graph_local WHERE host_id=".$data["host_id"].")";
        $graph_templates = db_fetch_assoc($sql);
        autom8_log(__FUNCTION__ ." Host[" . $data["host_id"] . "], sql: " . $sql, true, "AUTOM8 TRACE", POLLER_VERBOSITY_MEDIUM);

        /* create all graph template graphs */
        foreach ($graph_templates as $graph_template) {
        $data["graph_template_id"] = $graph_template["id"];
        autom8_log(__FUNCTION__ ." Host[" . $data["host_id"] . "], graph: " . $data["graph_template_id"], true, "AUTOM8 TRACE", POLLER_VERBOSITY_MEDIUM);
        execute_graph_template($data);
        }

        unset($data["graph_template_id"]);
        /* all associated data queries */
        $data_queries = db_fetch_assoc("SELECT " .
                                                "snmp_query.id, " .
                                                "snmp_query.name, " .
                                                "host_snmp_query.reindex_method " .
                                                "FROM (snmp_query,host_snmp_query) " .
                                                "WHERE snmp_query.id=host_snmp_query.snmp_query_id " .
                                                "AND host_snmp_query.host_id=" . $data["host_id"]);

        /* create all data query graphs */
        foreach ($data_queries as $data_query) {
        $data["snmp_query_id"] = $data_query["id"];
        autom8_log(__FUNCTION__ ." Host[" . $data["host_id"] . "], dq: " . $data["snmp_query_id"], true, "AUTOM8 TRACE", POLLER_VERBOSITY_MEDIUM);
        execute_data_query($data);
                                }

        /* now handle tree rules for that host */
        autom8_log(__FUNCTION__ ." Host[" . $data["host_id"] . "], create_tree for host: " . $data["host_id"], true, "AUTOM8 TRACE", POLLER_VERBOSITY_MEDIUM);
        execute_device_create_tree(array("id" => $data["host_id"]));
}
