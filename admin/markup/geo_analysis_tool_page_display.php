<?php
if(!current_user_can('administrator')) return;
$self_rdr_base_link = admin_url("?page=wpcdd_ifso_geo_log_analyzer");
if(!empty($_GET['dl_log']) && (int) $_GET['dl_log']===1)
    $this->output_log_file();
$show_all = (isset($_GET['show_all']) && $_GET['show_all']);
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'suspicious';
$logProcessor = IfSo\Addons\Geolocation\Services\GeoRequestLogService::get_instance();

if($type==='suspicious'){
    $ips = $logProcessor->find_suspicious_ips($show_all);
    $row_names = ['IP','Issues','More Info'];
}
elseif($type==='ip_hist' && !empty($_GET['ip'])){
    $ip = sanitize_text_field($_GET['ip']);
    $ips = $logProcessor->find_ip_occurences($ip);
    $row_names = ['User Agent','Date','Status'];
}
$lines_processed = $logProcessor->get_last_processed_line();
?>
<style>
    .notice,#adminmenumain{
        display: none;
    }
    #wpcontent{
        margin:0!important;
    }
    #ifso-sus-ips-table{
        width: 100%;
    }
    #ifso-sus-ips-table td{
        border: 1px solid black;
        padding: 10px;
    }
    .description-section{
        color: gray;
        padding: 0px 30px 30px 0;
        max-width: 920px;
    }
    .error-msg{
        color:red;
    }
</style>
    <h3 class="page-title">Geolocation Sessions Log Analysis</h3>
    <div class="description-section">
        <p>
            The geolocation log analysis helps you identify the IPs of bots who visit your site and exhaust your session quota. Once you find these IPs, you can simply block them from the geolocation service (they will still be able to visit the site, but the geolocation service will not work for them).
        </p>
        <p>
            By default, the log includes IPs with more than 15 requests from the geolocation service. You can download the log or click the show all button to see the complete log.
        </p>
        <a href="https://www.if-so.com/faq-items/the-geolocation-session-count-doesnt-seem-to-behave-as-expected/?utm_source=Plugin&utm_medium=GeoLogAnalyzer&utm_campaign=geolocation_ext&utm_term=learnMore&utm_content=a" target="_blank">Learn more about how to identify IPs that belong to bots.</a>
    </div>
    <hr>
    <div>
        <h4 class="section-title">LOG ANALYSIS</h4>
        <p>Log lines processed: <?php echo esc_html($lines_processed); ?></p>
        <p><a href="<?php echo esc_url($self_rdr_base_link . "&dl_log=1"); ?>" target="_blank">Download log</a></p>
        <?php if($type!=='ip_hist'){ ?>
            <?php if(!$show_all) {?>
                <p>Showing IPs with  <?php echo esc_html($logProcessor->minimum_ip_occurrences_for_suspicion); ?> requests or more |
                <a href="<?php echo esc_url($self_rdr_base_link . "&show_all=1"); ?>">Show All</a>
        <?php }
             else{?>
                <p>Showing all logged IPs |
                <a href="<?php echo esc_url($self_rdr_base_link); ?>">Show only IPs with <?php echo esc_html($logProcessor->minimum_ip_occurrences_for_suspicion); ?> requests or more</a>
            <?php } ?>
            </p>
        <?php }else{ ?>
            <p>Showing occurences for IP : <b><?php echo esc_html($ip); ?></b></p>
            <a href="<?php echo esc_url($self_rdr_base_link);?>">Back to Log Analysis</a>
        <?php } ?>
    </div>
<?php
if(!empty($ips)){
    ?>
    <table id="ifso-sus-ips-table">
        <tr>
            <?php
                foreach ($row_names as $rowname)
                    echo "<th>".esc_html($rowname) ."</th>";
            ?>
        </tr>
        <?php
        if($type==='suspicious'){
            foreach ($ips as  $ip=>$occurences){
                echo "<tr>";
                echo "<td>" . esc_html($ip) ." (<a href='" . esc_url("{$self_rdr_base_link}&type=ip_hist&ip={$ip}") . "'>History</a>)</td>";
                echo "<td>". esc_html($occurences) ." occurences in the geo request log</td>";
                echo "<td><a target='_blank' href='" . esc_url("https://whatismyipaddress.com/ip/{$ip}") . "'>More About this IP</a></td>";
                echo "</tr>";
            }
        }
        elseif($type==='ip_hist'){
            foreach ($ips as  $ipdata){
                echo "<tr>";
                echo '<td>' . esc_html($ipdata['user-agent']) .'</td>';
                echo '<td>' . esc_html($ipdata['date']) .'</td>';
                echo '<td>' . esc_html($ipdata['status']) .'</td>';
                echo "</tr>";
            }
        }
        ?>
    </table>
    <?php
}
else{
    echo "<p class='error-msg'>The log file is empty or non-existant</p>";
}